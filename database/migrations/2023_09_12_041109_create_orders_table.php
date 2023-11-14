<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
          $table->id();
          $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
          $table->string('number', 32)->unique();
          $table->decimal('total_price', 12, 2)->nullable();
          $table->string('status')->default('new'); // ['new', 'processing', 'shipped', 'delivered', 'cancelled']
          $table->string('currency');
          $table->decimal('shipping_price')->nullable();
          $table->string('shipping_method')->nullable();
          $table->text('notes')->nullable();
          $table->timestamps();
          $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
