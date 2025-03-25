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
            //san pham 1
            [
                'id' => 'bi001', 'bom_id' => 'bom001', 'process_id' => 'pr001',
                'product_id' => 'pro001', 'input_material_id' => 'mat001', 'input_material_type' => 'materials',
                'quantity_input' => 2, 'input_unit_id' => 'u001', 'output_id' => 'sem008', 'output_type' => 'semi_finished_products',
                'quantity_output' => 1.8, 'output_unit_id' => 'u001', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi002', 'bom_id' => 'bom001', 'process_id' => 'pr002',
                'product_id' => 'pro001', 'input_material_id' => 'sem008', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1.8, 'input_unit_id' => 'u001', 'output_id' => 'sem001', 'output_type' => 'semi_finished_products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi003', 'bom_id' => 'bom001', 'process_id' => 'pr003',
                'product_id' => 'pro001', 'input_material_id' => 'mat003', 'input_material_type' => 'materials',
                'quantity_input' => 0.5, 'input_unit_id' => 'u003', 'output_id' => 'sem004', 'output_type' => 'semi_finished_products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi004', 'bom_id' => 'bom001', 'process_id' => 'pr003',
                'product_id' => 'pro001', 'input_material_id' => 'sem001', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1, 'input_unit_id' => 'u002', 'output_id' => 'sem004', 'output_type' => 'semi_finished_products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi035', 'bom_id' => 'bom001', 'process_id' => 'pr004',
                'product_id' => 'pro001', 'input_material_id' => 'sem004', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1, 'input_unit_id' => 'u004', 'output_id' => 'pro001', 'output_type' => 'products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi005', 'bom_id' => 'bom001', 'process_id' => 'pr004',
                'product_id' => 'pro001', 'input_material_id' => 'sem003', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1, 'input_unit_id' => 'u004', 'output_id' => 'pro001', 'output_type' => 'products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi006', 'bom_id' => 'bom001', 'process_id' => 'pr004',
                'product_id' => 'pro001', 'input_material_id' => 'sem006', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1, 'input_unit_id' => 'u004', 'output_id' => 'pro001', 'output_type' => 'products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi007', 'bom_id' => 'bom001', 'process_id' => 'pr004',
                'product_id' => 'pro001', 'input_material_id' => 'sem007', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1, 'input_unit_id' => 'u004', 'output_id' => 'pro001', 'output_type' => 'products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            //san pham 4
            [
                'id' => 'bi008', 'bom_id' => 'bom004', 'process_id' => 'pr001',
                'product_id' => 'pro004', 'input_material_id' => 'mat002', 'input_material_type' => 'materials',
                'quantity_input' => 1.5, 'input_unit_id' => 'u001', 'output_id' => 'sem009', 'output_type' => 'semi_finished_products',
                'quantity_output' => 1.2, 'output_unit_id' => 'u001', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi009', 'bom_id' => 'bom004', 'process_id' => 'pr002',
                'product_id' => 'pro004', 'input_material_id' => 'sem009', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1.2, 'input_unit_id' => 'u001', 'output_id' => 'sem002', 'output_type' => 'semi_finished_products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi010', 'bom_id' => 'bom004', 'process_id' => 'pr005',
                'product_id' => 'pro004', 'input_material_id' => 'sem002', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1, 'input_unit_id' => 'u002', 'output_id' => 'sem010', 'output_type' => 'semi_finished_products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi011', 'bom_id' => 'bom004', 'process_id' => 'pr005',
                'product_id' => 'pro004', 'input_material_id' => 'mat004', 'input_material_type' => 'materials',
                'quantity_input' => 1, 'input_unit_id' => 'u004', 'output_id' => 'sem010', 'output_type' => 'semi_finished_products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi012', 'bom_id' => 'bom004', 'process_id' => 'pr003',
                'product_id' => 'pro004', 'input_material_id' => 'mat003', 'input_material_type' => 'materials',
                'quantity_input' => 0.5, 'input_unit_id' => 'u003', 'output_id' => 'sem011', 'output_type' => 'semi_finished_products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi013', 'bom_id' => 'bom004', 'process_id' => 'pr003',
                'product_id' => 'pro004', 'input_material_id' => 'sem010', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1, 'input_unit_id' => 'u002', 'output_id' => 'sem011', 'output_type' => 'semi_finished_products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi014', 'bom_id' => 'bom004', 'process_id' => 'pr004',
                'product_id' => 'pro004', 'input_material_id' => 'sem003', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1, 'input_unit_id' => 'u004', 'output_id' => 'pro004', 'output_type' => 'products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi015', 'bom_id' => 'bom004', 'process_id' => 'pr004',
                'product_id' => 'pro004', 'input_material_id' => 'sem006', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1, 'input_unit_id' => 'u004', 'output_id' => 'pro004', 'output_type' => 'products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi016', 'bom_id' => 'bom004', 'process_id' => 'pr004',
                'product_id' => 'pro004', 'input_material_id' => 'sem007', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1, 'input_unit_id' => 'u004', 'output_id' => 'pro004', 'output_type' => 'products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi017', 'bom_id' => 'bom004', 'process_id' => 'pr004',
                'product_id' => 'pro004', 'input_material_id' => 'sem011', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1, 'input_unit_id' => 'u002', 'output_id' => 'pro004', 'output_type' => 'products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
             //san pham 2
             [
                'id' => 'bi018', 'bom_id' => 'bom002', 'process_id' => 'pr001',
                'product_id' => 'pro002', 'input_material_id' => 'mat002', 'input_material_type' => 'materials',
                'quantity_input' => 1.5, 'input_unit_id' => 'u001', 'output_id' => 'sem009', 'output_type' => 'semi_finished_products',
                'quantity_output' => 1.2, 'output_unit_id' => 'u001', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi019', 'bom_id' => 'bom002', 'process_id' => 'pr002',
                'product_id' => 'pro002', 'input_material_id' => 'sem009', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1.2, 'input_unit_id' => 'u001', 'output_id' => 'sem002', 'output_type' => 'semi_finished_products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi020', 'bom_id' => 'bom002', 'process_id' => 'pr003',
                'product_id' => 'pro002', 'input_material_id' => 'mat003', 'input_material_type' => 'materials',
                'quantity_input' => 0.5, 'input_unit_id' => 'u003', 'output_id' => 'sem005', 'output_type' => 'semi_finished_products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi021', 'bom_id' => 'bom002', 'process_id' => 'pr003',
                'product_id' => 'pro002', 'input_material_id' => 'sem002', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1, 'input_unit_id' => 'u002', 'output_id' => 'sem005', 'output_type' => 'semi_finished_products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi022', 'bom_id' => 'bom002', 'process_id' => 'pr004',
                'product_id' => 'pro002', 'input_material_id' => 'sem003', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1, 'input_unit_id' => 'u004', 'output_id' => 'pro002', 'output_type' => 'products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi023', 'bom_id' => 'bom002', 'process_id' => 'pr004',
                'product_id' => 'pro002', 'input_material_id' => 'sem006', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1, 'input_unit_id' => 'u004', 'output_id' => 'pro002', 'output_type' => 'products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi024', 'bom_id' => 'bom002', 'process_id' => 'pr004',
                'product_id' => 'pro002', 'input_material_id' => 'sem007', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1, 'input_unit_id' => 'u004', 'output_id' => 'pro002', 'output_type' => 'products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi025', 'bom_id' => 'bom002', 'process_id' => 'pr004',
                'product_id' => 'pro002', 'input_material_id' => 'sem005', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1, 'input_unit_id' => 'u002', 'output_id' => 'pro002', 'output_type' => 'products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            //san pham 3
            [
                'id' => 'bi027', 'bom_id' => 'bom003', 'process_id' => 'pr001',
                'product_id' => 'pro003', 'input_material_id' => 'mat001', 'input_material_type' => 'materials',
                'quantity_input' => 2, 'input_unit_id' => 'u001', 'output_id' => 'sem008', 'output_type' => 'semi_finished_products',
                'quantity_output' => 1.8, 'output_unit_id' => 'u001', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi028', 'bom_id' => 'bom003', 'process_id' => 'pr002',
                'product_id' => 'pro003', 'input_material_id' => 'sem008', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1.8, 'input_unit_id' => 'u001', 'output_id' => 'sem001', 'output_type' => 'semi_finished_products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi029', 'bom_id' => 'bom003', 'process_id' => 'pr003',
                'product_id' => 'pro003', 'input_material_id' => 'mat003', 'input_material_type' => 'materials',
                'quantity_input' => 0.5, 'input_unit_id' => 'u003', 'output_id' => 'sem004', 'output_type' => 'semi_finished_products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi030', 'bom_id' => 'bom003', 'process_id' => 'pr003',
                'product_id' => 'pro003', 'input_material_id' => 'sem001', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1, 'input_unit_id' => 'u002', 'output_id' => 'sem004', 'output_type' => 'semi_finished_products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi031', 'bom_id' => 'bom003', 'process_id' => 'pr004',
                'product_id' => 'pro003', 'input_material_id' => 'sem003', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1, 'input_unit_id' => 'u004', 'output_id' => 'pro003', 'output_type' => 'products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi032', 'bom_id' => 'bom003', 'process_id' => 'pr004',
                'product_id' => 'pro003', 'input_material_id' => 'sem006', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1, 'input_unit_id' => 'u004', 'output_id' => 'pro003', 'output_type' => 'products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi033', 'bom_id' => 'bom003', 'process_id' => 'pr004',
                'product_id' => 'pro003', 'input_material_id' => 'sem007', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1, 'input_unit_id' => 'u004', 'output_id' => 'pro003', 'output_type' => 'products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi034', 'bom_id' => 'bom003', 'process_id' => 'pr004',
                'product_id' => 'pro003', 'input_material_id' => 'sem004', 'input_material_type' => 'semi_finished_products',
                'quantity_input' => 1, 'input_unit_id' => 'u004', 'output_id' => 'pro003', 'output_type' => 'products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'id' => 'bi036', 'bom_id' => 'bom003', 'process_id' => 'pr004',
                'product_id' => 'pro003', 'input_material_id' => 'mat005', 'input_material_type' => 'materials',
                'quantity_input' => 1, 'input_unit_id' => 'u004', 'output_id' => 'pro003', 'output_type' => 'products',
                'quantity_output' => 1, 'output_unit_id' => 'u002', 'created_at' => now(), 'updated_at' => now()
            ],
            
        ]);
    }
}
