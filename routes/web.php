<?php

declare(strict_types=1);

use App\Domain\Access\Enums\UserRole;
use App\Http\Controllers\Management\DashboardController;
use App\Livewire\Admin\ChecklistTemplates\Index as TemplateIndex;
use App\Livewire\Admin\ChecklistTemplates\Manage as TemplateManage;
use App\Livewire\Management\Incidents\Index;
use App\Livewire\Management\Incidents\Show;
use App\Livewire\Staff\Checklists\DailyRun;
use App\Livewire\Staff\Incidents\Create;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route(auth()->user()->landingRouteName());
    }

    return view('welcome');
})->name('home');

Route::middleware(['auth', 'active'])->group(function () {

    // ── Staff-only routes ────────────────────────────────
    Route::middleware('role:'.UserRole::Staff->value)->group(function () {
        Route::get('checklists/runs/today', DailyRun::class)
            ->name('checklists.runs.today');

        Route::get('incidents/new', Create::class)
            ->name('incidents.create');
    });

    // ── Management-only routes (Admin + Supervisor) ──────
    Route::middleware('role:'.UserRole::Admin->value.','.UserRole::Supervisor->value)->group(function () {
        Route::get('dashboard', DashboardController::class)
            ->name('dashboard');

        Route::get('incidents', Index::class)
            ->name('incidents.index');

        Route::get('incidents/{incident}', Show::class)
            ->name('incidents.show');
    });

    // ── Admin-only routes ────────────────────────────────
    Route::middleware('role:'.UserRole::Admin->value)->group(function () {
        Route::get('templates', TemplateIndex::class)
            ->name('templates.index');

        Route::get('templates/create', TemplateManage::class)
            ->name('templates.create');

        Route::get('templates/{template}/edit', TemplateManage::class)
            ->name('templates.edit');
    });
});

require __DIR__.'/settings.php';
