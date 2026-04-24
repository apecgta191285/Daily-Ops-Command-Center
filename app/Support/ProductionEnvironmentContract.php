<?php

declare(strict_types=1);

namespace App\Support;

use RuntimeException;

class ProductionEnvironmentContract
{
    /**
     * Assert that the production runtime contract is satisfied.
     *
     * @param  array<string, mixed>  $config
     */
    public function assertSatisfied(array $config): void
    {
        $violations = $this->violations($config);

        if ($violations === []) {
            return;
        }

        throw new RuntimeException(
            "Production environment contract violations:\n- ".implode("\n- ", $violations),
        );
    }

    /**
     * Collect production contract violations from the current configuration.
     *
     * @param  array<string, mixed>  $config
     * @return list<string>
     */
    public function violations(array $config): array
    {
        $violations = [];

        if ($this->read($config, 'app.debug') !== false) {
            $violations[] = 'APP_DEBUG must be false in production.';
        }

        if ($this->read($config, 'database.default') !== 'mysql') {
            $violations[] = 'DB_CONNECTION must be mysql in production.';
        }

        if ($this->read($config, 'queue.default') !== 'database') {
            $violations[] = 'QUEUE_CONNECTION must be database in production.';
        }

        if ($this->read($config, 'cache.default') !== 'database') {
            $violations[] = 'CACHE_STORE must be database in production.';
        }

        if ($this->read($config, 'session.driver') !== 'database') {
            $violations[] = 'SESSION_DRIVER must be database in production.';
        }

        if ($this->read($config, 'session.secure') !== true) {
            $violations[] = 'SESSION_SECURE_COOKIE must be true in production.';
        }

        if ($this->read($config, 'mail.default') !== 'smtp') {
            $violations[] = 'MAIL_MAILER must be smtp in production.';
        }

        if (! $this->usesDailyLogging($config)) {
            $violations[] = 'Logging must use daily files in production (daily channel or a stack that includes daily).';
        }

        if (! $this->usesProductionSafeLogLevel($config)) {
            $violations[] = 'LOG_LEVEL must be info or higher in production.';
        }

        if (! $this->hasValidProductionUrl($config)) {
            $violations[] = 'APP_URL must be an https URL and must not point to localhost or loopback in production.';
        }

        return $violations;
    }

    /**
     * @param  array<string, mixed>  $config
     */
    protected function usesDailyLogging(array $config): bool
    {
        $defaultChannel = $this->read($config, 'logging.default');

        if ($defaultChannel === 'daily') {
            return true;
        }

        if ($defaultChannel !== 'stack') {
            return false;
        }

        $stackChannels = $this->read($config, 'logging.channels.stack.channels', []);

        return is_array($stackChannels) && in_array('daily', $stackChannels, true);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    protected function usesProductionSafeLogLevel(array $config): bool
    {
        $dailyLevel = $this->read($config, 'logging.channels.daily.level');

        return is_string($dailyLevel)
            && in_array(strtolower($dailyLevel), [
                'info',
                'notice',
                'warning',
                'error',
                'critical',
                'alert',
                'emergency',
            ], true);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    protected function hasValidProductionUrl(array $config): bool
    {
        $appUrl = $this->read($config, 'app.url');

        if (! is_string($appUrl) || $appUrl === '') {
            return false;
        }

        $scheme = parse_url($appUrl, PHP_URL_SCHEME);
        $host = parse_url($appUrl, PHP_URL_HOST);

        if (! is_string($scheme) || strtolower($scheme) !== 'https') {
            return false;
        }

        if (! is_string($host) || $host === '') {
            return false;
        }

        return ! in_array(strtolower($host), ['localhost', '127.0.0.1', '::1'], true);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    protected function read(array $config, string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $value = $config;

        foreach ($segments as $segment) {
            if (! is_array($value) || ! array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }
}
