<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\LandingPage;
use Illuminate\Http\Request;

class PublicLandingPageController extends Controller
{
    /**
     * Show the public-facing landing page.
     */
    public function show(string $slug)
    {
        $landingPage = LandingPage::where('slug', $slug)
            ->where('status', 'active')
            ->with(['doctors', 'testimonials', 'gallery'])
            ->firstOrFail();

        // Build booking slots array from the stored text (one per line)
        $slots = [];
        if ($landingPage->booking_slots) {
            $rawSlots = preg_replace('/<\s*(?:br|p|li)[^>]*>/i', "\n", $landingPage->booking_slots);
            $cleanSlots = strip_tags(html_entity_decode($rawSlots, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $slots = array_values(array_filter(array_map('trim', explode("\n", $cleanSlots))));
        }

        // Build services list safely with HTML tags stripped
        $services = [];
        if ($landingPage->services) {
            $rawServices = preg_replace('/<\s*(?:br|p|li)[^>]*>/i', "\n", $landingPage->services);
            $cleanServices = strip_tags(html_entity_decode($rawServices, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $services = array_values(array_filter(array_map('trim', explode("\n", $cleanServices))));
        }

        return view('public.landing-page', compact('landingPage', 'slots', 'services'));
    }

    /**
     * Handle appointment booking from the public landing page.
     * Stores into the existing appointments table.
     */
    public function book(Request $request, string $slug)
    {
        $landingPage = LandingPage::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        // Only accept bookings if appointment booking is enabled for this landing page
        if (!$landingPage->is_appointment_enabled) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Appointment booking is currently disabled.'], 403);
            }
            return back()->withErrors(['error' => 'Appointment booking is currently disabled.']);
        }

        $request->validate([
            'patient_name'     => 'required|string|max:255',
            'patient_phone'    => 'required|string|max:20',
            'patient_email'    => 'nullable|email|max:255',
            'gender'           => 'nullable|in:Male,Female,Other',
            'age'              => 'nullable|integer|min:0|max:150',
            'appointment_date' => 'required|date|after_or_equal:today',
            'slot_time'        => 'required|string',
            'doctor_name'      => 'nullable|string|max:255',
            'remarks'          => 'nullable|string',
        ]);

        // Generate unique appointment number
        $lastId            = Appointment::max('id') ?? 0;
        $appointmentNumber = 'APT' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);

        Appointment::create([
            'appointment_number' => $appointmentNumber,
            'clinic_id'          => null,  // Public bookings have no session clinic
            'doctor_id'          => null,  // No system doctor linked from public page
            'patient_id'         => null,
            'patient_name'       => $request->patient_name,
            'patient_phone'      => $request->patient_phone,
            'patient_email'      => $request->patient_email,
            'gender'             => $request->gender ?? 'Male',
            'age'                => $request->age ?? 0,
            'appointment_date'   => $request->appointment_date,
            'slot_time'          => $request->slot_time,
            'visit_type'         => 'Walk-In',
            'status'             => 'queue',
            'remarks'            => $request->remarks . ($request->doctor_name ? ' | Doctor: ' . $request->doctor_name : ''),
            'created_by'         => $landingPage->created_by,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Your appointment has been booked successfully! We will contact you to confirm.',
            ]);
        }

        return back()->with('booking_success', 'Your appointment has been booked successfully! We will contact you to confirm.');
    }
}
