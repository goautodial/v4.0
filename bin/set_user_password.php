<?php
/**
 * Set modern bcrypt password hashes for vicidial_users.
 *
 * Usage:
 *   php bin/set_user_password.php --user=goadmin --password='new-password'
 *   php bin/set_user_password.php --user=goadmin --password-env=NEW_PASSWORD
 *   php bin/set_user_password.php --csv=/path/to/users.csv
 *
 * CSV format: user,password
 */

function loadAsteriskDbConfig(): array
{
    $configPath = __DIR__ . '/../astguiclient.conf';
    if (!is_readable($configPath)) {
        throw new RuntimeException("Unable to read {$configPath}");
    }

    $config = [];
    foreach (file($configPath) as $line) {
        $line = preg_replace('/\s|#.*|;.*/', '', (string) $line);
        if (!str_contains((string) $line, '=>')) {
            continue;
        }
        [$key, $value] = explode('=>', (string) $line, 2);
        $config[$key] = $value;
    }

    foreach (['VARDB_server', 'VARDB_user', 'VARDB_pass', 'VARDB_database'] as $key) {
        if (!array_key_exists($key, $config)) {
            throw new RuntimeException("Missing {$key} in {$configPath}");
        }
    }

    return [
        'host' => $config['VARDB_server'],
        'user' => $config['VARDB_user'],
        'pass' => $config['VARDB_pass'],
        'db' => $config['VARDB_database'],
        'port' => (int) ($config['VARDB_port'] ?? 3306),
    ];
}

function usage(int $exitCode = 0): void
{
    $message = "Usage:\n"
        . "  php bin/set_user_password.php --user=<user> --password=<password>\n"
        . "  php bin/set_user_password.php --user=<user> --password-env=<ENV_NAME>\n"
        . "  php bin/set_user_password.php --csv=<csv-file>\n"
        . "  php bin/set_user_password.php --status\n"
        . "  php bin/set_user_password.php --enable-hashing\n\n"
        . "CSV format:\n"
        . "  user,password\n";
    fwrite($exitCode === 0 ? STDOUT : STDERR, $message);
    exit($exitCode);
}

$options = getopt('', ['user:', 'password:', 'password-env:', 'csv:', 'status', 'enable-hashing', 'help']);
if (isset($options['help'])) {
    usage(0);
}

try {
    $dbConfig = loadAsteriskDbConfig();
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}

$mysqli = new mysqli($dbConfig['host'], $dbConfig['user'], $dbConfig['pass'], $dbConfig['db'], $dbConfig['port']);
if ($mysqli->connect_errno) {
    fwrite(STDERR, "Unable to connect to asterisk database {$dbConfig['db']} at {$dbConfig['host']}:{$dbConfig['port']}: {$mysqli->connect_error}\n");
    exit(1);
}

$mysqli->set_charset('utf8mb4');
echo "Connected to {$dbConfig['db']} at {$dbConfig['host']}:{$dbConfig['port']} as {$dbConfig['user']}.\n";

function ensurePasswordHashColumn(mysqli $mysqli): void
{
    $result = $mysqli->query("SHOW COLUMNS FROM vicidial_users LIKE 'pass_hash'");
    $column = $result ? $result->fetch_assoc() : null;

    if (!$column) {
        if (!$mysqli->query("ALTER TABLE vicidial_users ADD COLUMN pass_hash VARCHAR(255) NOT NULL DEFAULT ''")) {
            throw new RuntimeException("Unable to add pass_hash column: {$mysqli->error}");
        }
        return;
    }

    $type = strtolower((string) $column['Type']);
    if (preg_match('/varchar\((\d+)\)/', $type, $matches) && (int) $matches[1] < 255) {
        if (!$mysqli->query("ALTER TABLE vicidial_users MODIFY pass_hash VARCHAR(255) NOT NULL DEFAULT ''")) {
            throw new RuntimeException("Unable to resize pass_hash column: {$mysqli->error}");
        }
    }
}

