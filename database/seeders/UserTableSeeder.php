<?php

namespace Database\Seeders;

use App\Enums\CommonStatus;
use App\Models\Clinic;
use App\Models\Package;
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
        // 1. Super Admin User
        $superAdmin = User::where('email', 'admin@gmail.com')
            ->orWhere('phone_no', '01234567847')
            ->first();

        if (!$superAdmin) {
            $superAdmin = User::create([
                'email' => 'admin@gmail.com',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'date_of_birth' => '1996-11-14',
                'password' => 'Admin@123',
                'phone_no' => '01234567847',
                'status' => CommonStatus::ACTIVE->value,
                'address' => 'Headquarters, Skoracare Main Road',
            ]);
        }
        
        $superAdminRole = Role::where('name', config('constants.super_admin_role_name'))->first();
        if ($superAdminRole && $superAdmin) {
            $superAdmin->assignRole($superAdminRole);
        }

        // 2. Demo Doctor User
        $goldPkg = Package::where('name', 'Package 2')->orWhere('name', 'Gold')->first();
        $doctorUser = User::where('email', 'doctor@gmail.com')
            ->orWhere('phone_no', '9876543210')
            ->first();

        if (!$doctorUser) {
            $doctorUser = User::create([
                'email' => 'doctor@gmail.com',
                'first_name' => 'Rocky',
                'last_name' => 'Kumar Singh',
                'date_of_birth' => '1990-01-01',
                'password' => 'Admin@123',
                'phone_no' => '9876543210',
                'status' => CommonStatus::ACTIVE->value,
                'qualification' => 'MBBS, MD',
                'registration_number' => 'DOC12345',
                'package_id' => $goldPkg->id ?? null,
                'address' => 'Skoracare Hospital, Sector 12',
            ]);
        }

        $doctorRole = Role::where('name', config('constants.doctor_role_name'))->first();
        if ($doctorRole && $doctorUser) {
            $doctorUser->assignRole($doctorRole);
        }

        // 3. Demo Clinic for Doctor
        if ($doctorUser) {
            Clinic::firstOrCreate(
                ['doctor_id' => $doctorUser->id],
                [
                    'name' => 'Skoracares Super Speciality Clinic',
                    'consultation_fee' => 500,
                    'phone_no' => '9876543210',
                    'address' => 'Plot 42, Health City, Skoracares',
                ]
            );
        }
    }
}
