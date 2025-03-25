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
        Schema::create('specs', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('product_id');
            $table->string('process_id')->nullable();
            $table->string('machine_id')->nullable();
            $table->float('lead_time')->nullable();
            $table->float('cycle_time')->nullable();
            $table->integer('lot_size')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('process_id')->references('id')->on('processes')->onDelete('set null');
            $table->foreign('machine_id')->references('id')->on('machines')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specs');
    }
};
