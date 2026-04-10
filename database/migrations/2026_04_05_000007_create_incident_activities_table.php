<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Day 2A: Create incident_activities table.
     * - Append-only log: only created_at, no updated_at
     * - FK to incidents: cascadeOnDelete (parent-child)
     * - FK actor_id → users: restrictOnDelete (traceability FR-07)
     * - Canonical action_type values: 'created', 'status_changed' (Data Definition §10)
     * - Source: System Spec §6, Task List §4.1
     */
    public function up(): void
    {
        Schema::create('incident_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained('incidents')->cascadeOnDelete();
            $table->string('action_type');
            $table->text('summary')->nullable();
            $table->foreignId('actor_id')->constrained('users')->restrictOnDelete();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_activities');
    }
};
