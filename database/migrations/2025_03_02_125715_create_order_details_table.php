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
        Schema::create('order_details', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('order_id');
            $table->string('product_id')->nullable();
            $table->string('semi_finished_product_id')->nullable();
            $table->string('product_type', 255);
            $table->integer('quantity_product');
            $table->string('unit_id');
            $table->timestamps();

            $table->foreign('semi_finished_product_id')->references('id')->on('semi_finished_products')->delete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
