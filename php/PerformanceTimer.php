<?php

namespace creamy;

/**
 * Lightweight request timing helper for temporary performance investigation.
 *
 * Disabled by default. Enable with ?perf=1 and disable with ?perf=0.
 */
class PerformanceTimer
{
    private const COOKIE_NAME = 'GO_PERF';
    private const COOKIE_TTL_SECONDS = 3600;

    private static ?bool $enabled = null;
    private static bool $shutdownRegistered = false;
    private static float $requestStart = 0.0;
    private static array $timings = [];

    public static function begin(): float
    {
        if (!self::isEnabled()) {
            return 0.0;
        }

        return microtime(true);
    }

    public static function end(string $label, float $startedAt): void
    {
        if ($startedAt <= 0.0 || !self::isEnabled()) {
            return;
        }

        $elapsedMs = (microtime(true) - $startedAt) * 1000;

        if (!isset(self::$timings[$label])) {
            self::$timings[$label] = [
                'count' => 0,
                'total_ms' => 0.0,
                'max_ms' => 0.0,
            ];
        }

        self::$timings[$label]['count']++;
        self::$timings[$label]['total_ms'] += $elapsedMs;
        self::$timings[$label]['max_ms'] = max(self::$timings[$label]['max_ms'], $elapsedMs);
    }

    /**
     * @param array<string, mixed> $context
     */
    public static function logSlow(string $label, float $startedAt, array $context = [], float $thresholdMs = 500.0): void
    {
        if ($startedAt <= 0.0 || !self::isEnabled()) {
            return;
        }

        $elapsedMs = (microtime(true) - $startedAt) * 1000;
        if ($elapsedMs < $thresholdMs) {
            return;
        }

        $contextParts = [];
        foreach ($context as $key => $value) {
            if (is_scalar($value) || $value === null) {
                $contextParts[] = $key . '=' . (string) $value;
            }
        }

        error_log(sprintf(
            '[perf-slow] uri=%s label=%s ms=%.2f %s',
            self::requestUri(),
            $label,
            $elapsedMs,
            implode(' ', $contextParts)
        ));
    }

    public static function isEnabled(): bool
    {
        if (self::$enabled !== null) {
            return self::$enabled;
        }

        self::$requestStart = isset($_SERVER['REQUEST_TIME_FLOAT'])
            ? (float) $_SERVER['REQUEST_TIME_FLOAT']
            : microtime(true);

        $requestedMode = $_GET['perf'] ?? null;
        if ($requestedMode === '1') {
            self::$enabled = true;
            self::setCookie('1');
        } elseif ($requestedMode === '0') {
            self::$enabled = false;
            self::setCookie('', true);
        } else {
            self::$enabled = ($_COOKIE[self::COOKIE_NAME] ?? '') === '1';
        }

        if (self::$enabled && !self::$shutdownRegistered) {
            register_shutdown_function([self::class, 'logSummary']);
            self::$shutdownRegistered = true;
        }

        return self::$enabled;
    }

    public static function logSummary(): void
    {
        if (!self::$enabled) {
            return;
        }

        $totalMs = (microtime(true) - self::$requestStart) * 1000;
        $parts = [
            'uri=' . self::requestUri(),
            'total_ms=' . number_format($totalMs, 2, '.', ''),
            'peak_kb=' . (int) round(memory_get_peak_usage(true) / 1024),
        ];

        ksort(self::$timings);
        foreach (self::$timings as $label => $timing) {
            $parts[] = sprintf(
                '%s_count=%d %s_total_ms=%.2f %s_max_ms=%.2f',
                $label,
                $timing['count'],
                $label,
                $timing['total_ms'],
                $label,
                $timing['max_ms']
            );
        }

        error_log('[perf] ' . implode(' ', $parts));
    }

    private static function setCookie(string $value, bool $expire = false): void
    {
        if (headers_sent()) {
            return;
        }

        $expires = $expire ? time() - 3600 : time() + self::COOKIE_TTL_SECONDS;
        setcookie(self::COOKIE_NAME, $value, $expires, '/');
        $_COOKIE[self::COOKIE_NAME] = $value;
    }

    private static function requestUri(): string
    {
        return $_SERVER['REQUEST_URI'] ?? 'unknown';
    }
}
