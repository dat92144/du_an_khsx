<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('inventory_semi_products', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('semi_product_id', 36);
            $table->integer('quantity')->default(0);
            $table->string('unit_id', 36);
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('semi_product_id')->references('id')->on('semi_finished_products')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('inventory_semi_products');
    }
};