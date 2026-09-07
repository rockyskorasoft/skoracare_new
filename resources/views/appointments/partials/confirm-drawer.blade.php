{{-- Confirm Appointment Side Drawer Overlay (Screenshot 5) --}}
<div class="confirm-drawer-backdrop" id="confirmBackdrop" onclick="closeConfirmDrawer()"></div>

<div class="confirm-drawer" id="confirmDrawer">
    <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-white">
        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <button type="button" class="btn-close text-reset me-1" onclick="closeConfirmDrawer()"></button>
            <span>{{ __('labels.confirm_appointment') }}</span>
        </h5>
        <button type="button" id="submitBookAptBtn" onclick="submitBooking()" class="btn btn-primary rounded-3 px-3 py-2 fw-bold" style="background: #6366f1; border: none;">
            {{ __('labels.book_appointment') }}
        </button>
    </div>

    <div class="p-4 flex-grow-1 overflow-y-auto bg-light">
        <form id="bookingForm">
            @csrf
            <input type="hidden" name="doctor_id" id="formDoctorId">
            <input type="hidden" name="appointment_date" id="formAptDate">
            <input type="hidden" name="slot_time" id="formSlotTime">
            <input type="hidden" name="patient_id" id="formPatientId">

            {{-- Selected Doctor Card --}}
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-body p-3 d-flex align-items-center justify-content-between bg-white rounded-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i class="fa-solid fa-user-doctor"></i>
                        </div>
                        <div class="fw-bold text-dark" id="displayDoctorName">Dr. Anil Chauhan</div>
                    </div>
                    <i class="fa-solid fa-pen text-primary cursor-pointer" onclick="closeConfirmDrawer()"></i>
                </div>
            </div>

            {{-- Selected Slot Card --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-3 d-flex align-items-center justify-content-between bg-white rounded-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i class="fa-regular fa-clock"></i>
                        </div>
                        <div class="fw-bold text-dark" id="displaySlotTime">07:30 PM (Today) | 24th Aug 2026</div>
                    </div>
                    <i class="fa-solid fa-pen text-primary cursor-pointer" onclick="closeConfirmDrawer()"></i>
                </div>
            </div>

            {{-- Patient Search & Input --}}
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">
                    Patient Name, Mobile no & ID <span class="text-danger">*</span>
                </label>
                <div class="position-relative">
                    <i class="fa-solid fa-magnifying-glass position-absolute text-muted" style="left: 1rem; top: 50%; transform: translateY(-50%);"></i>
                    <input type="text"
                           id="confirmPatientSearch"
                           class="form-control form-control-lg ps-5 rounded-3 border-primary"
                           placeholder="Search by Patient's Name, Phone number or Id"
                           required
                           style="border-radius: 12px !important; font-size: 0.9rem;">
                </div>

                {{-- Live Search Results dropdown --}}
                <div id="confirmSearchResults" class="list-group position-absolute shadow-lg w-100 mt-1" style="z-index: 1000; display: none; max-height: 200px; overflow-y: auto;">
                    {{-- Dynamically populated --}}
                </div>
            </div>

            {{-- Selected Patient Details Card --}}
            <div id="selectedPatientCard" class="card border-primary border-opacity-25 bg-primary bg-opacity-10 rounded-3 mb-3 p-3" style="display: none;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fw-bold text-dark" id="selPatName"></div>
                        <div class="small text-muted" id="selPatPhone"></div>
                    </div>
                    <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="clearSelectedPatient()">&times; Change</button>
                </div>
            </div>

            {{-- Manual patient inputs if not selected --}}
            <div id="manualPatientInputs" class="mb-3">
                <div class="row g-2">
                    <div class="col-6">
                        <input type="text" name="patient_name" id="formPatientName" class="form-control rounded-3" placeholder="Full Name *" required>
                    </div>
                    <div class="col-6">
                        <input type="text" name="patient_phone" id="formPatientPhone" class="form-control rounded-3" placeholder="Mobile No *" required>
                    </div>
                </div>
            </div>

            {{-- Add New Patient Pill --}}
            <div class="mb-4">
                <a href="{{ route('admin.users.create') }}" class="btn btn-outline-primary rounded-pill px-3 py-1.5 fw-bold text-decoration-none small">
                    <i class="fa-solid fa-plus me-1"></i> {{ __('labels.add_new_patient') }}
                </a>
            </div>

            {{-- Remarks Textarea --}}
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">{{ __('labels.remarks') }}</label>
                <textarea name="remarks" class="form-control rounded-3" rows="3" placeholder="{{ __('labels.write_remarks_placeholder') }}"></style="border-radius: 12px;"></textarea>
            </div>
        </form>
    </div>
</div>

<script>
function openConfirmDrawer() {
    document.getElementById('formDoctorId').value = selectedDoctorId;
    document.getElementById('formAptDate').value = selectedDateStr;
    document.getElementById('formSlotTime').value = selectedSlotTime;

    document.getElementById('displayDoctorName').textContent = selectedDoctorName;
    document.getElementById('displaySlotTime').textContent = `${selectedSlotTime} | ${selectedDateStr}`;

    document.getElementById('confirmBackdrop').classList.add('show');
    document.getElementById('confirmDrawer').classList.add('open');
}

function closeConfirmDrawer() {
    document.getElementById('confirmBackdrop').classList.remove('show');
    document.getElementById('confirmDrawer').classList.remove('open');
}

document.addEventListener('DOMContentLoaded', function() {
    const pInput = document.getElementById('confirmPatientSearch');
    const results = document.getElementById('confirmSearchResults');

    if (!pInput) return;

    pInput.addEventListener('input', function() {
        const val = this.value.trim();
        if (val.length < 1) {
            results.style.display = 'none';
            return;
        }

        fetch("{{ route('admin.appointments.search-patients') }}?q=" + encodeURIComponent(val))
            .then(res => res.json())
            .then(data => {
                results.innerHTML = '';
                if (!data || data.length === 0) {
                    results.style.display = 'none';
                    return;
                }

                data.forEach(p => {
                    const a = document.createElement('a');
                    a.href = '#';
                    a.className = 'list-group-item list-group-item-action py-2';
                    a.innerHTML = `<strong>${p.name}</strong> (${p.phone}) - <small class="text-muted">${p.patient_id_formatted}</small>`;
                    a.onclick = (e) => {
                        e.preventDefault();
                        selectPatientForConfirm(p);
                    };
                    results.appendChild(a);
                });
                results.style.display = 'block';
            });
    });
});

function selectPatientForConfirm(p) {
    document.getElementById('formPatientId').value = p.id;
    document.getElementById('formPatientName').value = p.name;
    document.getElementById('formPatientPhone').value = p.phone;

    document.getElementById('selPatName').textContent = p.name;
    document.getElementById('selPatPhone').textContent = `${p.phone} • ${p.patient_id_formatted}`;

    document.getElementById('selectedPatientCard').style.display = 'block';
    document.getElementById('manualPatientInputs').style.display = 'none';
    document.getElementById('confirmSearchResults').style.display = 'none';
}

function clearSelectedPatient() {
    document.getElementById('formPatientId').value = '';
    document.getElementById('formPatientName').value = '';
    document.getElementById('formPatientPhone').value = '';
    document.getElementById('selectedPatientCard').style.display = 'none';
    document.getElementById('manualPatientInputs').style.display = 'block';
}

function submitBooking() {
    const doctor_id = document.getElementById('formDoctorId').value;
    const appointment_date = document.getElementById('formAptDate').value;
    const slot_time = document.getElementById('formSlotTime').value;
    const patient_id = document.getElementById('formPatientId').value;
    const patient_name = document.getElementById('formPatientName').value;
    const patient_phone = document.getElementById('formPatientPhone').value;
    const remarks = document.querySelector('textarea[name="remarks"]').value;

    if (!patient_name || !patient_phone) {
        alert('Please select or enter patient name and mobile number.');
        return;
    }

    const btn = document.getElementById('submitBookAptBtn');
    btn.disabled = true;
    btn.textContent = 'Booking...';

    fetch("{{ route('admin.appointments.store') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            doctor_id,
            appointment_date,
            slot_time,
            patient_id,
            patient_name,
            patient_phone,
            remarks,
            visit_type: 'Online'
        })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.textContent = '{{ __("labels.book_appointment") }}';
        if (data.success) {
            window.location.href = "{{ route('admin.appointments.index') }}";
        } else {
            alert(data.message || 'Booking failed');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.textContent = '{{ __("labels.book_appointment") }}';
        console.error(err);
        alert('Server error creating appointment');
    });
}
</script>
