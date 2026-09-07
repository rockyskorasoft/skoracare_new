<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    /**
     * Display the appointment queue dashboard (Screenshot 1).
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $dateParam = $request->get('date', date('Y-m-d'));
        $currentTab = $request->get('tab', 'queue');
        $search = $request->get('search', '');

        $query = Appointment::with(['patient', 'doctor', 'clinic']);

        // Filter by doctor if user is doctor, else all
        if ($user->hasRole(config('constants.doctor_role_name'))) {
            $query->where('doctor_id', $user->id);
        }

        // Active clinic session filter
        $activeClinicId = session('active_clinic_id');
        if ($activeClinicId) {
            $query->where('clinic_id', $activeClinicId);
        }

        // Date filter
        $query->whereDate('appointment_date', $dateParam);

        // Search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('patient_name', 'like', "%{$search}%")
                  ->orWhere('patient_phone', 'like', "%{$search}%")
                  ->orWhere('appointment_number', 'like', "%{$search}%");
            });
        }

        // Counts per status
        $baseCountQuery = Appointment::query();
        if ($user->hasRole(config('constants.doctor_role_name'))) {
            $baseCountQuery->where('doctor_id', $user->id);
        }
        if ($activeClinicId) {
            $baseCountQuery->where('clinic_id', $activeClinicId);
        }
        $baseCountQuery->whereDate('appointment_date', $dateParam);

        $counts = [
            'queue' => (clone $baseCountQuery)->where('status', 'queue')->count(),
            'draft' => (clone $baseCountQuery)->where('status', 'draft')->count(),
            'finished' => (clone $baseCountQuery)->where('status', 'finished')->count(),
            'cancelled' => (clone $baseCountQuery)->where('status', 'cancelled')->count(),
            'referral' => (clone $baseCountQuery)->where('status', 'referral')->count(),
        ];

        // Filter appointments for current tab
        $appointments = (clone $query)->where('status', $currentTab)->latest()->paginate(15);

        $doctors = User::role(config('constants.doctor_role_name'))->get();
        $clinics = Clinic::all();

        return view('appointments.index', compact(
            'appointments',
            'counts',
            'currentTab',
            'dateParam',
            'search',
            'doctors',
            'clinics'
        ));
    }

    /**
     * Display the Slot Booking page (Screenshot 4).
     */
    public function slotBooking(Request $request)
    {
        $user = auth()->user();
        $selectedDate = $request->get('date', date('Y-m-d'));
        $doctorId = $request->get('doctor_id', $user->id);

        $doctors = User::role(config('constants.doctor_role_name'))->get();
        $selectedDoctor = User::find($doctorId) ?? $user;

        return view('appointments.slot-booking', compact('selectedDate', 'doctors', 'selectedDoctor'));
    }

    /**
     * AJAX Get Available Slots by time-of-day category.
     */
    public function getSlots(Request $request): JsonResponse
    {
        $date = $request->get('date', date('Y-m-d'));
        $doctorId = $request->get('doctor_id');

        // Sample slots categorized by time of day
        $allSlots = [
            'morning' => ['09:00 AM', '09:15 AM', '09:30 AM', '09:45 AM', '10:00 AM', '10:15 AM', '10:30 AM', '10:45 AM', '11:00 AM', '11:15 AM', '11:30 AM', '11:45 AM'],
            'afternoon' => ['12:00 PM', '12:15 PM', '12:30 PM', '12:45 PM', '02:00 PM', '02:15 PM', '02:30 PM', '02:45 PM', '03:00 PM', '03:15 PM', '03:30 PM', '03:45 PM'],
            'evening' => ['05:00 PM', '05:10 PM', '05:20 PM', '05:30 PM', '05:40 PM', '05:50 PM', '06:00 PM', '06:10 PM', '06:20 PM', '06:30 PM', '06:40 PM', '06:50 PM', '07:00 PM', '07:10 PM', '07:20 PM', '07:30 PM', '07:40 PM', '07:50 PM', '08:00 PM', '08:10 PM', '08:20 PM', '08:30 PM', '08:40 PM', '08:50 PM'],
            'night' => ['09:00 PM', '09:15 PM', '09:30 PM', '09:45 PM', '10:00 PM'],
        ];

        // Fetch already booked slots for this doctor and date
        $bookedSlots = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->whereIn('status', ['queue', 'draft', 'finished', 'referral'])
            ->pluck('slot_time')
            ->toArray();

        // Calculate expired slots if date is today or past
        $expiredSlots = [];
        $tz = config('app.timezone', 'Asia/Kolkata');
        $now = Carbon::now($tz);
        $todayStr = $now->format('Y-m-d');

        foreach ($allSlots as $category => $slots) {
            foreach ($slots as $slotStr) {
                if ($date < $todayStr) {
                    $expiredSlots[] = $slotStr;
                } elseif ($date === $todayStr) {
                    try {
                        $slotDateTime = Carbon::parse("{$date} {$slotStr}", $tz);
                        if ($slotDateTime->isPast()) {
                            $expiredSlots[] = $slotStr;
                        }
                    } catch (\Exception $e) {
                        // ignore parse errors
                    }
                }
            }
        }

        return response()->json([
            'slots' => $allSlots,
            'booked_slots' => $bookedSlots,
            'expired_slots' => array_values(array_unique($expiredSlots)),
            'date_formatted' => Carbon::parse($date)->format('d M Y'),
        ]);
    }

    /**
     * AJAX Search Patients by name, phone or ID (Screenshot 2 & Screenshot 5).
     */
    public function searchPatients(Request $request): JsonResponse
    {
        $q = trim($request->get('q', ''));

        if (empty($q)) {
            $patients = User::role(config('constants.patient_role_name'))->limit(10)->get();
        } else {
            $patients = User::role(config('constants.patient_role_name'))
                ->where(function ($query) use ($q) {
                    $query->where('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone_no', 'like', "%{$q}%")
                        ->orWhere('id', 'like', "%{$q}%");
                })
                ->limit(10)
                ->get();
        }

        $results = $patients->map(function ($p) {
            $fullName = trim("{$p->first_name} {$p->last_name}");
            $gender = $p->gender ?? 'Male';
            $age = $p->age ? "{$p->age}y" : '30y';
            $patId = 'PAT' . str_pad($p->id, 4, '0', STR_PAD_LEFT);

            return [
                'id' => $p->id,
                'name' => $fullName ?: $p->email,
                'phone' => $p->phone_no ?? '7766886760',
                'email' => $p->email,
                'gender' => $gender,
                'age' => $age,
                'patient_id_formatted' => $patId,
                'label' => "Mr {$fullName} ({$gender}, {$age}) - " . ($p->phone_no ?? '') . " - {$patId}",
            ];
        });

        return response()->json($results);
    }

    /**
     * Store new Appointment (Screenshot 5 Confirm Appointment submit).
     */
    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date',
            'slot_time' => 'required|string',
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'required|string|max:20',
        ]);

        $activeClinicId = session('active_clinic_id');

        // Generate unique appointment number
        $lastId = Appointment::max('id') ?? 0;
        $appointmentNumber = 'APT' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);

        $appointment = Appointment::create([
            'appointment_number' => $appointmentNumber,
            'clinic_id' => $activeClinicId,
            'doctor_id' => $request->doctor_id,
            'patient_id' => $request->patient_id ?: null,
            'patient_name' => $request->patient_name,
            'patient_phone' => $request->patient_phone,
            'patient_email' => $request->patient_email,
            'gender' => $request->gender ?? 'Male',
            'age' => $request->age ?? '30',
            'appointment_date' => $request->appointment_date,
            'slot_time' => $request->slot_time,
            'visit_type' => $request->visit_type ?? 'Walk-In',
            'status' => 'queue',
            'remarks' => $request->remarks,
            'created_by' => auth()->id(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('labels.appointment_booked_success'),
                'appointment' => $appointment,
            ]);
        }

        return redirect()->route('admin.appointments.index')
            ->with('message', __('labels.appointment_booked_success'));
    }

    /**
     * AJAX Update status of appointment (queue, finished, cancelled, etc.).
     */
    public function updateStatus(Request $request, Appointment $appointment): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:queue,draft,finished,cancelled,referral',
        ]);

        $appointment->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('labels.appointment_status_updated'),
        ]);
    }

    /**
     * Display Patient Details / Visit Summary (Screenshot 3).
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor', 'clinic']);
        return view('appointments.show', compact('appointment'));
    }
}
