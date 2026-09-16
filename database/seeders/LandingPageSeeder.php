<?php

namespace Database\Seeders;

use App\Models\LandingPage;
use App\Models\LandingPageDoctor;
use App\Models\LandingPageTestimonial;
use App\Models\User;
use Illuminate\Database\Seeder;

class LandingPageSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::role(config('constants.super_admin_role_name'))->first() ?? User::first();
        $adminId = $superAdmin ? $superAdmin->id : 1;

        // 1. Revival Healthcare Services
        $lp1 = LandingPage::updateOrCreate(
            ['slug' => 'revival-healthcare-services'],
            [
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'clinic_name' => 'Revival Healthcare Services',
                'about_clinic' => '<p>Revival Healthcare Services is dedicated to providing compassionate, comprehensive, and patient-centered healthcare. With state-of-the-art medical technology and a team of distinguished specialists, we ensure that you and your family receive the highest quality of treatment and wellness care.</p>',
                'timings' => 'Mon - Sat: 09:00 AM - 08:00 PM',
                'address' => 'B-12, Sector 18, Commercial Belt, Noida, UP - 201301',
                'services' => "General Health Consultation\nCardiology & Heart Care\nOrthopedic & Joint Pain Management\nPediatric Care\nDiagnostic Lab & Pharmacy Support",
                'clinic_rating' => 4.9,
                'booking_slots' => "09:00 AM\n09:30 AM\n10:00 AM\n10:30 AM\n11:00 AM\n11:30 AM\n02:00 PM\n02:30 PM\n03:00 PM\n03:30 PM\n05:00 PM\n05:30 PM\n06:00 PM\n06:30 PM\n07:00 PM",
                'booking_start_time' => '09:00',
                'booking_end_time' => '19:30',
                'slot_interval_minutes' => 30,
                'is_appointment_enabled' => true,
                'whatsapp_number' => '+91 9876543210',
                'notification_email' => 'contact@revivalhealthcare.com',
                'status' => 'active',
            ]
        );

        $lp1->doctors()->delete();
        LandingPageDoctor::create([
            'landing_page_id' => $lp1->id,
            'doctor_name' => 'Dr. Rajesh Sharma',
            'email' => 'dr.rajesh@revival.com',
            'specialization' => 'Senior Cardiologist (MBBS, MD, DM)',
            'consultation_fee' => '₹800',
            'experience' => '14 Years',
            'sort_order' => 1,
        ]);
        LandingPageDoctor::create([
            'landing_page_id' => $lp1->id,
            'doctor_name' => 'Dr. Ananya Verma',
            'email' => 'dr.ananya@revival.com',
            'specialization' => 'Consultant Pediatrician (MBBS, DNB)',
            'consultation_fee' => '₹600',
            'experience' => '9 Years',
            'sort_order' => 2,
        ]);

        $lp1->testimonials()->delete();
        LandingPageTestimonial::create([
            'landing_page_id' => $lp1->id,
            'patient_name' => 'Sunil Kumar',
            'rating' => 5,
            'story' => 'Exceptional treatment and very caring medical staff. Dr. Rajesh explained the entire diagnosis clearly and the booking procedure was effortless.',
            'sort_order' => 1,
        ]);
        LandingPageTestimonial::create([
            'landing_page_id' => $lp1->id,
            'patient_name' => 'Pooja Aggarwal',
            'rating' => 5,
            'story' => 'Clean clinic premises, no waiting queue, and highly polite doctors. Strongly recommended for families.',
            'sort_order' => 2,
        ]);


        // 2. Skoracares Super Speciality Clinic
        $lp2 = LandingPage::updateOrCreate(
            ['slug' => 'skoracares-super-speciality-clinic'],
            [
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'clinic_name' => 'Skoracares Super Speciality Clinic',
                'about_clinic' => '<p>Skoracares Super Speciality Clinic delivers advanced clinical solutions with precision diagnostic facilities. Our multi-disciplinary team is committed to fast recovery, personalized consultations, and digital healthcare access.</p>',
                'timings' => 'Mon - Sun: 08:30 AM - 09:00 PM',
                'address' => 'Plot 42, Health City, Skoracares, New Delhi - 110001',
                'services' => "Internal Medicine\nNeurology & Spine Care\nDiabetes & Metabolism Management\nPhysiotherapy & Rehab\nEmergency Support",
                'clinic_rating' => 4.8,
                'booking_slots' => "08:30 AM\n09:00 AM\n09:30 AM\n10:00 AM\n10:30 AM\n11:00 AM\n11:30 AM\n04:00 PM\n04:30 PM\n05:00 PM\n05:30 PM\n06:00 PM\n06:30 PM\n07:00 PM\n07:30 PM\n08:00 PM",
                'booking_start_time' => '08:30',
                'booking_end_time' => '20:30',
                'slot_interval_minutes' => 30,
                'is_appointment_enabled' => true,
                'whatsapp_number' => '+91 9971000000',
                'notification_email' => 'support@skoracares.com',
                'status' => 'active',
            ]
        );

        $lp2->doctors()->delete();
        LandingPageDoctor::create([
            'landing_page_id' => $lp2->id,
            'doctor_name' => 'Dr. Rocky Kumar Singh',
            'email' => 'doctor@gmail.com',
            'specialization' => 'General Medicine & Critical Care Specialist',
            'consultation_fee' => '₹500',
            'experience' => '11 Years',
            'sort_order' => 1,
        ]);
        LandingPageDoctor::create([
            'landing_page_id' => $lp2->id,
            'doctor_name' => 'Dr. Meenakshi Iyer',
            'email' => 'dr.meenakshi@skoracares.com',
            'specialization' => 'Physiotherapist & Rehab Expert (MPT)',
            'consultation_fee' => '₹450',
            'experience' => '8 Years',
            'sort_order' => 2,
        ]);

        $lp2->testimonials()->delete();
        LandingPageTestimonial::create([
            'landing_page_id' => $lp2->id,
            'patient_name' => 'Amitabh Sen',
            'rating' => 5,
            'story' => 'Dr. Rocky provided immediate relief with his accurate diagnosis. Booking slot online took less than a minute!',
            'sort_order' => 1,
        ]);


        // 3. Dev Hospital Care
        $lp3 = LandingPage::updateOrCreate(
            ['slug' => 'dev-hospital-care'],
            [
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'clinic_name' => 'Dev Hospital Care',
                'about_clinic' => '<p>Dev Hospital Care offers top-notch medical facilities, 24/7 emergency response, in-house laboratory, and dedicated OPD services for patients across all age groups.</p>',
                'timings' => 'Mon - Sat: 10:00 AM - 07:00 PM',
                'address' => 'Main Road, Near Railway Station, Aurangabad, Bihar - 824101',
                'services' => "24x7 Emergency & Trauma\nGeneral & Laparoscopic Surgery\nPediatric & Neonatal Care\nOrthopedics & Fracture Clinic",
                'clinic_rating' => 4.7,
                'booking_slots' => "10:00 AM\n10:20 AM\n10:40 AM\n11:00 AM\n11:20 AM\n11:40 AM\n12:00 PM\n03:00 PM\n03:20 PM\n03:40 PM\n04:00 PM\n04:20 PM\n04:40 PM\n05:00 PM",
                'booking_start_time' => '10:00',
                'booking_end_time' => '17:00',
                'slot_interval_minutes' => 20,
                'is_appointment_enabled' => true,
                'whatsapp_number' => '+91 7766886760',
                'notification_email' => 'dev@gmail.com',
                'status' => 'active',
            ]
        );

        $lp3->doctors()->delete();
        LandingPageDoctor::create([
            'landing_page_id' => $lp3->id,
            'doctor_name' => 'Dr. Lovely Pal',
            'email' => 'dr.lovely@devhospital.com',
            'specialization' => 'Consultant Physician & Surgeon (MBBS, MS)',
            'consultation_fee' => '₹300',
            'experience' => '10 Years',
            'sort_order' => 1,
        ]);

        $lp3->testimonials()->delete();
        LandingPageTestimonial::create([
            'landing_page_id' => $lp3->id,
            'patient_name' => 'Ravi Prakash',
            'rating' => 5,
            'story' => 'Great experience at Dev Hospital. Consultation was thorough, and appointment confirmation arrived instantly.',
            'sort_order' => 1,
        ]);
    }
}
