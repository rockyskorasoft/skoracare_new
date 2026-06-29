<div class="modal fade" id="showBaseModal" tabindex="-1" aria-labelledby="showModalLabel"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 overflow-hidden">
            <div class="modal-header px-4 py-3">
                <h5 class="modal-title fw-semibold modalTitle text-primary"></h5>
                <button type="button" class="btn-close closeButton" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="modal-detail-body">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
