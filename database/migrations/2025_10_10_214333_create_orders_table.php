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
            
            // User relationship
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            // Guest checkout support
            $table->string('session_id')->nullable()->index();
            $table->string('email')->nullable();
            
            // Address relationships
            $table->unsignedBigInteger('shipping_address_id')->nullable()->index();
            $table->unsignedBigInteger('billing_address_id')->nullable()->index();
            
            // Order details
            $table->decimal('total', 10, 2);
            $table->string('status')->default('pending');
            
            // Payment fields (CRITICAL for Paystack)
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])
                  ->default('pending');
            $table->string('payment_method')->nullable(); // paystack, pesapal, etc.
            $table->string('payment_reference')->nullable()->unique();
            $table->timestamp('paid_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};