function enablePasswordHashing(mysqli $mysqli): void
{
    if (!$mysqli->query("UPDATE system_settings SET pass_hash_enabled = '1'")) {
        throw new RuntimeException("Unable to enable pass_hash_enabled: {$mysqli->error}");
    }

    $result = $mysqli->query("SELECT pass_hash_enabled, pass_cost, pass_key FROM system_settings LIMIT 1");
    $settings = $result ? $result->fetch_assoc() : null;
    if (!$settings) {
        throw new RuntimeException("Unable to verify system_settings update: {$mysqli->error}");
    }

    if ((string) $settings['pass_hash_enabled'] !== '1') {
        throw new RuntimeException("pass_hash_enabled is still {$settings['pass_hash_enabled']} after update");
    }

    echo "Verified system_settings.pass_hash_enabled=1.\n";
}

function getPasswordHashStatus(mysqli $mysqli): array
{
    $result = $mysqli->query("SELECT pass_hash_enabled, pass_cost, pass_key FROM system_settings LIMIT 1");
    $settings = $result ? $result->fetch_assoc() : null;
    if (!$settings) {
        throw new RuntimeException("Unable to read system_settings: {$mysqli->error}");
    }

    return $settings;
}

function setUserPassword(mysqli $mysqli, string $user, string $password): bool
{
    if ($user === '' || $password === '') {
        throw new InvalidArgumentException('User and password must not be empty.');
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    if ($hash === false) {
        throw new RuntimeException("Unable to generate password hash for user {$user}");
    }

    $stmt = $mysqli->prepare("UPDATE vicidial_users SET pass = '', pass_hash = ? WHERE user = ?");
    if (!$stmt) {
        throw new RuntimeException("Unable to prepare update: {$mysqli->error}");
    }

    $stmt->bind_param('ss', $hash, $user);
    if (!$stmt->execute()) {
        throw new RuntimeException("Unable to update user {$user}: {$stmt->error}");
    }

    return $stmt->affected_rows > 0;
}

try {
    if (isset($options['status'])) {
        $settings = getPasswordHashStatus($mysqli);
        echo "system_settings: " . json_encode($settings) . "\n";
        exit(0);
    }

    if (isset($options['enable-hashing'])) {
        ensurePasswordHashColumn($mysqli);
        enablePasswordHashing($mysqli);
        exit(0);
    }

    ensurePasswordHashColumn($mysqli);
    enablePasswordHashing($mysqli);

    if (isset($options['csv'])) {
        $csvPath = (string) $options['csv'];
        if (!is_readable($csvPath)) {
            throw new RuntimeException("CSV file is not readable: {$csvPath}");
        }

        $handle = fopen($csvPath, 'r');
        if (!$handle) {
            throw new RuntimeException("Unable to open CSV file: {$csvPath}");
        }

        $updated = 0;
        $missing = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 2) {
                continue;
            }
            [$user, $password] = $row;
            if ($user === 'user' && $password === 'password') {
                continue;
            }
            if (setUserPassword($mysqli, trim((string) $user), (string) $password)) {
                $updated++;
            } else {
                $missing++;
                fwrite(STDERR, "No matching user updated: {$user}\n");
            }
        }
        fclose($handle);
        echo "Updated {$updated} users. Missing/unchanged: {$missing}.\n";
        exit(0);
    }

    if (!isset($options['user'])) {
        usage(1);
    }

    $password = $options['password'] ?? null;
    if ($password === null && isset($options['password-env'])) {
        $envValue = getenv((string) $options['password-env']);
        $password = $envValue === false ? null : $envValue;
    }

    if ($password === null) {
        usage(1);
    }

    $updated = setUserPassword($mysqli, (string) $options['user'], (string) $password);
    if (!$updated) {
        fwrite(STDERR, "No matching user updated: {$options['user']}\n");
        exit(1);
    }

    echo "Password updated for user {$options['user']}.\n";
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}
