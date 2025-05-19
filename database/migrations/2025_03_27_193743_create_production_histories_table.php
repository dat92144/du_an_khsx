<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('production_histories', function (Blueprint $table) {
            $table->id();
            $table->string('production_order_id'); 
            $table->string('process_id'); 
            $table->string('product_id'); 
            $table->integer('completed_quantity')->default(0); 
            $table->date('date'); 
            $table->timestamps();

            $table->foreign('production_order_id')->references('id')->on('production_orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('process_id')->references('id')->on('processes')->onDelete('cascade');

        });
    }

    public function down()
    {
        Schema::dropIfExists('production_histories');
    }
};
