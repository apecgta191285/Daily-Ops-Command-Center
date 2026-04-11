<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checklist_templates', function (Blueprint $table) {
            $table->unique('title', 'checklist_templates_title_unique');
        });

        if (DB::getDriverName() !== 'sqlite') {
            return;
        }

        DB::statement(
            'CREATE UNIQUE INDEX checklist_templates_single_active_idx
             ON checklist_templates (is_active)
             WHERE is_active = 1'
        );
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            try {
                DB::statement('DROP INDEX checklist_templates_single_active_idx');
            } catch (QueryException) {
                // Index may not exist if the migration failed before creation.
            }
        }

        Schema::table('checklist_templates', function (Blueprint $table) {
            $table->dropUnique('checklist_templates_title_unique');
        });
    }
};
