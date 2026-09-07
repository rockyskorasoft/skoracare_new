@extends('layouts.app')

@section('title', __('labels.select_appointment_slot'))

@push('styles')
    @vite(['resources/css/doctor/doctordashboard.css', 'resources/css/appointment.css'])
@endpush

@section('content')
    <div class="container-fluid p-0">

        {{-- Top Header (Screenshot 4) --}}
        <div class="slot-page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="slot-page-title">
                    <a href="{{ route('admin.appointments.index') }}" class="text-white text-decoration-none">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                    <span>{{ __('labels.select_appointment_slot') }}</span>
                </h3>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-light rounded-pill px-3 py-1.5 fw-bold text-dark border shadow-sm">
                        <i class="fa-regular fa-circle-play me-1 text-primary"></i> Tutorial
                    </button>
                    <button class="btn btn-primary rounded-3 px-3 py-1.5 fw-bold">
                        <i class="fa-regular fa-calendar-check me-1"></i> {{ __('labels.availability_settings') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- Slot Selection Box --}}
        <div class="slot-card-wrapper">

            {{-- Month & Nav --}}
            <div class="slot-month-header">
                <div class="slot-month-title">
                    {{ \Carbon\Carbon::parse($selectedDate)->format('F, Y') }}
                </div>
            </div>

            {{-- Date Carousel (Next 14 Days) --}}
            <div class="slot-date-carousel">
                @for ($i = 0; $i < 14; $i++)
                    @php
                        $loopDate = \Carbon\Carbon::today()->addDays($i);
                        $formattedLoop = $loopDate->format('Y-m-d');
                        $isActive = $formattedLoop === $selectedDate;
                    @endphp
                    <a href="{{ route('admin.appointments.slot-booking', ['date' => $formattedLoop, 'doctor_id' => $selectedDoctor->id]) }}"
                       class="slot-date-pill {{ $isActive ? 'active' : '' }}">
                        <span class="slot-date-num">{{ $loopDate->format('d') }}</span>
                        <span class="slot-date-day">{{ $loopDate->format('D') }}</span>
                    </a>
                @endfor
            </div>

            {{-- Doctor Selector Dropdown --}}
            <div class="mb-4" style="max-width: 300px;">
                <label class="form-label small fw-bold text-muted">Select Doctor</label>
                <select id="doctorSelector" class="form-select rounded-3 font-semibold" onchange="changeDoctor(this.value)">
                    @foreach ($doctors as $doc)
                        <option value="{{ $doc->id }}" {{ $doc->id == $selectedDoctor->id ? 'selected' : '' }}>
                            Dr. {{ $doc->first_name }} {{ $doc->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>


            {{-- Time of Day Tabs --}}
            <div class="slot-time-tabs">
                <div class="slot-time-tab" onclick="switchCategory('morning', this)">{{ __('labels.morning') }}</div>
                <div class="slot-time-tab" onclick="switchCategory('afternoon', this)">{{ __('labels.afternoon') }}</div>
                <div class="slot-time-tab active" onclick="switchCategory('evening', this)">{{ __('labels.evening') }}</div>
                <div class="slot-time-tab" onclick="switchCategory('night', this)">{{ __('labels.night') }}</div>
            </div>

            {{-- Slot Counter --}}
            <div class="fw-bold text-secondary mb-3 small" id="slotCounterText">
                Loading slots...
            </div>

            {{-- Grid of Time Slots --}}
            <div class="slot-grid" id="slotsGrid">
                {{-- Dynamically loaded via AJAX --}}
            </div>

        </div>

    </div>

    {{-- Confirm Appointment Side Drawer Overlay (Screenshot 5) --}}
    @include('appointments.partials.confirm-drawer')

@endsection

@push('scripts')
<script>
let currentCategory = 'evening';
let allCategorySlots = {};
let bookedSlots = [];
let expiredSlots = [];
let selectedSlotTime = '';
let selectedDateStr = '{{ $selectedDate }}';
let selectedDoctorId = '{{ $selectedDoctor->id }}';
let selectedDoctorName = 'Dr. {{ $selectedDoctor->first_name }} {{ $selectedDoctor->last_name }}';

document.addEventListener('DOMContentLoaded', function() {
    loadSlots();
});

function changeDoctor(docId) {
    selectedDoctorId = docId;
    loadSlots();
}

function loadSlots() {
    fetch(`{{ route('admin.appointments.ajax-slots') }}?date=${selectedDateStr}&doctor_id=${selectedDoctorId}`)
        .then(res => res.json())
        .then(data => {
            allCategorySlots = data.slots || {};
            bookedSlots = data.booked_slots || [];
            expiredSlots = data.expired_slots || [];
            renderSlots(currentCategory);
        })
        .catch(err => console.error(err));
}

function switchCategory(cat, element) {
    document.querySelectorAll('.slot-time-tab').forEach(el => el.classList.remove('active'));
    element.classList.add('active');
    currentCategory = cat;
    renderSlots(cat);
}

function renderSlots(cat) {
    const grid = document.getElementById('slotsGrid');
    const counter = document.getElementById('slotCounterText');
    grid.innerHTML = '';

    const slots = allCategorySlots[cat] || [];
    let availableCount = 0;

    if (slots.length === 0) {
        grid.innerHTML = '<div class="p-3 text-muted small col-12">{{ __("labels.no_slots_available") }}</div>';
        counter.textContent = '0 Slots Available';
        return;
    }

    slots.forEach(timeStr => {
        const isBooked = bookedSlots.includes(timeStr);
        const isExpired = expiredSlots.includes(timeStr);
        const isDisabled = isBooked || isExpired;

        if (!isDisabled) {
            availableCount++;
        }

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'slot-time-btn' + (isDisabled ? ' disabled' : '');

        if (isBooked) {
            btn.innerHTML = `${timeStr} <small style="display:block;font-size:0.65rem;color:#ef4444;margin-top:2px;">Booked</small>`;
        } else if (isExpired) {
            btn.innerHTML = `${timeStr} <small style="display:block;font-size:0.65rem;color:#94a3b8;margin-top:2px;">Expired</small>`;
        } else {
            btn.textContent = timeStr;
        }

        if (isDisabled) {
            btn.disabled = true;
        } else {
            btn.onclick = () => selectSlot(timeStr, btn);
        }

        grid.appendChild(btn);
    });

    counter.textContent = `${availableCount} Slots Available (${slots[0] || ''} to ${slots[slots.length - 1] || ''})`;
}

function selectSlot(timeStr, btn) {
    document.querySelectorAll('.slot-time-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    selectedSlotTime = timeStr;

    // Open Confirm Appointment Side Drawer (Screenshot 5)
    openConfirmDrawer();
}
</script>
@endpush
