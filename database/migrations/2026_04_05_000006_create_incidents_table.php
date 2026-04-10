<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Day 2A: Create incidents table.
     * - title: max 120 chars per Data Definition §11
     * - status: default 'Open' per Data Definition §3
     * - Canonical values:
     *   category: อุปกรณ์คอมพิวเตอร์ / เครือข่าย / ความสะอาด / ความปลอดภัย / สภาพแวดล้อม / อื่น ๆ
     *   severity: Low / Medium / High
     *   status: Open / In Progress / Resolved
     * - FK created_by → users: restrictOnDelete (traceability FR-07)
     * - Source: System Spec §6, Task List §4.1, Data Definition §3
     */
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120);
            $table->string('category');
            $table->string('severity');
            $table->string('status')->default('Open');
            $table->text('description');
            $table->string('attachment_path')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
