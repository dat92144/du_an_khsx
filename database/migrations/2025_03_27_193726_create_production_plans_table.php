<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('production_plans', function (Blueprint $table) {
            $table->id();
            $table->string('order_id'); // Đơn hàng liên quan
            $table->string('product_id'); // Sản phẩm cần sản xuất
            $table->string('semi_finished_product_id')->nullable(); // ban Sản phẩm cần sản xuất
            $table->integer('lot_number'); // Số lô sản xuất
            $table->integer('lot_size'); // Số lượng sản xuất mỗi lô
            $table->integer('total_quantity'); // Tổng số lượng cần sản xuất
            $table->string('machine_id')->nullable(); // Máy thực hiện
            $table->string('process_id')->nullable(); // Công đoạn sản xuất
            $table->timestamp('start_time')->nullable(); // Thời gian bắt đầu
            $table->timestamp('end_time')->nullable(); // Thời gian kết thúc
            $table->timestamp('delivery_date')->nullable(); // Thời gian giao hàng
            $table->enum('status', ['planned', 'in_progress', 'completed'])->default('planned'); // Trạng thái kế hoạch
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('production_plans');
    }
};
