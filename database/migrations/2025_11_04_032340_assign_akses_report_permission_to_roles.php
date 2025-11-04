<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get the akses_report permission ID
        $permissionId = DB::table('permissions')->where('name', 'akses_report')->value('id');
        
        if ($permissionId) {
            // Get role IDs for all roles except 'logistic' and 'user'
            $roleIds = DB::table('roles')
                ->whereNotIn('name', ['logistic', 'user'])
                ->pluck('id');
            
            // Assign permission to all eligible roles (skip if already assigned)
            foreach ($roleIds as $roleId) {
                $exists = DB::table('role_has_permissions')
                    ->where('role_id', $roleId)
                    ->where('permission_id', $permissionId)
                    ->exists();
                
                if (!$exists) {
                    DB::table('role_has_permissions')->insert([
                        'role_id' => $roleId,
                        'permission_id' => $permissionId,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get the akses_report permission ID
        $permissionId = DB::table('permissions')->where('name', 'akses_report')->value('id');
        
        if ($permissionId) {
            // Remove permission from 'adminproc' role only (since others already had it)
            $adminprocRoleId = DB::table('roles')->where('name', 'adminproc')->value('id');
            
            if ($adminprocRoleId) {
                DB::table('role_has_permissions')
                    ->where('role_id', $adminprocRoleId)
                    ->where('permission_id', $permissionId)
                    ->delete();
            }
        }
    }
};
