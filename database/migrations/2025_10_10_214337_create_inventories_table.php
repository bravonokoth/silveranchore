<?php



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    Schema::create('inventories', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_id')->constrained()->onDelete('cascade');
        $table->integer('quantity')->default(0);
        $table->string('type')->default('adjustment');
        $table->text('notes')->nullable();
        $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};