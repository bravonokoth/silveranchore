<?php
// database/migrations/XXXX_XX_XX_XXXXXX_recreate_notifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('notifications');

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable'); // notifiable_type + notifiable_id
            $table->json('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // Allow guest notifications
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('notifiable_type')->nullable()->change();
            $table->unsignedBigInteger('notifiable_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};