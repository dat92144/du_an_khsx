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
        Schema::create('spec_attributes', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('spec_id');
            $table->string('name', 255);
            $table->text('value')->nullable();
            $table->string('attribute_type', 255);
            $table->timestamps();

            $table->foreign('spec_id')->references('id')->on('specs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spec_attributes');
    }
};
