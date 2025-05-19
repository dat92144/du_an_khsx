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
        Schema::create('product_costs', function (Blueprint $table) {
    $table->string('id')->primary();
    $table->string('product_id');
    $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
    $table->double('material_cost')->default(0);
    $table->double('overhead_cost')->default(0);
    $table->double('inventory_cost')->default(0);
    $table->double('transportation_cost')->default(0);
    $table->double('wastage_cost')->default(0);
    $table->double('depreciation_cost')->default(0);
    $table->double('service_outsourcing_cost')->default(0);
    $table->double('other_costs')->default(0);
    $table->double('total_cost')->default(0);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_costs');
    }
};
