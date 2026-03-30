<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add assign_document permission
        $permissionId = DB::table('permissions')->insertGetId([
            'name' => 'assign_document',
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Get role IDs
        $superadminRoleId = DB::table('roles')->where('name', 'superadmin')->value('id');
        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        $adminprocRoleId = DB::table('roles')->where('name', 'adminproc')->value('id');

        // Assign permission to superadmin, admin, and adminproc roles
        $rolePermissions = [];
        if ($superadminRoleId) {
            $rolePermissions[] = [
                'role_id' => $superadminRoleId,
                'permission_id' => $permissionId,
            ];
        }
        if ($adminRoleId) {
            $rolePermissions[] = [
                'role_id' => $adminRoleId,
                'permission_id' => $permissionId,
            ];
        }
        if ($adminprocRoleId) {
            $rolePermissions[] = [
                'role_id' => $adminprocRoleId,
                'permission_id' => $permissionId,
            ];
        }

        if (!empty($rolePermissions)) {
            DB::table('role_has_permissions')->insert($rolePermissions);
        }
    }

    public function down(): void
    {
        $permissionId = DB::table('permissions')->where('name', 'assign_document')->value('id');
        
        if ($permissionId) {
            // Remove role permissions
            DB::table('role_has_permissions')->where('permission_id', $permissionId)->delete();
            
            // Remove permission
            DB::table('permissions')->where('id', $permissionId)->delete();
        }
    }
};
