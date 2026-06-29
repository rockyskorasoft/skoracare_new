<?php

namespace Database\Seeders;

use App\Enums\CommonStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::updateOrCreate(
            ['email' => 'skoracare@gmail.com'],
            [
                'first_name' => 'super',
                'last_name' => 'admin',
                'date_of_birth' => '1996-11-14',
                'password' => 'Admin@123',
                'phone_no' => '01234567847',
                'status' => CommonStatus::ACTIVE->value,
                'profile_pic' => null,
                'address' => null,
            ]
        );
        $superAdminRole = Role::where('name', config('constants.super_admin_role_name'))
            ->first();
        if ($superAdminRole) {
            $superAdmin->assignRole($superAdminRole);
        }

        $admin = User::updateOrCreate(
            ['email' => 'murphymedicaladmin@yopmail.com'],
            [
                'first_name' => 'admin',
                'last_name' => null,
                'date_of_birth' => '1998-11-14',
                'password' => 'Admin@123',
                'phone_no' => '0123456789',
                'status' => CommonStatus::ACTIVE->value,
                'profile_pic' => null,
                'address' => null,
            ]
        );
        $adminRole = Role::where('name', config('constants.admin_role_name'))
            ->first();
        if ($adminRole) {
            $admin->assignRole($adminRole);
        }
    }
}
