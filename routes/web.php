<?php

use App\Filament\Resources\ChecklistTemplates\ChecklistTemplateResource;
use App\Http\Controllers\Management\DashboardController;
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

Route::middleware(['auth'])->group(function () {

    // ── Staff-only routes ────────────────────────────────
    Route::middleware('role:staff')->group(function () {
        Route::get('checklists/runs/today', DailyRun::class)
            ->name('checklists.runs.today');

        Route::get('incidents/new', Create::class)
            ->name('incidents.create');
    });

    // ── Management-only routes (Admin + Supervisor) ──────
    Route::middleware('role:admin,supervisor')->group(function () {
        Route::get('dashboard', DashboardController::class)
            ->name('dashboard');

        Route::get('incidents', Index::class)
            ->name('incidents.index');

        Route::get('incidents/{incident}', Show::class)
            ->name('incidents.show');
    });

    // ── Admin-only routes ────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::get('templates', function () {
            return redirect(ChecklistTemplateResource::getUrl('index'));
        })->name('templates.index');
    });
});

require __DIR__.'/settings.php';
