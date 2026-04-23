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
                ['run_date', 'submitted_at'],
                'checklist_runs_run_date_submitted_at_index',
            );
        });

        Schema::table('incidents', function (Blueprint $table): void {
            $table->index('created_at', 'incidents_created_at_index');
            $table->index(['status', 'created_at'], 'incidents_status_created_at_index');
            $table->index(['status', 'owner_id'], 'incidents_status_owner_id_index');
            $table->index(['status', 'follow_up_due_at'], 'incidents_status_follow_up_due_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table): void {
            $table->dropIndex('incidents_status_follow_up_due_at_index');
            $table->dropIndex('incidents_status_owner_id_index');
            $table->dropIndex('incidents_status_created_at_index');
            $table->dropIndex('incidents_created_at_index');
        });

        Schema::table('checklist_runs', function (Blueprint $table): void {
            $table->dropIndex('checklist_runs_run_date_submitted_at_index');
        });
    }
};
