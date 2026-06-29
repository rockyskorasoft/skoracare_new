import { resetForms } from '@utils/helpers';

$(document).on("click", ".add-btn", async function () {
    const tableId = $(this).attr("table-id");
    const url = $(this).attr("form-url");
    const modalTitle = $(this).attr("modal-title") || "Default Title";
    $(".profile-image").addClass("d-none");
    $(".modalTitle").text(modalTitle);
    $(".status-field").addClass("d-none");
    // Disable status field on create
    $(".select-status").prop("disabled", true);
    $(".dynamic-required").addClass("required");
    let formId = modalTitle.replace(/\s+/g, "-").toLowerCase();
    $("form.ajaxClass").attr("id", formId); // Set the form ID dynamically
    // Set the action URL and table ID as form attributes
    $("form.ajaxClass").attr("table-id", tableId);
    $("form.ajaxClass").attr("action", url);
    // Reset form method and fields
    $("form.ajaxClass").data("method", "POST");
    resetForms();
    $(".btn-text").text(create);
    $(".responseMessage").html("");
     $(".closeButton").click(function () {
        resetForms();
        $(".modalTitle").text(modalTitle);
        $(".btn-text").text(create);
    });
});