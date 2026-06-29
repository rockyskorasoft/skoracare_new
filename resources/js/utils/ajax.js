/**
 * Common AJAX request handler using jQuery + Promises
 * @param {Object} config - jQuery-compatible config
 * @param {string} config.url - Request URL
 * @param {string} [config.method='GET'] - HTTP method
 * @param {Object|FormData} [config.data={}] - Data to send
 * @param {Object} [config.headers={}] - Custom headers
 * @param {boolean} [config.processData=true] - jQuery processData flag
 * @param {string|boolean} [config.contentType='application/x-www-form-urlencoded'] - Content-Type header
 * @returns {Promise<Object>} - Resolves with { success, data } or rejects with { success, error, errors, xhr }
 */
export function sendRequest({
    url,
    method = 'GET',
    data = {},
    headers = {},
    processData = true,
    contentType = 'application/x-www-form-urlencoded',
}) {
    const defaultHeaders = {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    };

    return new Promise((resolve, reject) => {
        $.ajax({
            url,
            type: method,
            data,
            processData,
            contentType,
            headers: { ...defaultHeaders, ...headers },
            success: (response) => {
                if (response.status && response.status.toLowerCase() === "success") {
                    resolve(response);
                } else {
                    reject({
                        success: false,
                        error: response.message || "Server returned error",
                        errors: response.errors || null,
                        xhr: null
                    });
                }
            },
            error: (xhr) => {
                let message = error_message;
                try {
                    if (xhr.responseJSON?.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        const parsed = JSON.parse(xhr.responseText);
                        message = parsed.message || xhr.responseText;
                    }
                } catch (_) {
                    message = xhr.responseText || message;
                }

                reject({
                    success: false,
                    error: message,
                    errors: xhr.responseJSON?.errors || null,
                    xhr
                });
            }
        });
    });
}
