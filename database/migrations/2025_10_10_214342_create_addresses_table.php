<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable(); // guest checkout

            // Contact details
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();

            // Address details
            $table->string('type')->default('shipping'); // shipping or billing
            $table->string('line1');
            $table->string('line2')->nullable();
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country');

            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('shipping_address_id')
                  ->nullable()
                  ->constrained('addresses')
                  ->nullOnDelete();
            $table->foreignId('billing_address_id')
                  ->nullable()
                  ->constrained('addresses')
                  ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shipping_address_id');
            $table->dropConstrainedForeignId('billing_address_id');
        });

        Schema::dropIfExists('addresses');
    }
};
