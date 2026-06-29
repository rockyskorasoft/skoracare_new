import "toastr/build/toastr.min.css";
import toastr from "toastr";

export function resetForms(selector = ".ajaxClass, .filterId form") {
    $(selector).each(function () {
        this.reset();
    });
    $("form.ajaxClass")[0].reset();
    $(".error-message").remove();
}

export function showToastr(status, message) {
    if (status === "success") {
        toastr.success(message, "Success");
    } else {
        toastr.error(message || "An error occurred", "Error");
    }
}
