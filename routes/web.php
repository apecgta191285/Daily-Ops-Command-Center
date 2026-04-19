<?php

declare(strict_types=1);

use App\Domain\Access\Enums\UserRole;
use App\Http\Controllers\Admin\DuplicateChecklistTemplateController;
use App\Http\Controllers\Management\DashboardController;
use App\Livewire\Admin\ChecklistTemplates\Index as TemplateIndex;
use App\Livewire\Admin\ChecklistTemplates\Manage as TemplateManage;
use App\Livewire\Admin\Users\Index as UserIndex;
use App\Livewire\Admin\Users\Manage as UserManage;
use App\Livewire\Management\Checklists\HistoryIndex as ChecklistHistoryIndex;
use App\Livewire\Management\Checklists\HistoryShow as ChecklistHistoryShow;
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
        Route::get('checklists/runs/today/{scope?}', DailyRun::class)
            ->name('checklists.runs.today');

        Route::get('incidents/new', Create::class)
            ->name('incidents.create');
    });

    // ── Management-only routes (Admin + Supervisor) ──────
    Route::middleware('role:'.UserRole::Admin->value.','.UserRole::Supervisor->value)->group(function () {
        Route::get('dashboard', DashboardController::class)
            ->name('dashboard');

        Route::get('checklists/history', ChecklistHistoryIndex::class)
            ->name('checklists.history.index');

        Route::get('checklists/history/{run}', ChecklistHistoryShow::class)
            ->name('checklists.history.show');

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

        Route::post('templates/{template}/duplicate', DuplicateChecklistTemplateController::class)
            ->name('templates.duplicate');

        Route::get('templates/{template}/edit', TemplateManage::class)
            ->name('templates.edit');

        Route::get('users', UserIndex::class)
            ->name('users.index');

        Route::get('users/create', UserManage::class)
            ->name('users.create');

        Route::get('users/{user}/edit', UserManage::class)
            ->name('users.edit');
    });
});

require __DIR__.'/settings.php';
