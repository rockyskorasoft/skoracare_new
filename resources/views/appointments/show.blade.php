@extends('layouts.app')

@section('title', __('labels.patient_details'))

@push('styles')
    @vite(['resources/css/doctor/doctordashboard.css', 'resources/css/appointment.css'])
@endpush

@section('content')
    <div class="container-fluid p-0">

        {{-- Patient Banner (Screenshot 3) --}}
        <div class="pat-header-banner">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('admin.appointments.index') }}" class="btn btn-sm btn-light rounded-circle p-2 shadow-sm">
                        <i class="fa-solid fa-chevron-left text-dark"></i>
                    </a>
                    <div>
                        <h3 class="fw-bold mb-0 text-dark">
                            {{ $appointment->patient_name }}
                        </h3>
                        <div class="small text-secondary fw-semibold mt-1">
                            {{ $appointment->gender }}, {{ $appointment->age ? $appointment->age . 'y' : '30y' }}
                            • <i class="fa-solid fa-phone ms-2 me-1"></i> {{ $appointment->patient_phone }}
                            • <span class="badge bg-dark text-white ms-2">{{ $appointment->appointment_number }}</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-light rounded-pill px-3 py-1.5 fw-bold border shadow-sm">
                        <i class="fa-regular fa-circle-play me-1 text-primary"></i> Tutorial
                    </button>
                    <button class="btn btn-light rounded-pill px-3 py-1.5 fw-bold border shadow-sm">
                        <i class="fa-solid fa-rotate-left me-1 text-primary"></i> Repeat Rx
                    </button>
                    <button class="btn btn-primary rounded-3 px-4 py-1.5 fw-bold">
                        {{ __('labels.consult') }} <i class="fa-solid fa-chevron-down ms-1"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Content Grid --}}
        <div class="row g-4 position-relative" style="z-index: 10;">
            
            {{-- Left Sub Sidebar (Screenshot 3) --}}
            <div class="col-lg-3">
                <div class="pat-card">
                    <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5" style="width: 48px; height: 48px;">
                            {{ strtoupper(substr($appointment->patient_name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="fw-bold text-dark fs-6">{{ $appointment->patient_name }}</div>
                            <div class="small text-muted">{{ $appointment->gender }}, {{ $appointment->age ? $appointment->age . 'y' : '30y' }}</div>
                        </div>
                    </div>

                    <div class="pat-sidebar-nav">
                        <a href="#" class="pat-sidebar-link active">
                            <i class="fa-solid fa-file-medical text-primary"></i> Visit Summary
                        </a>
                        <a href="#" class="pat-sidebar-link">
                            <i class="fa-solid fa-award"></i> Certificate
                        </a>
                        <a href="#" class="pat-sidebar-link">
                            <i class="fa-regular fa-folder-open"></i> Medical Records
                        </a>
                        <a href="#" class="pat-sidebar-link">
                            <i class="fa-solid fa-file-invoice-dollar"></i> Add Bill / Payment
                        </a>
                        <a href="#" class="pat-sidebar-link">
                            <i class="fa-solid fa-pen-ruler"></i> Canvases
                        </a>
                    </div>
                </div>
            </div>

            {{-- Middle Column: Medical History Card --}}
            <div class="col-lg-4">
                <div class="pat-card">
                    <h5 class="fw-bold text-dark d-flex align-items-center gap-2 mb-4">
                        <i class="fa-solid fa-book-medical text-primary"></i> Medical History
                    </h5>

                    <div class="text-center py-5 text-muted">
                        <i class="fa-solid fa-heart-pulse fs-1 mb-3 text-secondary opacity-50"></i>
                        <p class="mb-0 fw-semibold">No Medical History saved for the patient!</p>
                    </div>
                </div>
            </div>

            {{-- Right Column: Prescription / Summary Details (Screenshot 3) --}}
            <div class="col-lg-5">
                <div class="pat-card">
                    <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
                        <div>
                            <h6 class="fw-bold text-dark mb-0">
                                Dr. {{ $appointment->doctor->first_name ?? 'Anil' }} {{ $appointment->doctor->last_name ?? 'Chauhan' }} | Physiotherapy
                            </h6>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y') }}, {{ $appointment->slot_time }}</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-secondary rounded-3"><i class="fa-solid fa-print"></i></button>
                            <button class="btn btn-sm btn-outline-secondary rounded-3"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                        </div>
                    </div>

                    {{-- Symptoms --}}
                    <div class="mb-4">
                        <div class="fw-bold text-secondary small d-flex align-items-center gap-2 mb-1">
                            <i class="fa-solid fa-virus text-primary"></i> Symptoms
                        </div>
                        <div class="fw-semibold text-dark ps-4">Xanthelasma :</div>
                    </div>

                    {{-- Examinations --}}
                    <div class="mb-4">
                        <div class="fw-bold text-secondary small d-flex align-items-center gap-2 mb-1">
                            <i class="fa-solid fa-stethoscope text-primary"></i> Examinations
                        </div>
                        <div class="fw-semibold text-dark ps-4">Zoster :</div>
                    </div>

                    {{-- Medication Table (Screenshot 3) --}}
                    <div class="mb-4">
                        <div class="fw-bold text-secondary small d-flex align-items-center gap-2 mb-2">
                            <i class="fa-solid fa-pills text-primary"></i> Medication (Rx)
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle rx-table mb-0">
                                <thead>
                                    <tr>
                                        <th>S.NO</th>
                                        <th>MEDICINE</th>
                                        <th>DOSE</th>
                                        <th>FREQUENCY</th>
                                        <th>DURATION</th>
                                        <th>QTY</th>
                                        <th>NOTES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-bold text-muted">1</td>
                                        <td>
                                            <div class="fw-bold text-dark">1 AL M Tab</div>
                                            <small class="text-muted">Levocetirizine (5mg) + Montelukast (10mg)</small>
                                        </td>
                                        <td>1 Tablet(s)</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Advice --}}
                    <div class="mb-4">
                        <div class="fw-bold text-secondary small d-flex align-items-center gap-2 mb-1">
                            <i class="fa-solid fa-plus text-primary"></i> Advice
                        </div>
                        <div class="fw-semibold text-dark ps-4">1 glass of milk after breakfast</div>
                    </div>

                    {{-- Doctor Note --}}
                    <div class="mb-4">
                        <div class="fw-bold text-secondary small d-flex align-items-center gap-2 mb-1">
                            <i class="fa-regular fa-note-sticky text-primary"></i> Doctor Note
                        </div>
                        <div class="fw-semibold text-dark ps-4">{{ $appointment->remarks ?: 'Initial consultation completed.' }}</div>
                    </div>

                    {{-- Follow up --}}
                    <div class="mb-3">
                        <div class="fw-bold text-secondary small d-flex align-items-center gap-2 mb-1">
                            <i class="fa-regular fa-calendar-check text-primary"></i> Follow-up: <span class="text-dark fw-bold ms-1">08/04/2026</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
