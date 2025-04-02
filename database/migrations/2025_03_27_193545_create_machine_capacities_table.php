<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('machine_capacity', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('machine_id');
            $table->integer('max_output_per_day');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('machine_capacity');
    }
};
