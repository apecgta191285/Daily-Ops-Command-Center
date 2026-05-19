<?php

use App\Support\ProductionEnvironmentContract;

test('production environment contract passes for the supported baseline', function () {
    $contract = new ProductionEnvironmentContract;

    expect($contract->violations(productionContractConfig()))->toBe([]);
});

test('production environment contract reports baseline violations clearly', function () {
    $contract = new ProductionEnvironmentContract;

    $violations = $contract->violations(productionContractConfig([
        'app' => [
            'debug' => true,
            'url' => 'http://localhost:8000',
        ],
        'database' => [
            'default' => 'sqlite',
        ],
        'queue' => [
            'default' => 'redis',
        ],
        'cache' => [
            'default' => 'redis',
        ],
        'session' => [
            'driver' => 'file',
            'encrypt' => false,
            'expire_on_close' => false,
            'secure' => false,
        ],
        'auth' => [
            'password_timeout' => 10800,
        ],
        'mail' => [
            'default' => 'log',
        ],
        'logging' => [
            'default' => 'stack',
            'channels' => [
                'stack' => ['channels' => ['single']],
                'daily' => ['level' => 'debug'],
            ],
        ],
    ]));

    expect($violations)->toContain('APP_DEBUG must be false in production.')
        ->toContain('DB_CONNECTION must be mysql in production.')
        ->toContain('QUEUE_CONNECTION must be database in production.')
        ->toContain('CACHE_STORE must be database in production.')
        ->toContain('SESSION_DRIVER must be database in production.')
        ->toContain('SESSION_ENCRYPT must be true in production.')
        ->toContain('SESSION_EXPIRE_ON_CLOSE must be true in production for shared lab workstations.')
        ->toContain('SESSION_SECURE_COOKIE must be true in production.')
        ->toContain('AUTH_PASSWORD_TIMEOUT must be 900 seconds or less in production.')
        ->toContain('MAIL_MAILER must be smtp in production.')
        ->toContain('Logging must use daily files in production (daily channel or a stack that includes daily).')
        ->toContain('LOG_LEVEL must be info or higher in production.')
        ->toContain('APP_URL must be an https URL and must not point to localhost or loopback in production.');
});

test('production environment contract validates enabled LINE notification configuration', function () {
    $contract = new ProductionEnvironmentContract;

    $violations = $contract->violations(productionContractConfig([
        'services' => [
            'line' => [
                'notifications' => [
                    'enabled' => true,
                    'channel_access_token' => '',
                    'to' => 'invalid-recipient',
                    'timeout' => 30,
                ],
            ],
        ],
    ]));

    expect($violations)->toContain('LINE_CHANNEL_ACCESS_TOKEN must be set when LINE notifications are enabled.')
        ->toContain('At least one LINE notification recipient must be configured when LINE notifications are enabled.')
        ->toContain('LINE notification recipients must be LINE user, group, or room ids.')
        ->toContain('LINE_NOTIFICATION_TIMEOUT must be between 1 and 15 seconds.');
});

test('production environment contract accepts role-specific LINE recipients without a default recipient', function () {
    $contract = new ProductionEnvironmentContract;

    $violations = $contract->violations(productionContractConfig([
        'services' => [
            'line' => [
                'notifications' => [
                    'enabled' => true,
                    'channel_access_token' => 'line-token',
                    'to' => null,
                    'admin_to' => 'Cadmin123',
                    'supervisor_to' => 'Csupervisor123,Uuser456',
                    'staff_to' => 'Rstaff123',
                    'timeout' => 5,
                ],
            ],
        ],
    ]));

    expect($violations)->toBe([]);
});

test('production environment contract throws with a readable summary when violated', function () {
    $contract = new ProductionEnvironmentContract;

    expect(fn () => $contract->assertSatisfied(productionContractConfig([
        'app' => ['debug' => true],
    ])))->toThrow(
        RuntimeException::class,
        "Production environment contract violations:\n- APP_DEBUG must be false in production.",
    );
});

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function productionContractConfig(array $overrides = []): array
{
    return array_replace_recursive([
        'app' => [
            'debug' => false,
            'url' => 'https://daily-ops.example.edu',
        ],
        'database' => [
            'default' => 'mysql',
        ],
        'queue' => [
            'default' => 'database',
        ],
        'cache' => [
            'default' => 'database',
        ],
        'session' => [
            'driver' => 'database',
            'encrypt' => true,
            'expire_on_close' => true,
            'secure' => true,
        ],
        'auth' => [
            'password_timeout' => 900,
        ],
        'mail' => [
            'default' => 'smtp',
        ],
        'logging' => [
            'default' => 'stack',
            'channels' => [
                'stack' => ['channels' => ['daily']],
                'daily' => ['level' => 'info'],
            ],
        ],
        'services' => [
            'line' => [
                'notifications' => [
                    'enabled' => false,
                    'channel_access_token' => null,
                    'to' => null,
                    'admin_to' => null,
                    'supervisor_to' => null,
                    'staff_to' => null,
                    'timeout' => 5,
                ],
            ],
        ],
    ], $overrides);
}
