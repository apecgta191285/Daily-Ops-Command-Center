<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            return;
        }

        try {
            DB::statement('DROP INDEX checklist_templates_single_active_idx');
        } catch (QueryException) {
            // Legacy index may already be absent in some environments.
        }

        DB::statement(
            'CREATE UNIQUE INDEX checklist_templates_single_active_per_scope_idx
             ON checklist_templates (scope)
             WHERE is_active = 1'
        );
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            return;
        }

        try {
            DB::statement('DROP INDEX checklist_templates_single_active_per_scope_idx');
        } catch (QueryException) {
            // Index may already be absent.
        }

        DB::statement(
            'CREATE UNIQUE INDEX checklist_templates_single_active_idx
             ON checklist_templates (is_active)
             WHERE is_active = 1'
        );
    }
};
