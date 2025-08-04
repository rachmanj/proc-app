<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Omanof Sullivans',
            'email' => 'oman@gmail.com',
            'username' => 'superadmin',
            'password' => Hash::make('123456'),
            'project' => '000H',
            'is_active' => true,
        ]);

        User::factory()->create([
            'name' => 'Admin Procurement',
            'email' => 'adminproc@gmail.com',
            'username' => 'adminproc',
            'password' => Hash::make('123456'),
            'project' => '001H',
            'is_active' => true,
        ]);

        User::factory()->create([
            'name' => 'Buyer One',
            'email' => 'buyer@gmail.com',
            'username' => 'buyer1',
            'password' => Hash::make('123456'),
            'project' => '001H',
            'is_active' => true,
        ]);

        User::factory()->create([
            'name' => 'Procurement Manager',
            'email' => 'procmgr@gmail.com',
            'username' => 'procmgr',
            'password' => Hash::make('123456'),
            'project' => '001H',
            'is_active' => true,
        ]);

        User::factory()->create([
            'name' => 'Director',
            'email' => 'director@gmail.com',
            'username' => 'director',
            'password' => Hash::make('123456'),
            'project' => '001H',
            'is_active' => true,
        ]);

        $bodDepartmentId = DB::table('departments')->where('akronim', 'BOD')->value('id');
        $procDepartmentId = DB::table('departments')->where('akronim', 'PROC')->value('id');
        $itDepartmentId = DB::table('departments')->where('akronim', 'IT')->value('id');



        $this->call([
            ProjectsTableSeeder::class,
            DepartmentsTableSeeder::class,
            RolePermissionSeeder::class,
            ApprovalLevelSeeder::class,
            WarehouseSeeder::class,
        ]);
    }
}
