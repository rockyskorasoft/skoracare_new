import 'bootstrap';
import '../css/auth.css';

window.togglePassword = function (id, button) {
    const input = document.getElementById(id);
    if (!input) {
        return;
    }

    const icon = button.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        if (icon) {
            icon.classList.remove('bi-eye-fill');
            icon.classList.add('bi-eye-slash-fill');
        }
    } else {
        input.type = 'password';
        if (icon) {
            icon.classList.remove('bi-eye-slash-fill');
            icon.classList.add('bi-eye-fill');
        }
    }
};

