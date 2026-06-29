<div>
    <div class="modal fade baseModal" tabindex="-1" aria-labelledby="baseModalLabel" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-5 overflow-hidden">
                <div class="modal-header px-5 py-4">
                    <h3 class="modal-title fw-semibold modalTitle text-primary px-1 fs-4" id="baseModalLabel"></h3>
                    <button type="button" class="btn-close closeButton" data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>
                </div>
                <div class="modal-body bg-middark p-5">
                    <div class="p-4 overflow-y-scroll bg-white modal-form-block rounded-2">
                            {{ $slot }}
                            <div class="p-0 pt-3 pb-0 mt-3 modal-footer justify-content-start">
                                <button type="submit" class="btn btn-primary btn-text">
                                </button>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
