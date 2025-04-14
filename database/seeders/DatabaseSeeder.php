<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            // UsersTableSeeder::class,
            // RolesTableSeeder::class,
            // UserRolesTableSeeder::class,
            // PermissionsTableSeeder::class,
            // RolePermissionsTableSeeder::class,
            // ProductsTableSeeder::class,
            // MaterialsTableSeeder::class,
            // SemiFinishedProductsTableSeeder::class,
            // CustomersTableSeeder::class,
            // ProcessesTableSeeder::class,
            // MachinesTableSeeder::class,
            // UnitsTableSeeder::class,
            BomsTableSeeder::class,
            BomItemsTableSeeder::class,
            // SpecsTableSeeder::class,
            // SpecAttributesTableSeeder::class,
            // SpecAttributeValuesTableSeeder::class,
            // OrdersTableSeeder::class,
            // OrderDetailsTableSeeder::class,
            // InventoryTableSeeder::class,
            // SuppliersTableSeeder::class,
            // SupplierPricesTableSeeder::class,
            // InventoryMaterialSeeder::class,
            // InventorySemiProductSeeder::class,
            // InventoryProductSeeder::class,
            // MachineCapacitySeeder::class,
            // RoutingBomSeeder::class,
        ]);
    }
}
