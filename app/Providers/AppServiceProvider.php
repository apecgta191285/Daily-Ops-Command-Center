<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\ChecklistRun;
use App\Models\Incident;
use App\Policies\ChecklistRunPolicy;
use App\Policies\IncidentPolicy;
use App\Support\ProductionEnvironmentContract;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerPolicies();
        $this->assertProductionEnvironmentContract();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Model::shouldBeStrict(! app()->isProduction());

        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    /**
     * Register application policy mappings explicitly so object-level authorization does not rely only on routes.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(Incident::class, IncidentPolicy::class);
        Gate::policy(ChecklistRun::class, ChecklistRunPolicy::class);
    }

    /**
     * Fail fast when production runtime assumptions drift from the supported baseline.
     */
    protected function assertProductionEnvironmentContract(): void
    {
        if (! app()->isProduction()) {
            return;
        }

        app(ProductionEnvironmentContract::class)->assertSatisfied(config()->all());
    }
}
