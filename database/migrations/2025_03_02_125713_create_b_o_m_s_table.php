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
        Schema::create('boms', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('product_id')->nullable();
            $table->string('semi_finished_product_id')->nullable();
            $table->timestamps();

            $table->foreign('semi_finished_product_id')->references('id')->on('semi_finished_products')->delete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('b_o_m_s');
    }
};
