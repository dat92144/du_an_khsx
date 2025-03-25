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
        Schema::create('spec_attribute_values', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('spec_attribute_id');
            $table->float('number_value')->nullable();
            $table->text('text_value')->nullable();
            $table->boolean('boolean_value')->nullable();
            $table->timestamps();

            $table->foreign('spec_attribute_id')->references('id')->on('spec_attributes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spec_attribute_values');
    }
};
