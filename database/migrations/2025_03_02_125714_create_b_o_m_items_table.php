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
        Schema::create('bom_items', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('bom_id');
            $table->string('process_id');
            $table->string('product_id')->nullable();
            $table->string('semi_finished_product_id')->nullable();
            $table->string('input_material_id');
            $table->string('input_material_type', 255);
            $table->integer('quantity_input');
            $table->string('input_unit_id');
            $table->string('output_id');
            $table->string('output_type', 255);
            $table->integer('quantity_output');
            $table->string('output_unit_id');
            $table->timestamps();

            $table->foreign('semi_finished_product_id')->references('id')->on('semi_finished_products')->delete('cascade');
            $table->foreign('bom_id')->references('id')->on('boms')->onDelete('cascade');
            $table->foreign('process_id')->references('id')->on('processes')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('input_unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('output_unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('b_o_m_items');
    }
};
