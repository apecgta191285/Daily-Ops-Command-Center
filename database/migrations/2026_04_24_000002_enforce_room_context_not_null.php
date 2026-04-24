<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $hasChecklistRunNulls = DB::table('checklist_runs')->whereNull('room_id')->exists();
        $hasIncidentNulls = DB::table('incidents')->whereNull('room_id')->exists();

        if ($hasChecklistRunNulls || $hasIncidentNulls) {
            throw new RuntimeException(
                'Cannot enforce room context as non-null while room-less checklist runs or incidents still exist.'
            );
        }

        Schema::table('checklist_runs', function (Blueprint $table): void {
            $table->foreignId('room_id')
                ->nullable(false)
                ->change();
        });

        Schema::table('incidents', function (Blueprint $table): void {
            $table->foreignId('room_id')
                ->nullable(false)
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('checklist_runs', function (Blueprint $table): void {
            $table->foreignId('room_id')
                ->nullable()
                ->change();
        });

        Schema::table('incidents', function (Blueprint $table): void {
            $table->foreignId('room_id')
                ->nullable()
                ->change();
        });
    }
};
