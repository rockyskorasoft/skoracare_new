import { sendRequest } from '@utils/ajax';
import { resetForms, showToastr } from '@utils/helpers';

$(document).on("submit", ".ajaxClass", async function (event) {
    event.preventDefault();
    const $form = $(this);
    const url = $form.attr("action");
    const method = $form.data("method") || "POST";
    const formData = new FormData(this);
    if (["PUT", "PATCH"].includes(method)) {
        formData.append("_method", method);
    }
    $(".error-message").remove();
    try {
        const response = await sendRequest({
            url,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
        });
        resetForms();
        showToastr("success", response.message);
        if (response.data?.redirect_url) {
            sessionStorage.setItem("toastr_message", response.message);
            window.location.href = response.data.redirect_url;
        } else {
            setTimeout(() => location.reload(), 500);
        }

    } catch (error) {
        if (error.errors) {
            $.each(error.errors, function (field, messages) {
                const input = $(`[name="${field}"], [name="${field}[]"]`);
                if (input.length) {
                    const html = messages.map(msg =>
                        `<span class="error-message text-danger d-block mt-1">${msg}</span>`
                    ).join('');
                    input.after(html);
                }
            });
        }
        showToastr("error", error.error);
    }

});
