$('.password-toggle-group .toggle-password-btn').on('click', function () {
    const group = $(this).closest('.password-toggle-group');
    const input = group.find('input');
    const icon = $(this).find('i');

    if (input.attr('type') === 'password') {
        input.attr('type', 'text');
        icon.removeClass('bi-eye-fill').addClass('bi-eye-slash-fill');
    } else {
        input.attr('type', 'password');
        icon.removeClass('bi-eye-slash-fill').addClass('bi-eye-fill');
    }
});

