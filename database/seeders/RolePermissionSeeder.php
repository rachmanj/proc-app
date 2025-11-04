<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'superadmin', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'adminproc', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'buyer', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'director', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'user', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('roles')->insert($roles);

        $permissions = [
            ['name' => 'akses_admin', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'akses_permission', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'akses_user', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'akses_master', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'akses_procurement', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'akses_approval', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'akses_report', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'akses_proc_po', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'akses_proc_pr', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'view_poservice', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'access_consignment', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'upload_consignment', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'crud_consignment', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'search_consignment', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('permissions')->insert($permissions);

        // get superadmin role
        $superadminRoleId = DB::table('roles')->where('name', 'superadmin')->first()->id;
        $adminRoleId = DB::table('roles')->where('name', 'admin')->first()->id;
        $adminprocRoleId = DB::table('roles')->where('name', 'adminproc')->first()->id;
        $buyerRoleId = DB::table('roles')->where('name', 'buyer')->first()->id;
        $directorRoleId = DB::table('roles')->where('name', 'director')->first()->id;
        // get permission id
        $aksesAdminPermissionId = DB::table('permissions')->where('name', 'akses_admin')->first()->id;
        $aksesPermissionPermissionId = DB::table('permissions')->where('name', 'akses_permission')->first()->id;
        $aksesUserPermissionId = DB::table('permissions')->where('name', 'akses_user')->first()->id;
        $aksesMasterPermissionId = DB::table('permissions')->where('name', 'akses_master')->first()->id;
        $aksesProcurementPermissionId = DB::table('permissions')->where('name', 'akses_procurement')->first()->id;
        $aksesProcPoPermissionId = DB::table('permissions')->where('name', 'akses_proc_po')->first()->id;
        $aksesApprovalPermissionId = DB::table('permissions')->where('name', 'akses_approval')->first()->id;
        $aksesReportPermissionId = DB::table('permissions')->where('name', 'akses_report')->first()->id;
        $aksesProcPrPermissionId = DB::table('permissions')->where('name', 'akses_proc_pr')->first()->id;
        $viewPoservicePermissionId = DB::table('permissions')->where('name', 'view_poservice')->first()->id;
        $accessConsignmentPermissionId = DB::table('permissions')->where('name', 'access_consignment')->first()->id;
        $uploadConsignmentPermissionId = DB::table('permissions')->where('name', 'upload_consignment')->first()->id;
        $crudConsignmentPermissionId = DB::table('permissions')->where('name', 'crud_consignment')->first()->id;
        $searchConsignmentPermissionId = DB::table('permissions')->where('name', 'search_consignment')->first()->id;
        // assign permission to roles
        $rolePermissions = [
            ['role_id' => $superadminRoleId, 'permission_id' => $aksesAdminPermissionId],
            ['role_id' => $superadminRoleId, 'permission_id' => $aksesPermissionPermissionId],
            ['role_id' => $superadminRoleId, 'permission_id' => $aksesUserPermissionId],
            ['role_id' => $superadminRoleId, 'permission_id' => $aksesMasterPermissionId],
            ['role_id' => $superadminRoleId, 'permission_id' => $aksesProcurementPermissionId],
            ['role_id' => $superadminRoleId, 'permission_id' => $aksesApprovalPermissionId],
            ['role_id' => $superadminRoleId, 'permission_id' => $aksesReportPermissionId],
            ['role_id' => $superadminRoleId, 'permission_id' => $aksesProcPoPermissionId],
            ['role_id' => $superadminRoleId, 'permission_id' => $aksesProcPrPermissionId],
            ['role_id' => $superadminRoleId, 'permission_id' => $viewPoservicePermissionId],
            ['role_id' => $superadminRoleId, 'permission_id' => $accessConsignmentPermissionId],
            ['role_id' => $superadminRoleId, 'permission_id' => $uploadConsignmentPermissionId],
            ['role_id' => $superadminRoleId, 'permission_id' => $crudConsignmentPermissionId],
            ['role_id' => $superadminRoleId, 'permission_id' => $searchConsignmentPermissionId],
            ['role_id' => $adminRoleId, 'permission_id' => $aksesAdminPermissionId],
            ['role_id' => $adminRoleId, 'permission_id' => $aksesPermissionPermissionId],
            ['role_id' => $adminRoleId, 'permission_id' => $aksesUserPermissionId],
            ['role_id' => $adminRoleId, 'permission_id' => $aksesMasterPermissionId],
            ['role_id' => $adminRoleId, 'permission_id' => $aksesProcurementPermissionId],
            ['role_id' => $adminRoleId, 'permission_id' => $aksesApprovalPermissionId],
            ['role_id' => $adminRoleId, 'permission_id' => $aksesReportPermissionId],
            ['role_id' => $adminRoleId, 'permission_id' => $viewPoservicePermissionId],
            ['role_id' => $adminRoleId, 'permission_id' => $accessConsignmentPermissionId],
            ['role_id' => $adminRoleId, 'permission_id' => $uploadConsignmentPermissionId],
            ['role_id' => $adminRoleId, 'permission_id' => $crudConsignmentPermissionId],
            ['role_id' => $adminRoleId, 'permission_id' => $searchConsignmentPermissionId],
            ['role_id' => $adminprocRoleId, 'permission_id' => $aksesMasterPermissionId],
            ['role_id' => $adminprocRoleId, 'permission_id' => $aksesProcurementPermissionId],
            ['role_id' => $adminprocRoleId, 'permission_id' => $aksesApprovalPermissionId],
            ['role_id' => $adminprocRoleId, 'permission_id' => $aksesReportPermissionId],
            ['role_id' => $adminprocRoleId, 'permission_id' => $aksesProcPoPermissionId],
            ['role_id' => $adminprocRoleId, 'permission_id' => $aksesProcPrPermissionId],
            ['role_id' => $adminprocRoleId, 'permission_id' => $viewPoservicePermissionId],
            ['role_id' => $adminprocRoleId, 'permission_id' => $accessConsignmentPermissionId],
            ['role_id' => $adminprocRoleId, 'permission_id' => $uploadConsignmentPermissionId],
            ['role_id' => $adminprocRoleId, 'permission_id' => $crudConsignmentPermissionId],
            ['role_id' => $adminprocRoleId, 'permission_id' => $searchConsignmentPermissionId],
            ['role_id' => $buyerRoleId, 'permission_id' => $aksesProcurementPermissionId],
            ['role_id' => $buyerRoleId, 'permission_id' => $aksesReportPermissionId],
            ['role_id' => $buyerRoleId, 'permission_id' => $aksesProcPrPermissionId],
            ['role_id' => $buyerRoleId, 'permission_id' => $aksesProcPoPermissionId],
            ['role_id' => $buyerRoleId, 'permission_id' => $viewPoservicePermissionId],
            ['role_id' => $buyerRoleId, 'permission_id' => $accessConsignmentPermissionId],
            ['role_id' => $buyerRoleId, 'permission_id' => $searchConsignmentPermissionId],
            ['role_id' => $directorRoleId, 'permission_id' => $aksesApprovalPermissionId],
            ['role_id' => $directorRoleId, 'permission_id' => $aksesProcPoPermissionId],
            ['role_id' => $directorRoleId, 'permission_id' => $aksesReportPermissionId],
            ['role_id' => $directorRoleId, 'permission_id' => $aksesProcurementPermissionId],
            ['role_id' => $directorRoleId, 'permission_id' => $viewPoservicePermissionId],
        ];

        DB::table('role_has_permissions')->insert($rolePermissions);

        $user = DB::table('users')->where('username', 'superadmin')->first();
        if ($user) {
            DB::table('model_has_roles')->insert([
                'role_id' => $superadminRoleId,
                'model_id' => $user->id,
                'model_type' => 'App\Models\User',
            ]);
        }

        $userBuyer = DB::table('users')->where('username', 'buyer1')->first();
        if ($userBuyer) {
            DB::table('model_has_roles')->insert([
                'role_id' => $buyerRoleId,
                'model_id' => $userBuyer->id,
                'model_type' => 'App\Models\User',
            ]);
        }

        $userProcMgr = DB::table('users')->where('username', 'procmgr')->first();
        if ($userProcMgr) {
            DB::table('model_has_roles')->insert([
                'role_id' => $adminprocRoleId,
                'model_id' => $userProcMgr->id,
                'model_type' => 'App\Models\User',
            ]);
        }

        $userDirector = DB::table('users')->where('username', 'director')->first();
        if ($userDirector) {
            DB::table('model_has_roles')->insert([
                'role_id' => $directorRoleId,
                'model_id' => $userDirector->id,
                'model_type' => 'App\Models\User',
            ]);
        }

        $approvalLevel1 = DB::table('approval_levels')->where('level', 1)->first();
        $approvalLevel2 = DB::table('approval_levels')->where('level', 2)->first();

        if ($approvalLevel1) {
            DB::table('approvers')->insert([
                'user_id' => $userProcMgr->id,
                'approval_level_id' => $approvalLevel1->id,
            ]);
        }

        if ($approvalLevel2) {
            DB::table('approvers')->insert([
                'user_id' => $userDirector->id,
                'approval_level_id' => $approvalLevel2->id,
            ]);
        }
    }
}
