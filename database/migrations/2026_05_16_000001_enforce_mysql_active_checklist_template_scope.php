<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement(
            'ALTER TABLE checklist_templates
             ADD COLUMN active_scope_key VARCHAR(255)
             GENERATED ALWAYS AS (CASE WHEN is_active = 1 THEN scope ELSE NULL END) VIRTUAL'
        );

        DB::statement(
            'ALTER TABLE checklist_templates
             ADD UNIQUE INDEX checklist_templates_active_scope_unique (active_scope_key)'
        );
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE checklist_templates DROP INDEX checklist_templates_active_scope_unique');
        DB::statement('ALTER TABLE checklist_templates DROP COLUMN active_scope_key');
    }
};
