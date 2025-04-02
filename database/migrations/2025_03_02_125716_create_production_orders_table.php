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
        Schema::create('production_orders', function (Blueprint $table) {
            $table->string('id')->primary(); // Sử dụng UUID thay vì auto-increment ID
            $table->string('product_id');
            $table->string('order_id')->nullable();
            $table->integer('order_quantity')->notNull();
            $table->date('order_date')->notNull();
            $table->date('delivery_date')->notNull();
            $table->string('bom_id');
            $table->string('producing_status', 255)->notNull(); // planned, producing, completed
            $table->timestamps(); // Thêm created_at & updated_at

            // Khóa ngoại
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('bom_id')->references('id')->on('boms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_orders');
    }
};
