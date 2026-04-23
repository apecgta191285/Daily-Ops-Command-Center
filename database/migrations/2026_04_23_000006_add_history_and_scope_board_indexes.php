<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checklist_runs', function (Blueprint $table): void {
            $table->index(
                ['created_by', 'run_date', 'room_id'],
                'checklist_runs_created_by_run_date_room_id_index',
            );
            $table->index(
                ['run_date', 'assigned_team_or_scope', 'submitted_at'],
                'checklist_runs_run_date_scope_submitted_at_index',
            );
        });

        Schema::table('incidents', function (Blueprint $table): void {
            $table->index('resolved_at', 'incidents_resolved_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table): void {
            $table->dropIndex('incidents_resolved_at_index');
        });

        Schema::table('checklist_runs', function (Blueprint $table): void {
            $table->dropIndex('checklist_runs_run_date_scope_submitted_at_index');
            $table->dropIndex('checklist_runs_created_by_run_date_room_id_index');
        });
    }
};
