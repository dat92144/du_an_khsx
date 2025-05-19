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
        Schema::create('product_cost_histories', function (Blueprint $table) {
    $table->id();
    $table->string('product_id');
    $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
    $table->year('year');
    $table->decimal('old_total_cost', 12, 2)->nullable();
    $table->decimal('total_cost', 12, 2);
    $table->string('reason')->nullable();
    $table->timestamps();
    $table->unique(['product_id', 'year']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_cost_histories');
    }
};
