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
            'secure' => false,
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
        ->toContain('SESSION_SECURE_COOKIE must be true in production.')
        ->toContain('MAIL_MAILER must be smtp in production.')
        ->toContain('Logging must use daily files in production (daily channel or a stack that includes daily).')
        ->toContain('LOG_LEVEL must be info or higher in production.')
        ->toContain('APP_URL must be an https URL and must not point to localhost or loopback in production.');
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
            'secure' => true,
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
    ], $overrides);
}
