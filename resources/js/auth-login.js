        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            // Mostrar/ocultar contraseña
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('bi-eye');
                this.classList.toggle('bi-eye-slash');
            });

            // Validación del formulario
            loginForm.addEventListener('submit', function(event) {
                event.preventDefault();

                const dni = document.getElementById('dni');
                const password = document.getElementById('password');
                let isValid = true;

                // Validar DNI
                if (!dni.value || !isValidDni(dni.value)) {
                    dni.classList.add('is-invalid');
                    isValid = false;
                } else {
                    dni.classList.remove('is-invalid');
                }

                // Validar contraseña
                if (!password.value) {
                    password.classList.add('is-invalid');
                    isValid = false;
                } else {
                    password.classList.remove('is-invalid');
                }

                // Si es válido, procesar el login
                if (isValid) {
                    this.submit();
                }
            });

            function isValidDni(dni) {
                const re = /^\d{8,10}$/;
                return re.test(dni);
            }
        });
