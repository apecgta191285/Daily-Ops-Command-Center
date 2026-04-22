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
            $table->foreignId('room_id')
                ->nullable()
                ->after('checklist_template_id')
                ->constrained('rooms')
                ->nullOnDelete();
        });

        Schema::table('incidents', function (Blueprint $table): void {
            $table->foreignId('room_id')
                ->nullable()
                ->after('severity')
                ->constrained('rooms')
                ->nullOnDelete();
            $table->string('equipment_reference', 120)
                ->nullable()
                ->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('room_id');
            $table->dropColumn('equipment_reference');
        });

        Schema::table('checklist_runs', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('room_id');
        });
    }
};
