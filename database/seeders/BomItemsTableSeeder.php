<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class BomItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bom_items')->insert([

            // ===== BOM001: Quy trình chi tiết sản xuất Clinker CPC50 =====

            // B1. Nhập nguyên liệu
            [
                'id' => 'BI001', 'bom_id' => 'BOM001', 'process_id' => 'P001',
                'product_id' => null, 'semi_finished_product_id' => 'SFP002',
                'input_material_id' => 'MAT001', 'input_material_type' => 'materials',
                'quantity_input' => 80, 'input_unit_id' => 'U001',
                'output_id' => 'SFP002', 'output_type' => 'semi_finished_products',
                'quantity_output' => 100, 'output_unit_id' => 'U001',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'BI002', 'bom_id' => 'BOM001', 'process_id' => 'P001',
                'product_id' => null, 'semi_finished_product_id' => 'SFP002',
                'input_material_id' => 'MAT002', 'input_material_type' => 'materials',
                'quantity_input' => 15, 'input_unit_id' => 'U001',
                'output_id' => 'SFP002', 'output_type' => 'semi_finished_products',
                'quantity_output' => 100, 'output_unit_id' => 'U001',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'BI003', 'bom_id' => 'BOM001', 'process_id' => 'P001',
                'product_id' => null, 'semi_finished_product_id' => 'SFP002',
                'input_material_id' => 'MAT003', 'input_material_type' => 'materials',
                'quantity_input' => 5, 'input_unit_id' => 'U001',
                'output_id' => 'SFP002', 'output_type' => 'semi_finished_products',
                'quantity_output' => 100, 'output_unit_id' => 'U001',
                'created_at' => now(), 'updated_at' => now(),
            ],

            // B2. Nghiền & phối trộn (giữ tỷ lệ 80:15:5 như trên)

            // B3. Đồng nhất hóa
            [
                'id' => 'BI004', 'bom_id' => 'BOM001', 'process_id' => 'P001',
                'product_id' => null, 'semi_finished_product_id' => 'SFP002',
                'input_material_id' => 'SFP002', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 100, 'input_unit_id' => 'U001',
                'output_id' => 'SFP002', 'output_type' => 'semi_finished_products',
                'quantity_output' => 100, 'output_unit_id' => 'U001',
                'created_at' => now(), 'updated_at' => now(),
            ],

            // B4. Sấy khô
            [
                'id' => 'BI005', 'bom_id' => 'BOM001', 'process_id' => 'P001',
                'product_id' => null, 'semi_finished_product_id' => 'SFP002',
                'input_material_id' => 'SFP002', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 100, 'input_unit_id' => 'U001',
                'output_id' => 'SFP002', 'output_type' => 'semi_finished_products',
                'quantity_output' => 97, 'output_unit_id' => 'U001', // hao hụt ~3%
                'created_at' => now(), 'updated_at' => now(),
            ],

            // B5. Nung clinker
            [
                'id' => 'BI006', 'bom_id' => 'BOM001', 'process_id' => 'P001',
                'product_id' => null, 'semi_finished_product_id' => 'SFP002',
                'input_material_id' => 'SFP002', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 97, 'input_unit_id' => 'U001',
                'output_id' => 'SFP002', 'output_type' => 'semi_finished_products',
                'quantity_output' => 63, 'output_unit_id' => 'U001', // hao hụt 35%
                'created_at' => now(), 'updated_at' => now(),
            ],

            // B6. Làm mát clinker
            [
                'id' => 'BI007', 'bom_id' => 'BOM001', 'process_id' => 'P001',
                'product_id' => null, 'semi_finished_product_id' => 'SFP002',
                'input_material_id' => 'SFP002', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 63, 'input_unit_id' => 'U001',
                'output_id' => 'SFP002', 'output_type' => 'semi_finished_products',
                'quantity_output' => 62.37, 'output_unit_id' => 'U001', // hao hụt 1%
                'created_at' => now(), 'updated_at' => now(),
            ],

            // B7. Lưu kho clinker (không thay đổi lượng)
            [
                'id' => 'BI008', 'bom_id' => 'BOM001', 'process_id' => 'P001',
                'product_id' => null, 'semi_finished_product_id' => 'SFP002',
                'input_material_id' => 'SFP002', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 62.37, 'input_unit_id' => 'U001',
                'output_id' => 'SFP002', 'output_type' => 'semi_finished_products',
                'quantity_output' => 62.37, 'output_unit_id' => 'U001',
                'created_at' => now(), 'updated_at' => now(),
            ],

            // ===== BOM002: Xi măng rời PCB40 =====
            // ===== BOM001: Quy trình chi tiết sản xuất Clinker CPC50 =====

            // B1. Nhập nguyên liệu
            [
                'id' => 'BI009', 'bom_id' => 'BOM002', 'process_id' => 'P001',
                'product_id' => null, 'semi_finished_product_id' => 'SFP001',
                'input_material_id' => 'MAT001', 'input_material_type' => 'materials',
                'quantity_input' => 80, 'input_unit_id' => 'U001',
                'output_id' => 'SFP002', 'output_type' => 'semi_finished_products',
                'quantity_output' => 100, 'output_unit_id' => 'U001',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'BI010', 'bom_id' => 'BOM002', 'process_id' => 'P001',
                'product_id' => null, 'semi_finished_product_id' => 'SFP001',
                'input_material_id' => 'MAT002', 'input_material_type' => 'materials',
                'quantity_input' => 15, 'input_unit_id' => 'U001',
                'output_id' => 'SFP002', 'output_type' => 'semi_finished_products',
                'quantity_output' => 100, 'output_unit_id' => 'U001',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'BI011', 'bom_id' => 'BOM002', 'process_id' => 'P001',
                'product_id' => null, 'semi_finished_product_id' => 'SFP001',
                'input_material_id' => 'MAT003', 'input_material_type' => 'materials',
                'quantity_input' => 5, 'input_unit_id' => 'U001',
                'output_id' => 'SFP002', 'output_type' => 'semi_finished_products',
                'quantity_output' => 100, 'output_unit_id' => 'U001',
                'created_at' => now(), 'updated_at' => now(),
            ],

            // B2. Nghiền & phối trộn (giữ tỷ lệ 80:15:5 như trên)

            // B3. Đồng nhất hóa
            [
                'id' => 'BI012', 'bom_id' => 'BOM002', 'process_id' => 'P001',
                'product_id' => null, 'semi_finished_product_id' => 'SFP001',
                'input_material_id' => 'SFP002', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 100, 'input_unit_id' => 'U001',
                'output_id' => 'SFP002', 'output_type' => 'semi_finished_products',
                'quantity_output' => 100, 'output_unit_id' => 'U001',
                'created_at' => now(), 'updated_at' => now(),
            ],

            // B4. Sấy khô
            [
                'id' => 'BI013', 'bom_id' => 'BOM002', 'process_id' => 'P001',
                'product_id' => null, 'semi_finished_product_id' => 'SFP001',
                'input_material_id' => 'SFP002', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 100, 'input_unit_id' => 'U001',
                'output_id' => 'SFP002', 'output_type' => 'semi_finished_products',
                'quantity_output' => 97, 'output_unit_id' => 'U001', // hao hụt ~3%
                'created_at' => now(), 'updated_at' => now(),
            ],

            // B5. Nung clinker
            [
                'id' => 'BI014', 'bom_id' => 'BOM002', 'process_id' => 'P001',
                'product_id' => null, 'semi_finished_product_id' => 'SFP001',
                'input_material_id' => 'SFP002', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 97, 'input_unit_id' => 'U001',
                'output_id' => 'SFP002', 'output_type' => 'semi_finished_products',
                'quantity_output' => 63, 'output_unit_id' => 'U001', // hao hụt 35%
                'created_at' => now(), 'updated_at' => now(),
            ],

            // B6. Làm mát clinker
            [
                'id' => 'BI015', 'bom_id' => 'BOM002', 'process_id' => 'P001',
                'product_id' => null, 'semi_finished_product_id' => 'SFP001',
                'input_material_id' => 'SFP002', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 63, 'input_unit_id' => 'U001',
                'output_id' => 'SFP002', 'output_type' => 'semi_finished_products',
                'quantity_output' => 62.37, 'output_unit_id' => 'U001', // hao hụt 1%
                'created_at' => now(), 'updated_at' => now(),
            ],
            //B7. sản xuất xi măng rời
            [
                'id' => 'BI016', 'bom_id' => 'BOM002', 'process_id' => 'P004',
                'product_id' => null, 'semi_finished_product_id' => 'SFP001',
                'input_material_id' => 'SFP002', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 95, 'input_unit_id' => 'U001',
                'output_id' => 'SFP001', 'output_type' => 'semi_finished_products',
                'quantity_output' => 100, 'output_unit_id' => 'U001',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'BI017', 'bom_id' => 'BOM002', 'process_id' => 'P004',
                'product_id' => null, 'semi_finished_product_id' => 'SFP001',
                'input_material_id' => 'MAT004', 'input_material_type' => 'materials',
                'quantity_input' => 3, 'input_unit_id' => 'U001',
                'output_id' => 'SFP001', 'output_type' => 'semi_finished_products',
                'quantity_output' => 100, 'output_unit_id' => 'U001',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'BI018', 'bom_id' => 'BOM002', 'process_id' => 'P004',
                'product_id' => null, 'semi_finished_product_id' => 'SFP001',
                'input_material_id' => 'MAT005', 'input_material_type' => 'materials',
                'quantity_input' => 2, 'input_unit_id' => 'U001',
                'output_id' => 'SFP001', 'output_type' => 'semi_finished_products',
                'quantity_output' => 100, 'output_unit_id' => 'U001',
                'created_at' => now(), 'updated_at' => now(),
            ],

            // ===== BOM003: Đóng bao PCB40 =====

            // ===== BOM001: Quy trình chi tiết sản xuất Clinker CPC50 =====

            // B1. Nhập nguyên liệu
            [
                'id' => 'BI019', 'bom_id' => 'BOM003', 'process_id' => 'P001',
                'product_id' => 'PRO001', 'semi_finished_product_id' => null,
                'input_material_id' => 'MAT001', 'input_material_type' => 'materials',
                'quantity_input' => 80, 'input_unit_id' => 'U001',
                'output_id' => 'SFP002', 'output_type' => 'semi_finished_products',
                'quantity_output' => 100, 'output_unit_id' => 'U001',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'BI020', 'bom_id' => 'BOM003', 'process_id' => 'P001',
                'product_id' => 'PRO001', 'semi_finished_product_id' => null,
                'input_material_id' => 'MAT002', 'input_material_type' => 'materials',
                'quantity_input' => 15, 'input_unit_id' => 'U001',
                'output_id' => 'SFP002', 'output_type' => 'semi_finished_products',
                'quantity_output' => 100, 'output_unit_id' => 'U001',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'BI021', 'bom_id' => 'BOM003', 'process_id' => 'P001',
                'product_id' => 'PRO001', 'semi_finished_product_id' => null,
                'input_material_id' => 'MAT003', 'input_material_type' => 'materials',
                'quantity_input' => 5, 'input_unit_id' => 'U001',
                'output_id' => 'SFP002', 'output_type' => 'semi_finished_products',
                'quantity_output' => 100, 'output_unit_id' => 'U001',
                'created_at' => now(), 'updated_at' => now(),
            ],

            // B2. Nghiền & phối trộn (giữ tỷ lệ 80:15:5 như trên)

            // B3. Đồng nhất hóa
            [
                'id' => 'BI022', 'bom_id' => 'BOM003', 'process_id' => 'P001',
                'product_id' => 'PRO001', 'semi_finished_product_id' => null,
                'input_material_id' => 'SFP002', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 100, 'input_unit_id' => 'U001',
                'output_id' => 'SFP002', 'output_type' => 'semi_finished_products',
                'quantity_output' => 100, 'output_unit_id' => 'U001',
                'created_at' => now(), 'updated_at' => now(),
            ],

            // B4. Sấy khô
            [
                'id' => 'BI023', 'bom_id' => 'BOM003', 'process_id' => 'P001',
                'product_id' => 'PRO001', 'semi_finished_product_id' => null,
                'input_material_id' => 'SFP002', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 100, 'input_unit_id' => 'U001',
                'output_id' => 'SFP002', 'output_type' => 'semi_finished_products',
                'quantity_output' => 97, 'output_unit_id' => 'U001', // hao hụt ~3%
                'created_at' => now(), 'updated_at' => now(),
            ],

            // B5. Nung clinker
            [
                'id' => 'BI024', 'bom_id' => 'BOM003', 'process_id' => 'P001',
                'product_id' => 'PRO001', 'semi_finished_product_id' => null,
                'input_material_id' => 'SFP002', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 97, 'input_unit_id' => 'U001',
                'output_id' => 'SFP002', 'output_type' => 'semi_finished_products',
                'quantity_output' => 63, 'output_unit_id' => 'U001', // hao hụt 35%
                'created_at' => now(), 'updated_at' => now(),
            ],

            // B6. Làm mát clinker
            [
                'id' => 'BI025', 'bom_id' => 'BOM003', 'process_id' => 'P001',
                'product_id' => 'PRO001', 'semi_finished_product_id' => null,
                'input_material_id' => 'SFP002', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 63, 'input_unit_id' => 'U001',
                'output_id' => 'SFP002', 'output_type' => 'semi_finished_products',
                'quantity_output' => 62.37, 'output_unit_id' => 'U001', // hao hụt 1%
                'created_at' => now(), 'updated_at' => now(),
            ],
            //B7. sản xuất xi măng rời
            [
                'id' => 'BI026', 'bom_id' => 'BOM003', 'process_id' => 'P004',
                'product_id' => 'PRO001', 'semi_finished_product_id' => null,
                'input_material_id' => 'SFP002', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 95, 'input_unit_id' => 'U001',
                'output_id' => 'SFP001', 'output_type' => 'semi_finished_products',
                'quantity_output' => 100, 'output_unit_id' => 'U001',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'BI027', 'bom_id' => 'BOM003', 'process_id' => 'P004',
                'product_id' => 'PRO001', 'semi_finished_product_id' => null,
                'input_material_id' => 'MAT004', 'input_material_type' => 'materials',
                'quantity_input' => 3, 'input_unit_id' => 'U001',
                'output_id' => 'SFP001', 'output_type' => 'semi_finished_products',
                'quantity_output' => 100, 'output_unit_id' => 'U001',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => 'BI028', 'bom_id' => 'BOM003', 'process_id' => 'P004',
                'product_id' => 'PRO001', 'semi_finished_product_id' => null,
                'input_material_id' => 'MAT005', 'input_material_type' => 'materials',
                'quantity_input' => 2, 'input_unit_id' => 'U001',
                'output_id' => 'SFP001', 'output_type' => 'semi_finished_products',
                'quantity_output' => 100, 'output_unit_id' => 'U001',
                'created_at' => now(), 'updated_at' => now(),
            ],

            [
                'id' => 'BI029', 'bom_id' => 'BOM003', 'process_id' => 'P006',
                'product_id' => 'PRO001', 'semi_finished_product_id' => null,
                'input_material_id' => 'SFP001', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 100, 'input_unit_id' => 'U001',
                'output_id' => 'PRO001', 'output_type' => 'products',
                'quantity_output' => 2000, 'output_unit_id' => 'U003',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);

    }
}
