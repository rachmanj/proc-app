<?php

namespace Database\Seeders;

use App\Models\ApprovalLevel;
use Illuminate\Database\Seeder;

class ApprovalLevelSeeder extends Seeder
{
    public function run()
    {
        ApprovalLevel::create([
            'name' => 'Level 1 Approval',
            'level' => 1
        ]);

        ApprovalLevel::create([
            'name' => 'Level 2 Approval',
            'level' => 2
        ]);
    }
} 