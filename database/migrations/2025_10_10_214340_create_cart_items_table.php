<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
      Schema::create('cart_items', function (Blueprint $table) {
    $table->id();

    // Must be nullable for SET NULL to work
    $table->foreignId('user_id')
          ->nullable()
          ->constrained()
          ->nullOnDelete();

    $table->foreignId('product_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->integer('quantity')->default(1);

    // For guest users
    $table->string('session_id')->nullable()->index();

    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};