<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\Incidents\Listeners\SendExternalNotificationOnIncidentEvent;
use App\Domain\Incidents\Events\IncidentAccountabilityChanged;
use App\Domain\Incidents\Events\IncidentCreated;
use App\Domain\Incidents\Events\IncidentStatusChanged;
use App\Models\ChecklistRun;
use App\Models\ChecklistTemplate;
use App\Models\Incident;
use App\Models\Room;
use App\Models\User;
use App\Policies\ChecklistRunPolicy;
use App\Policies\ChecklistTemplatePolicy;
use App\Policies\IncidentPolicy;
use App\Policies\RoomPolicy;
use App\Policies\UserPolicy;
use App\Support\ProductionEnvironmentContract;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
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
        $this->registerIncidentEventListeners();
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
        Gate::policy(ChecklistTemplate::class, ChecklistTemplatePolicy::class);
        Gate::policy(Room::class, RoomPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }

    /**
     * Register incident lifecycle event listeners for external notification delivery.
     *
     * Uses explicit registration rather than directory-based auto-discovery because
     * listeners in the Application layer follow a DDD layout outside of app/Listeners.
     */
    protected function registerIncidentEventListeners(): void
    {
        Event::listen(IncidentCreated::class, [SendExternalNotificationOnIncidentEvent::class, 'onCreated']);
        Event::listen(IncidentStatusChanged::class, [SendExternalNotificationOnIncidentEvent::class, 'onStatusChanged']);
        Event::listen(IncidentAccountabilityChanged::class, [SendExternalNotificationOnIncidentEvent::class, 'onAccountabilityChanged']);
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
