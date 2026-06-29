import { resetForms } from "@utils/helpers";
import { sendRequest } from "@utils/ajax";

$(document).on("click", ".edit-btn", async function () {
    const modalTitle = $(this).attr("modal-title");
    $(".modalTitle").text(modalTitle);
    $(".status-field").removeClass("d-none");
    $(".select-status").prop("disabled", false);
    $(".btn-text").text(update);
    $(".password-field").addClass("d-none");
    $(".confirm-password-field").addClass("d-none");
    $(".dynamic-required").removeClass("required");
    const url = $(this).attr("form-url");
    const userId = $(this).attr("data-employee-id");
    $(".data-employee-id").val(userId);
    const method = $(this).data("method") || "PUT";
    const editUrl = $(this).attr("edit-url");

    const formId = modalTitle.replace(/\s+/g, "-").toLowerCase();
    const $form = $("form.ajaxClass");
    $form.attr("id", formId);
    $form.attr("action", url).data("method", method);

    try {
        const response = await sendRequest({
            url: editUrl,
            method: "GET",
        });
        
        const data = response.data;

        $form.find("input, select, textarea").each(function () {
            const $input = $(this);
            const inputName = $input.attr("name");
            let inputValue = null;
            
            if (data && data.hasOwnProperty(inputName)) {
                inputValue = data[inputName];
            }
            else if (inputName === "name") {
                if(response.data["user_document_image"]){
                        inputValue = response.data["user_document_image"].name;
                }
            }
            if (inputValue !== null) {
                if (
                    $input.is(
                        'input[type="text"], input[type="number"], input[type="date"], input[type="time"], input[type="email"], input[type="password"], input[type="datetime-local"]'
                    )
                ) {
                    
                    $input.val(inputValue);
                } else if ($input.is("select")) {
                    
                    $input.val(inputValue).trigger("change");
                } else if ($input.is("textarea")) {
                    
                    $input.val(inputValue);
                }

                if ($input.is('input[type="file"]')) {
                   
                    if (inputValue) {
                                let previewImg = document.getElementById('documentFile');
                                let documentFile =  documentImagePath + '/' + inputValue; 
                                if (previewImg) {
                                    previewImg.href = documentFile;  // file URL from your response
                                    previewImg.textContent = 'View Document';
                                }
                    }
                }

                // Format datetime-local input
                if ($input.is('input[type="datetime-local"]')) {
                    let formattedDate = "";
                    if (inputValue) {
                        let parts = inputValue.split(/[- :]/);
                        if (parts.length >= 5) {
                            let [dd, mm, yyyy, hh, min] = parts;
                            formattedDate = `${yyyy}-${mm}-${dd}T${hh}:${min}`;
                        }
                        $input.val(formattedDate);
                    }
                }
            }
        });

    } catch (err) {
        console.error("Error fetching data:", err.error || err);
    }
    $(".closeButton, .cancelButton").click(function () {
        resetForms();
        $(".modalTitle").text(modalTitle);
        $(".btn-text").text(updateText);
    });
});
