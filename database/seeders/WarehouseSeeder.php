<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = [
            [
                'name' => 'Main Warehouse',
                'code' => 'MAIN',
                'description' => 'Main central warehouse for all projects',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Project A Warehouse',
                'code' => 'PROJ-A',
                'description' => 'Warehouse dedicated to Project A',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Project B Warehouse',
                'code' => 'PROJ-B',
                'description' => 'Warehouse dedicated to Project B',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'External Storage',
                'code' => 'EXT',
                'description' => 'External storage facility for overflow items',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Supplier Consignment',
                'code' => 'CONS',
                'description' => 'Items stored at supplier locations',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::firstOrCreate(
                ['code' => $warehouse['code']],
                $warehouse
            );
        }
    }
}
