<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table): void {
            $table->string('subcategory', 120)
                ->nullable()
                ->after('category');

            $table->index(['category', 'subcategory', 'status'], 'incidents_category_subcategory_status_index');
        });
    }

    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table): void {
            $table->dropIndex('incidents_category_subcategory_status_index');
            $table->dropColumn('subcategory');
        });
    }
};
