{{-- Start Walk-In Consultation Modal (Screenshot 2) --}}
<div class="modal fade" id="walkinConsultationModal" tabindex="-1" aria-labelledby="walkinConsultationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            {{-- Yellowish Warm Header Bar --}}
            <div class="modal-header border-0 px-4 py-3" style="background: linear-gradient(135deg, #ffc72c 0%, #fdb913 100%);">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="walkinConsultationModalLabel">
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.85rem;"></button>
                    <span><i class="fa-solid fa-chevron-left me-1"></i> {{ __('labels.start_walk_in_consultation') }}</span>
                </h5>
            </div>
            
            <div class="modal-body p-4" style="background: #ffffff;">
                <label class="form-label fw-semibold text-secondary small mb-2">
                    {{ __('labels.patient_name_mobile_id') }}
                </label>
                
                {{-- Search Box --}}
                <div class="position-relative mb-4">
                    <i class="fa-solid fa-magnifying-glass position-absolute text-muted" style="left: 1rem; top: 50%; transform: translateY(-50%);"></i>
                    <input type="text"
                           id="walkinSearchInput"
                           class="form-control form-control-lg ps-5 pe-5 rounded-3 border-primary"
                           placeholder="{{ __('labels.patient_name_mobile_id') }}"
                           style="border-radius: 12px !important; font-size: 0.95rem;">
                    <button type="button" id="clearWalkinSearch" class="btn position-absolute end-0 top-50 translate-middle-y text-muted pe-3 border-0 bg-transparent" style="display: none;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                {{-- Patient Search Results List --}}
                <div id="walkinSearchResults" class="d-flex flex-column gap-3 mb-4" style="max-height: 280px; overflow-y: auto;">
                    {{-- Dynamically populated via JS --}}
                </div>

                {{-- Add New Patient Button --}}
                <div class="border-top pt-3 d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-outline-primary rounded-3 fw-bold px-4">
                        <i class="fa-solid fa-plus me-1"></i> {{ __('labels.add_new_patient') }}
                    </a>
                    <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">
                        {{ __('labels.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('walkinSearchInput');
    const clearBtn = document.getElementById('clearWalkinSearch');
    const resultsContainer = document.getElementById('walkinSearchResults');

    if (!input) return;

    function fetchPatients(query) {
        fetch("{{ route('admin.appointments.search-patients') }}?q=" + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                resultsContainer.innerHTML = '';
                if (!data || data.length === 0) {
                    resultsContainer.innerHTML = '<div class="p-3 text-center text-muted small">No matching patients found</div>';
                    return;
                }

                data.forEach(p => {
                    const row = document.createElement('div');
                    row.className = 'd-flex align-items-center justify-content-between p-3 border rounded-3 bg-light hover-shadow transition-all';
                    row.innerHTML = `
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                <i class="fa-regular fa-user"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">${p.name}</div>
                                <div class="small text-muted d-flex align-items-center gap-2 mt-1">
                                    <span><i class="fa-solid fa-phone me-1"></i>${p.phone}</span>
                                    <span>•</span>
                                    <span><i class="fa-regular fa-id-card me-1"></i>${p.patient_id_formatted}</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ url('users') }}/${p.id}" class="btn btn-sm btn-outline-secondary rounded-3 px-3 fw-semibold">
                                <i class="fa-regular fa-eye me-1"></i> ${'{{ __("labels.patient_details") }}'}
                            </a>
                            <button type="button" onclick="quickBookWalkin(${p.id}, '${p.name.replace(/'/g, "\\'")}', '${p.phone}')" class="btn btn-sm btn-primary rounded-3 px-3 fw-bold">
                                ${'{{ __("labels.consult") }}'} <i class="fa-solid fa-chevron-down ms-1"></i>
                            </button>
                        </div>
                    `;
                    resultsContainer.appendChild(row);
                });
            })
            .catch(err => console.error(err));
    }

    input.addEventListener('input', function() {
        const val = this.value.trim();
        clearBtn.style.display = val.length > 0 ? 'block' : 'none';
        fetchPatients(val);
    });

    clearBtn.addEventListener('click', function() {
        input.value = '';
        this.style.display = 'none';
        fetchPatients('');
    });

    // Initial load when modal opens
    const modal = document.getElementById('walkinConsultationModal');
    if (modal) {
        modal.addEventListener('shown.bs.modal', function () {
            fetchPatients('');
        });
    }
});

function quickBookWalkin(patientId, patientName, patientPhone) {
    const today = new Date().toISOString().split('T')[0];
    const now = new Date();
    let hours = now.getHours();
    let minutes = now.getMinutes();
    let ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // 0 is 12
    minutes = minutes < 10 ? '0' + minutes : minutes;
    const timeSlot = (hours < 10 ? '0' + hours : hours) + ':' + minutes + ' ' + ampm;

    fetch("{{ route('admin.appointments.store') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            doctor_id: {{ auth()->id() }},
            appointment_date: today,
            slot_time: timeSlot,
            patient_id: patientId,
            patient_name: patientName,
            patient_phone: patientPhone,
            visit_type: 'Walk-In'
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = "{{ url('appointments') }}/" + data.appointment.id;
        } else {
            alert(data.message || 'Booking failed');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Server error creating walk-in appointment');
    });
}
</script>
@endpush
