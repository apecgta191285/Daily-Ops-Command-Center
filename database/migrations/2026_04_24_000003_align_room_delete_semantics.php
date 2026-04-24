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
            $table->dropForeign(['room_id']);
        });

        Schema::table('checklist_runs', function (Blueprint $table): void {
            $table->foreign('room_id')
                ->references('id')
                ->on('rooms')
                ->restrictOnDelete();
        });

        Schema::table('incidents', function (Blueprint $table): void {
            $table->dropForeign(['room_id']);
        });

        Schema::table('incidents', function (Blueprint $table): void {
            $table->foreign('room_id')
                ->references('id')
                ->on('rooms')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('checklist_runs', function (Blueprint $table): void {
            $table->dropForeign(['room_id']);
        });

        Schema::table('checklist_runs', function (Blueprint $table): void {
            $table->foreign('room_id')
                ->references('id')
                ->on('rooms')
                ->nullOnDelete();
        });

        Schema::table('incidents', function (Blueprint $table): void {
            $table->dropForeign(['room_id']);
        });

        Schema::table('incidents', function (Blueprint $table): void {
            $table->foreign('room_id')
                ->references('id')
                ->on('rooms')
                ->nullOnDelete();
        });
    }
};
