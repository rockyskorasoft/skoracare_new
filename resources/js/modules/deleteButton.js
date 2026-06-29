import { sendRequest } from "@utils/ajax";
import Swal from "sweetalert2";

$(document).on("click", ".delete-btn", function (event) {
    event.preventDefault();

    const form = $(this).closest("form");
    const actionUrl = form.attr("action");

    Swal.fire({
        title: delete_modal_title,
        text: delete_modal_text,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: confirm_button_modal,
        cancelButtonText: cancel,
        customClass: {
            title: "delete-modal-title",
            icon: "warning-icon mt-0",
            confirmButton:
                "confirm-button-class border-primary btn btn-primary px-4 fw-semibold",
            cancelButton:
                "cancel-button-class px-4 btn btn-secondary fw-semibold",
            popup: "rounded-3 py-8",
        },
        didRender: function () {
            $(".swal2-html-container").addClass("py-2");
        },
    }).then((result) => {
        if (result.isConfirmed) {
            sendRequest({
                url: actionUrl,
                method: "POST",
                data: form.serialize(),
            })
            .then(() => {
                // JSON success response (e.g. roles controller)
                window.location.reload();
            })
            .catch((error) => {
                if (error.xhr === null) {
                    // Server returned a redirect (non-JSON) — delete succeeded, reload
                    window.location.reload();
                } else {
                    // Actual HTTP error (4xx / 5xx)
                    Swal.fire("Error!", error.error || "Something went wrong.", "error");
                }
            });
        }
    });
});
