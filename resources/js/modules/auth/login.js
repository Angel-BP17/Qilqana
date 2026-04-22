export const LoginModule = {
    init: function() {
        const form = document.getElementById('loginForm');
        if (!form) return;

        this.setupPasswordToggle();
        this.setupValidation(form);
    },

    setupPasswordToggle: function() {
        const toggleBtn = document.getElementById('togglePassword');
        const input = document.getElementById('password');
        if (!toggleBtn || !input) return;

        toggleBtn.addEventListener('click', function() {
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            this.textContent = isPassword ? 'visibility' : 'visibility_off';
        });
    },

    setupValidation: function(form) {
        form.addEventListener('submit', (e) => {
            const dni = document.getElementById('dni');
            const pass = document.getElementById('password');
            let valid = true;

            if (!dni.value || !/^\d{8,10}$/.test(dni.value)) {
                dni.classList.add('is-invalid');
                valid = false;
            } else {
                dni.classList.remove('is-invalid');
            }

            if (!pass.value) {
                pass.classList.add('is-invalid');
                valid = false;
            } else {
                pass.classList.remove('is-invalid');
            }

            if (!valid) e.preventDefault();
        });
    }
};
