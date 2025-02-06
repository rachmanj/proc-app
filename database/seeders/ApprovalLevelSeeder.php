<?php

namespace Database\Seeders;

use App\Models\ApprovalLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Approver;
class ApprovalLevelSeeder extends Seeder
{
    public function run()
    {
        ApprovalLevel::create([
            'name' => 'Procurement Manager',
            'level' => 1
        ]);


        ApprovalLevel::create([
            'name' => 'Director',
            'level' => 2
        ]);

        $procMgrUser = DB::table('users')->where('username', 'procmgr')->first();
        $directorUser = DB::table('users')->where('username', 'director')->first();

        if ($procMgrUser) {
            Approver::create([
                'user_id' => $procMgrUser->id,
                'approval_level_id' => 1,
            ]);

        }

        if ($directorUser) {
            Approver::create([
                'user_id' => $directorUser->id,
                'approval_level_id' => 2,
            ]);
        }


    }

} 