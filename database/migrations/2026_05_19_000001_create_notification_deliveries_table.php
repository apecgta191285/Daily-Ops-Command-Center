<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_deliveries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('incident_id')->nullable()->constrained('incidents')->nullOnDelete();
            $table->string('channel', 40);
            $table->string('event_type', 80);
            $table->string('recipient_type', 20)->nullable();
            $table->string('recipient_fingerprint', 16)->nullable();
            $table->string('status', 30);
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->string('message', 500)->nullable();
            $table->timestamp('attempted_at');
            $table->timestamps();

            $table->index(['channel', 'status', 'attempted_at']);
            $table->index(['incident_id', 'event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_deliveries');
    }
};
