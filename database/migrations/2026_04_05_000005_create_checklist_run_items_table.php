<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Day 2A: Create checklist_run_items table.
     * - FK to checklist_runs: cascadeOnDelete (parent-child)
     * - FK to checklist_items: restrictOnDelete (cross-reference, protects historical data)
     * - FK checked_by → users: nullable, restrictOnDelete (traceability FR-07)
     * - result: nullable, canonical values = 'Done' / 'Not Done' (Data Definition §3, §10)
     * - Source: System Spec §6, Task List §4.1
     */
    public function up(): void
    {
        Schema::create('checklist_run_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_run_id')->constrained('checklist_runs')->cascadeOnDelete();
            $table->foreignId('checklist_item_id')->constrained('checklist_items')->restrictOnDelete();
            $table->string('result')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('checked_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_run_items');
    }
};
