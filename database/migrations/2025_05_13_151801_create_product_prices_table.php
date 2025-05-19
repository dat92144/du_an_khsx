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
        Schema::create('product_prices', function (Blueprint $table) {
    $table->string('id')->primary();
    $table->string('product_id');
    $table->year('year');
    $table->decimal('total_cost', 15, 2);
    $table->decimal('expected_profit_percent', 5, 2)->default(0);
    $table->decimal('sell_price', 15, 2);
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
