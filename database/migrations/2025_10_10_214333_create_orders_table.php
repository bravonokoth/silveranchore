<?php

namespace Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            // Allow NULL so SET NULL works
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->string('session_id')->nullable()->index();
            $table->string('email')->nullable(); // ✅ ADDED
            $table->unsignedBigInteger('shipping_address_id')->nullable()->index(); // ✅ ADDED
            $table->unsignedBigInteger('billing_address_id')->nullable()->index();  // ✅ ADDED
            $table->decimal('total', 10, 2);
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};