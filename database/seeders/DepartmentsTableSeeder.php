<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['id' => 1, 'department_name' => 'Management / BOD', 'akronim' => 'BOD', 'sap_code' => '10', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'department_name' => 'Commercial', 'akronim' => 'COMM', 'sap_code' => '100', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'department_name' => 'Internal Audit & System', 'akronim' => 'IAS', 'sap_code' => '120', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'department_name' => 'Corporate Secretary', 'akronim' => 'CORSEC', 'sap_code' => '130', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'department_name' => 'APS - Arka Project Support', 'akronim' => 'APS', 'sap_code' => '140', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'department_name' => 'Relationship & Coordination', 'akronim' => 'RNC', 'sap_code' => '160', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'department_name' => 'Design & Construction', 'akronim' => 'DNC', 'sap_code' => '180', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'department_name' => 'Finance', 'akronim' => 'FIN', 'sap_code' => '190', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'department_name' => 'Human Capital & Support', 'akronim' => 'HCS', 'sap_code' => '20', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'department_name' => 'Logistic & Warehouse', 'akronim' => 'LOGW', 'sap_code' => '200', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'department_name' => 'Accounting', 'akronim' => 'ACC', 'sap_code' => '30', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'department_name' => 'Plant', 'akronim' => 'PLANT', 'sap_code' => '40', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 16, 'department_name' => 'Procurement', 'akronim' => 'PROC', 'sap_code' => '50', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 17, 'department_name' => 'Operation & Production', 'akronim' => 'OPR', 'sap_code' => '60', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 18, 'department_name' => 'Safety', 'akronim' => 'SHE', 'sap_code' => '70', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 19, 'department_name' => 'Information Technology', 'akronim' => 'IT', 'sap_code' => '80', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 20, 'department_name' => 'Research & Development', 'akronim' => 'RND', 'sap_code' => '90', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('departments')->insert($departments);
    }
}
