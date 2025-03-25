<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->string('material_id');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
            $table->enum('type',['material','semi_finished_product']);
            $table->integer('quantity')->notNull();
            $table->string('unit_id');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->decimal('price_per_unit', 10, 2)->notNull();
            $table->decimal('total_price', 15, 2)->notNull();
            $table->timestamp('expected_delivery_date')->nullable();
            $table->string('status')->default('pending'); // pending, ordered, received
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};
