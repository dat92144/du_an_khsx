<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('routing_bom', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('product_id');
            $table->integer('step_order');
            $table->string('machine_id');
            $table->string('process_id');
            $table->float('cycle_time');
            $table->float('lead_time');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('routing_bom');
    }
};
