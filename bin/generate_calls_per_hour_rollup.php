<?php
/**
 * Generate completed hourly Calls Per Hour dashboard rollups.
 *
 * The generator writes one aggregate row per user group. ADMIN stores
 * unfiltered totals; other rows use the campaign and inbound-group filters
 * that the existing goGetCallsPerHour dashboard API applies.
 *
 * Usage:
 *   php bin/generate_calls_per_hour_rollup.php
 *   php bin/generate_calls_per_hour_rollup.php --date=2026-08-18 --hour=10
 *   php bin/generate_calls_per_hour_rollup.php --scope=ADMIN
 *
 * Cron example (run shortly after each hour begins):
 *   5 * * * * /usr/bin/php /var/www/html/bin/generate_calls_per_hour_rollup.php
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("This script must be run from the command line.\n");
}

require_once __DIR__ . '/../php/Config.php';

const ROLLUP_TABLE = 'go_dashboard_calls_per_hour';
const ROLLUP_LOCK_NAME = 'dashboard_calls_per_hour_rollup';

function usage(int $exitCode = 0): never
{
    $message = "Usage:\n"
        . "  php bin/generate_calls_per_hour_rollup.php\n"
        . "  php bin/generate_calls_per_hour_rollup.php --date=YYYY-MM-DD --hour=0-23\n"
        . "  php bin/generate_calls_per_hour_rollup.php --scope=<user-group>\n";

    fwrite($exitCode === 0 ? STDOUT : STDERR, $message);
    exit($exitCode);
}

function connectToDatabase(string $databaseNameConstant): mysqli
{
    foreach (['DB_HOST', 'DB_USERNAME', 'DB_PASSWORD', $databaseNameConstant, 'DB_PORT'] as $constant) {
        if (!defined($constant)) {
            throw new RuntimeException("Missing {$constant} database configuration.");
        }
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $database = new mysqli(
        (string) DB_HOST,
        (string) DB_USERNAME,
        (string) DB_PASSWORD,
        (string) constant($databaseNameConstant),
        (int) DB_PORT
    );
    $database->set_charset('utf8mb4');

    return $database;
}

function connectToAsteriskDatabase(): mysqli
{
    return connectToDatabase('DB_NAME_ASTERISK');
}

function connectToGoAutoDialDatabase(): mysqli
{
    return connectToDatabase('DB_NAME');
}

function bindParameters(mysqli_stmt $statement, array $parameters): void
{
    if ($parameters === []) {
        return;
    }

    $types = str_repeat('s', count($parameters));
    $bindings = [$types];

    foreach ($parameters as $index => $parameter) {
        $bindings[] = &$parameters[$index];
    }

    call_user_func_array([$statement, 'bind_param'], $bindings);
}

function fetchCount(mysqli $database, string $query, array $parameters): int
{
    $statement = $database->prepare($query);
    bindParameters($statement, $parameters);
    $statement->execute();
    $statement->bind_result($count);
    $statement->fetch();
    $statement->close();

    return (int) $count;
}

function fetchFirstColumn(mysqli $database, string $query, array $parameters = []): array
{
    $statement = $database->prepare($query);
    bindParameters($statement, $parameters);
    $statement->execute();
    $statement->bind_result($value);

    $values = [];
    while ($statement->fetch()) {
        $values[] = (string) $value;
    }

    $statement->close();

    return $values;
}

function fetchReportingScopes(mysqli $database, ?string $requestedScope): array
{
    if ($requestedScope !== null) {
        return [$requestedScope];
    }

    $scopes = ['ADMIN'];
    foreach (fetchFirstColumn($database, "SELECT DISTINCT user_group FROM vicidial_user_groups WHERE user_group <> ''") as $userGroup) {
        if (strtoupper($userGroup) !== 'ADMIN') {
            $scopes[] = $userGroup;
        }
    }

    return $scopes;
}

function fetchScopeFilters(mysqli $database, string $scope): array
{
    if (strtoupper($scope) === 'ADMIN') {
        return [[], []];
    }

    $campaignIds = fetchFirstColumn(
        $database,
        'SELECT campaign_id FROM vicidial_campaigns WHERE user_group = ?',
        [$scope]
    );
    $inboundGroupIds = fetchFirstColumn(
        $database,
        'SELECT group_id FROM vicidial_inbound_groups WHERE user_group = ?',
        [$scope]
    );

    return [$campaignIds, $inboundGroupIds];
}

function buildScopeFilter(string $column, array $values, array &$parameters): string
{
    if ($values === []) {
        return '';
    }

    $placeholders = implode(', ', array_fill(0, count($values), '?'));
    foreach ($values as $value) {
        $parameters[] = $value;
    }

    return " AND {$column} IN ({$placeholders})";
}

function calculateScopeTotals(
    mysqli $database,
    string $hourStart,
    string $hourEnd,
    array $campaignIds,
    array $inboundGroupIds
): array {
    $inboundParameters = [$hourStart, $hourEnd];
    $inboundFilter = buildScopeFilter('campaign_id', $inboundGroupIds, $inboundParameters);
    $inboundCalls = fetchCount(
        $database,
        "SELECT COUNT(*) FROM vicidial_closer_log WHERE call_date >= ? AND call_date < ?{$inboundFilter}",
        $inboundParameters
    );

    $outboundParameters = [$hourStart, $hourEnd];
    $outboundFilter = buildScopeFilter('campaign_id', $campaignIds, $outboundParameters);
    $statement = $database->prepare(
        "SELECT COUNT(*) AS outbound_calls,
                COALESCE(SUM(status IN ('DROP', 'IVRXFR')), 0) AS dropped_calls
         FROM vicidial_log
         WHERE call_date >= ? AND call_date < ?{$outboundFilter}"
    );
    bindParameters($statement, $outboundParameters);
    $statement->execute();
    $statement->bind_result($outboundCalls, $droppedCalls);
    $statement->fetch();
    $statement->close();

    return [
        'inboundCalls' => $inboundCalls,
        'outboundCalls' => (int) $outboundCalls,
        'droppedCalls' => (int) $droppedCalls,
    ];
}

function purgePreviousDayRollups(mysqli $database, string $reportDate): int
{
    $statement = $database->prepare(
        'DELETE FROM ' . ROLLUP_TABLE . ' WHERE `date` < ?'
    );
    $statement->bind_param('s', $reportDate);
    $statement->execute();
    $deletedRowCount = $statement->affected_rows;
    $statement->close();

    return $deletedRowCount;
}

function writeRollup(
    mysqli $database,
    string $reportDate,
    string $userGroup,
    int $hour,
    array $totals
): void {
    $statement = $database->prepare(
        'INSERT INTO ' . ROLLUP_TABLE . "
            (`date`, user_group, hour_of_day, inbound_calls, outbound_calls, dropped_calls, generated_at)
         VALUES (?, ?, ?, ?, ?, ?, NOW())
         ON DUPLICATE KEY UPDATE
            inbound_calls = VALUES(inbound_calls),
            outbound_calls = VALUES(outbound_calls),
            dropped_calls = VALUES(dropped_calls),
            generated_at = VALUES(generated_at)"
    );
    $statement->bind_param(
        'ssiiii',
        $reportDate,
        $userGroup,
        $hour,
        $totals['inboundCalls'],
        $totals['outboundCalls'],
        $totals['droppedCalls']
    );
    $statement->execute();
    $statement->close();
}

function resolveHour(array $options): array
{
    $hasDate = isset($options['date']);
    $hasHour = isset($options['hour']);

    if ($hasDate !== $hasHour) {
        throw new InvalidArgumentException('The --date and --hour options must be used together.');
    }

    if ($hasDate && $hasHour) {
        $reportDate = (string) $options['date'];
        $hour = filter_var($options['hour'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 23]]);
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $reportDate);

        if ($date === false || $date->format('Y-m-d') !== $reportDate || $hour === false) {
            throw new InvalidArgumentException('Use --date=YYYY-MM-DD and --hour=0-23.');
        }

        $hourStart = $date->setTime((int) $hour, 0);
    } else {
        $hourStart = (new DateTimeImmutable('now'))->setTime((int) date('H'), 0)->sub(new DateInterval('PT1H'));
    }

    return [
        $hourStart->format('Y-m-d'),
        (int) $hourStart->format('G'),
        $hourStart->format('Y-m-d H:i:s'),
        $hourStart->add(new DateInterval('PT1H'))->format('Y-m-d H:i:s'),
    ];
}

try {
    $options = getopt('', ['date:', 'hour:', 'scope:', 'help']);
    if (isset($options['help'])) {
        usage();
    }

    $requestedScope = isset($options['scope']) ? trim((string) $options['scope']) : null;
    if ($requestedScope === '') {
        throw new InvalidArgumentException('The --scope value must not be empty.');
    }

    $isAutomaticRun = !isset($options['date']) && !isset($options['hour']);
    [$reportDate, $hour, $hourStart, $hourEnd] = resolveHour($options);
    $asteriskDatabase = connectToAsteriskDatabase();
    $goAutoDialDatabase = connectToGoAutoDialDatabase();

    $lockResult = $goAutoDialDatabase->query("SELECT GET_LOCK('" . ROLLUP_LOCK_NAME . "', 0) AS acquired");
    $lock = $lockResult->fetch_assoc();
    if ((int) ($lock['acquired'] ?? 0) !== 1) {
        throw new RuntimeException('Another Calls Per Hour rollup is already running.');
    }

    try {
        $tableExists = $goAutoDialDatabase->query("SHOW TABLES LIKE '" . ROLLUP_TABLE . "'");
        if ($tableExists->num_rows !== 1) {
            throw new RuntimeException('Rollup table is missing. Apply sql/dashboard_calls_per_hour_rollup.sql first.');
        }

        if ($isAutomaticRun && $hour === 0) {
            $deletedRowCount = purgePreviousDayRollups($goAutoDialDatabase, $reportDate);
            printf("Removed %d expired Calls Per Hour rollup row(s).\n", $deletedRowCount);
        }

        $scopeCount = 0;
        foreach (fetchReportingScopes($asteriskDatabase, $requestedScope) as $scope) {
            [$campaignIds, $inboundGroupIds] = fetchScopeFilters($asteriskDatabase, $scope);
            $totals = calculateScopeTotals($asteriskDatabase, $hourStart, $hourEnd, $campaignIds, $inboundGroupIds);
            writeRollup($goAutoDialDatabase, $reportDate, $scope, $hour, $totals);

            printf(
                "%s hour %02d scope %s: inbound=%d outbound=%d dropped=%d\n",
                $reportDate,
                $hour,
                $scope,
                $totals['inboundCalls'],
                $totals['outboundCalls'],
                $totals['droppedCalls']
            );
            $scopeCount++;
        }

        printf("Generated %d Calls Per Hour rollup scope(s).\n", $scopeCount);
    } finally {
        $goAutoDialDatabase->query("SELECT RELEASE_LOCK('" . ROLLUP_LOCK_NAME . "')");
        $goAutoDialDatabase->close();
        $asteriskDatabase->close();
    }
} catch (Throwable $exception) {
    fwrite(STDERR, 'Calls Per Hour rollup failed: ' . $exception->getMessage() . "\n");
    exit(1);
}
