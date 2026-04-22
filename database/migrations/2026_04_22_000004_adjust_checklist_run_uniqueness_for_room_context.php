<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ROOM_LOOKUP_INDEX = 'checklist_runs_template_room_date_creator_index';

    public function up(): void
    {
        Schema::table('checklist_runs', function (Blueprint $table): void {
            $table->dropUnique('checklist_runs_checklist_template_id_run_date_created_by_unique');
            $table->index(
                ['checklist_template_id', 'room_id', 'run_date', 'created_by'],
                self::ROOM_LOOKUP_INDEX,
            );
        });
    }

    public function down(): void
    {
        Schema::table('checklist_runs', function (Blueprint $table): void {
            $table->dropIndex(self::ROOM_LOOKUP_INDEX);
            $table->unique(['checklist_template_id', 'run_date', 'created_by']);
        });
    }
};
