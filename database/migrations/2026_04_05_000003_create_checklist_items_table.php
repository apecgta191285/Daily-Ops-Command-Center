<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Day 2A: Create checklist_items table.
     * - FK to checklist_templates: cascadeOnDelete (parent-child)
     * - Unique constraint: (checklist_template_id, sort_order) per Data Definition §11
     * - Source: System Spec §6, Task List §4.1
     */
    public function up(): void
    {
        Schema::create('checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_template_id')->constrained('checklist_templates')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order');
            $table->boolean('is_required')->default(true);
            $table->timestamps();

            $table->unique(['checklist_template_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_items');
    }
};
