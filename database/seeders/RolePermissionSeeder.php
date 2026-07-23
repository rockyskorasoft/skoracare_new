<?php

namespace Database\Seeders;

use App\Models\Package;
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

        // ── 1. User Management Permissions ──────────────────────────────────────────
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
            'staff-list',
            'staff-create',
            'staff-delete',
            'staff-edit',
            'staff-show',
        ] as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName],
                ['parent_id' => $userManagementPermission->id]
            );
        }

        // ── 2. Dashboard Cards Permissions ──────────────────────────────────────────
        $dashboardManagementPermission = Permission::firstOrCreate(['name' => 'dashboard-management']);
        foreach ([
            'dashboard-users-card',
            'dashboard-doctors-card',
            'dashboard-clinics-card',
            'dashboard-roles-card',
            'dashboard-doctor-profile-card',
            'dashboard-my-clinics-card',
            'dashboard-latest-activities-card',
        ] as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName],
                ['parent_id' => $dashboardManagementPermission->id]
            );
        }

        // ── 3. Logs Management ──────────────────────────────────────────────────────
        $activityLogManagement = Permission::firstOrCreate(['name' => 'logs-management']);
        Permission::firstOrCreate(['name' => 'activity-log-list'], ['parent_id' => $activityLogManagement->id]);
        Permission::firstOrCreate(['name' => 'activity-log-show'], ['parent_id' => $activityLogManagement->id]);

        // ── 4. Doctor Management ───────────────────────────────────────────────────
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

        // ── 5. Clinic Management ───────────────────────────────────────────────────
        $clinicManagement = Permission::firstOrCreate(['name' => 'clinic-management']);
        foreach ([
            'clinic-list',
            'clinic-create',
            'clinic-edit',
            'clinic-delete',
            'clinic-show',
            'clinic-export',
        ] as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName],
                ['parent_id' => $clinicManagement->id]
            );
        }

        // ── 6. Package Management ──────────────────────────────────────────────────
        $packageManagement = Permission::firstOrCreate(['name' => 'package-management']);
        foreach ([
            'package-list',
            'package-create',
            'package-edit',
            'package-delete',
            'package-show',
        ] as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName],
                ['parent_id' => $packageManagement->id]
            );
        }

        // ── 7. Doctor Panel Feature Permissions ─────────────────────────────────────
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

        // ── 8. Create & Assign Roles ────────────────────────────────────────────────
        $allPermissions = Permission::all();

        // Super Admin gets EVERYTHING
        $superAdmin = Role::updateOrCreate(
            ['guard_name' => 'web', 'name' => config('constants.super_admin_role_name')],
            []
        );
        $superAdmin->givePermissionTo($allPermissions);

        // Admin gets EVERYTHING
        $adminRole = Role::updateOrCreate(
            ['guard_name' => 'web', 'name' => config('constants.admin_role_name')],
            []
        );
        $adminRole->givePermissionTo($allPermissions);

        // Doctor role getting baseline permissions
        $doctorRole = Role::updateOrCreate(
            ['guard_name' => 'web', 'name' => config('constants.doctor_role_name')],
            []
        );
        $doctorRole->syncPermissions([
            'dashboard-doctor-profile-card',
            'dashboard-my-clinics-card',
            'dashboard-latest-activities-card',
            'appointment-list',
            'appointment-create',
            'appointment-show',
            'clinic-list',
            'clinic-create',
            'clinic-show',
            'clinic-edit',
            'staff-list',
            'staff-create',
            'staff-edit',
            'staff-delete',
            'staff-show',
        ]);

        Role::updateOrCreate(
            ['guard_name' => 'web', 'name' => config('constants.vendor_role_name')],
            []
        );

        $staffRole = Role::updateOrCreate(
            ['guard_name' => 'web', 'name' => 'Staff'],
            []
        );
        $staffRole->syncPermissions([
            'appointment-list',
            'appointment-create',
            'clinic-list',
            'patients-list',
            'follow-up-list',
        ]);

        // ── 9. Seed Default Subscription Packages ────────────────────────────────────
        $basicPkg = Package::updateOrCreate(
            ['name' => 'Package 1'],
            [
                'description' => 'Essential starter plan for small single-doctor clinics.',
                'monthly_price' => 790.00,
                'yearly_price' => 7890.00,
                'clinic_limit' => 1,
                'user_limit' => 1,
                'status' => 'active',
                'is_popular' => false,
            ]
        );
        $basicPerms = Permission::whereIn('name', [
            'dashboard-doctor-profile-card',
            'dashboard-my-clinics-card',
            'appointment-list',
            'appointment-create',
            'appointment-show',
            'clinic-list',
            'clinic-create',
            'clinic-show',
        ])->pluck('id');
        $basicPkg->permissions()->sync($basicPerms);

        $goldPkg = Package::updateOrCreate(
            ['name' => 'Package 2'],
            [
                'description' => 'Comprehensive plan for growing multi-staff clinics.',
                'monthly_price' => 1290.00,
                'yearly_price' => 12880.00,
                'clinic_limit' => 3,
                'user_limit' => 3,
                'status' => 'active',
                'is_popular' => true,
            ]
        );
        $goldPerms = Permission::whereIn('name', [
            'dashboard-doctor-profile-card',
            'dashboard-my-clinics-card',
            'dashboard-latest-activities-card',
            'appointment-list',
            'appointment-create',
            'appointment-edit',
            'appointment-show',
            'appointment-export',
            'ask-skoracare-list',
            'clinic-list',
            'clinic-create',
            'clinic-edit',
            'clinic-show',
            'patients-list',
            'patients-create',
            'patients-show',
            'follow-up-list',
            'follow-up-create',
        ])->pluck('id');
        $goldPkg->permissions()->sync($goldPerms);

        $platinumPkg = Package::updateOrCreate(
            ['name' => 'Package 3'],
            [
                'description' => 'Advanced unlimited plan for multi-specialty hospitals.',
                'monthly_price' => 2490.00,
                'yearly_price' => 24880.00,
                'clinic_limit' => -1,
                'user_limit' => 5,
                'status' => 'active',
                'is_popular' => false,
            ]
        );
        $platinumPerms = Permission::where('parent_id', $doctorPanelManagement->id)
            ->orWhere('parent_id', $dashboardManagementPermission->id)
            ->pluck('id');
        $platinumPkg->permissions()->sync($platinumPerms);
    }
}
