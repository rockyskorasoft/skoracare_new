<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── User Management ──────────────────────────────────────────────────────
        $userManagementPermission = Permission::firstOrCreate(['name' => 'user-management']);
        foreach ([
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'role-show',
            'user-list',
            'user-create',
            'user-delete',
            'user-edit',
            'user-show',
        ] as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName],
                ['parent_id' => $userManagementPermission->id]
            );
        }

        $dashboardManagementPermission = Permission::firstOrCreate(['name' => 'dashboard-management']);
        foreach ([
            'dashboard-users-card',
            'dashboard-latest-activities-card',
        ] as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName],
                ['parent_id' => $dashboardManagementPermission->id]
            );
        }

        //Create permission for activity log 
        $activityLogManagement = Permission::firstOrCreate(['name' => 'logs-management']);
        Permission::firstOrCreate(['name' => 'activity-log-list'], ['parent_id' => $activityLogManagement->id]);
        Permission::firstOrCreate(['name' => 'activity-log-show'], ['parent_id' => $activityLogManagement->id]);

        // ── Doctor Management ──────────────────────────────────────────────────
        $doctorManagement = Permission::firstOrCreate(['name' => 'doctor-management']);
        foreach ([
            'doctor-list',
            'doctor-create',
            'doctor-edit',
            'doctor-delete',
            'doctor-show',
        ] as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName],
                ['parent_id' => $doctorManagement->id]
            );
        }

        // ── Clinic Management ──────────────────────────────────────────────────
        $clinicManagement = Permission::firstOrCreate(['name' => 'clinic-management']);
        foreach ([
            'clinic-list',
            'clinic-create',
            'clinic-edit',
            'clinic-delete',
            'clinic-show',
        ] as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName],
                ['parent_id' => $clinicManagement->id]
            );
        }

        // ── Doctor Panel Permissions (Granular Action permissions) ──────────
        $doctorPanelManagement = Permission::firstOrCreate(['name' => 'doctor-panel-management']);
        
        $features = [
            'appointment'   => 'Appointment',
            'ask-skoracare' => 'Ask Skoracare',
            'clinic'        => 'OPD Billing / Clinics',
            'patients'      => 'All Patients',
            'follow-up'     => 'Follow Up',
            'pharmacy'      => 'Pharmacy',
            'analytics'     => 'Data Analytics',
            'messages'      => 'Messages',
        ];

        foreach ($features as $featureCode => $featureLabel) {
            foreach (['list', 'create', 'edit', 'show', 'delete', 'export'] as $action) {
                $permissionName = "{$featureCode}-{$action}";
                Permission::firstOrCreate(
                    ['name' => $permissionName],
                    ['parent_id' => $doctorPanelManagement->id]
                );
            }
        }

        // ── Assign all permissions to Super Admin & Admin ──────────────────────
        $superAdmin = Role::updateOrCreate(
            ['guard_name' => 'web', 'name' => config('constants.super_admin_role_name')],
            []
        );
        $superAdmin->givePermissionTo(Permission::all());

        $adminRole = Role::updateOrCreate(
            ['guard_name' => 'web', 'name' => config('constants.admin_role_name')],
            []
        );
        $adminRole->givePermissionTo(Permission::all());

        // Create Doctor and Vendor roles
        $doctorRole = Role::updateOrCreate(
            ['guard_name' => 'web', 'name' => config('constants.doctor_role_name')],
            []
        );
        // Assign default basic permissions to Doctor role
        $doctorRole->syncPermissions([
            'appointment-list',
            'appointment-show',
            'clinic-list',
            'clinic-show',
        ]);

        Role::updateOrCreate(
            ['guard_name' => 'web', 'name' => config('constants.vendor_role_name')],
            []
        );
    }
}
