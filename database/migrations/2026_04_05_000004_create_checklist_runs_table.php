<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Day 2A: Create checklist_runs table.
     * - FK to checklist_templates: restrictOnDelete (runs are historical records)
     * - FK created_by → users: restrictOnDelete (traceability FR-07)
     * - FK submitted_by → users: nullable, restrictOnDelete (traceability FR-07)
     * - Unique constraint: (checklist_template_id, run_date, created_by) per Data Definition §11
     * - Source: System Spec §6, Task List §4.1, Decision Log D-008/D-009
     */
    public function up(): void
    {
        Schema::create('checklist_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_template_id')->constrained('checklist_templates')->restrictOnDelete();
            $table->date('run_date');
            $table->string('assigned_team_or_scope')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['checklist_template_id', 'run_date', 'created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_runs');
    }
};
