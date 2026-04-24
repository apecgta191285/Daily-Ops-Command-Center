<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ROOM_LOOKUP_INDEX = 'checklist_runs_template_room_date_creator_index';

    private const ROOM_LOOKUP_UNIQUE = 'checklist_runs_template_room_date_creator_unique';

    public function up(): void
    {
        $duplicatesExist = DB::table('checklist_runs')
            ->whereNotNull('room_id')
            ->selectRaw('checklist_template_id, room_id, run_date, created_by, COUNT(*) as duplicate_count')
            ->groupBy('checklist_template_id', 'room_id', 'run_date', 'created_by')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($duplicatesExist) {
            throw new RuntimeException(
                'Cannot restore room-aware checklist run uniqueness because duplicate room-tied runs already exist.'
            );
        }

        Schema::table('checklist_runs', function (Blueprint $table): void {
            $table->dropIndex(self::ROOM_LOOKUP_INDEX);
            $table->unique(
                ['checklist_template_id', 'room_id', 'run_date', 'created_by'],
                self::ROOM_LOOKUP_UNIQUE,
            );
        });
    }

    public function down(): void
    {
        Schema::table('checklist_runs', function (Blueprint $table): void {
            $table->dropUnique(self::ROOM_LOOKUP_UNIQUE);
            $table->index(
                ['checklist_template_id', 'room_id', 'run_date', 'created_by'],
                self::ROOM_LOOKUP_INDEX,
            );
        });
    }
};
