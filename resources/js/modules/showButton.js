import { resetForms } from "@utils/helpers";
import { sendRequest } from "@utils/ajax";
import Swal from "sweetalert2";

$(document).on("click", ".show-btn", async function () {
    const modalTitle = $(this).attr("modal-title");
    $(".modalTitle").text(modalTitle);
    let showUrl = $(this).attr("show-url");

    try {
        const response = await sendRequest({
            url: showUrl,
            method: "GET",
        });
        const data = response.data;
        $("[data-field]").each(function () {
            let field = $(this).data("field");
            $(this).text(data[field] ?? 'N/A');
        });
        // Trigger custom event for additional data handling (e.g., segments)
        $(document).trigger('showModalDataLoaded', [data]);
    } catch (error) {
        console.log('Error', error)
    }
    // Reset form when cancel or close button is clicked
    $(".closeButton, .cancelButton").click(function () {
        resetForms();
        $(".modalTitle").text(modalTitle);
    });
});

// Employee Status Toggle Handler
$(document).on("change", ".employee-status-toggle", async function (e) {
    const $toggle = $(this);
    const toggleUrl = $toggle.data("toggle-url");
    const employeeId = $toggle.data("employee-id");
    const currentStatus = $toggle.is(":checked");

    // If disabled, prevent the toggle
    if ($toggle.hasClass('disabled') || $toggle.is(':disabled')) {
        e.preventDefault();
        return false;
    }

    // Revert the toggle immediately (we'll update it after confirmation)
    $toggle.prop('checked', !currentStatus);

    // Show confirmation dialog
    const result = await Swal.fire({
        title: showTitle,
        text: `Do you want to ${currentStatus ? 'activate' : 'in-activate'} this employee?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, change it!',
        cancelButtonText: 'Cancel'
    });

    if (result.isConfirmed) {
        try {
            // Send the toggle request
            const response = await sendRequest({
                url: toggleUrl,
                method: "POST",
                data: {
                    id: employeeId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                }
            });
            if (response.status == 'success') {
                // Update the toggle to the new state
                $toggle.prop('checked', currentStatus);

                // Update the label text
                const label = $toggle.next('label');
                label.text(currentStatus ? 'Active' : 'Inactive');

                // Show success message
                await Swal.fire({
                    title: 'Success!',
                    text: response.message || 'Status updated successfully',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });

                // Reload the DataTable if it exists
                if ($.fn.DataTable && $.fn.DataTable.isDataTable('#employees')) {
                    $('#employees').DataTable().ajax.reload(null, false);
                }
            } else {
                throw new Error(response.message || 'Failed to update status');
            }
        } catch (error) {
            // Show error message
            await Swal.fire({
                title: 'Error!',
                text: error.message || 'Failed to update status. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    }
});
