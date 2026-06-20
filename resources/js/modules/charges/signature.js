export const SignatureModule = {
    pad: null,
    init: function() {
        const canvas = document.getElementById('signature-pad');
        const form = document.getElementById('signChargeForm');
        const modalEl = document.getElementById('signChargeModal');
        if (!canvas || typeof window.SignaturePad === 'undefined') return;

        this.pad = new window.SignaturePad(canvas, { 
            backgroundColor: 'rgba(255, 255, 255, 0)', 
            penColor: 'rgb(0, 0, 0)' 
        });
        
        // Redimensionar cuando el modal se muestra completamente
        if (modalEl) {
            modalEl.addEventListener('shown.bs.modal', () => {
                this.resize();
            });
        }

        // Redimensionar al cambiar el tamaño de la ventana
        window.addEventListener('resize', () => {
            // Solo redimensionar si el modal está visible para evitar problemas
            if (modalEl && modalEl.classList.contains('show')) {
                this.resize();
            }
        });

        const confirmBtn = document.getElementById('confirm-signature');

        // Eventos de limpieza y deshacer
        document.getElementById('clear-signature')?.addEventListener('click', () => {
            this.pad.clear();
            this.validate(confirmBtn);
        });
        
        document.getElementById('undo')?.addEventListener('click', () => {
            const data = this.pad.toData();
            if (data.length) { data.pop(); this.pad.fromData(data); }
            this.validate(confirmBtn);
        });

        // Validar al terminar cada trazo
        this.pad.addEventListener("endStroke", () => {
            this.validate(confirmBtn);
        });

        // Lógica de titularidad y campos extra
        const titularidadRadios = document.querySelectorAll('input[name="titularidad"]');
        const parentescoInput = document.getElementById('sign_parentesco');
        const cartaPoderInput = document.getElementById('sign_carta_poder');

        titularidadRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                const isTitular = document.getElementById('sign_titularidad_yes').checked;
                document.getElementById('sign_parentesco_group')?.classList.toggle('d-none', isTitular);
                document.getElementById('sign_carta_poder_group')?.classList.toggle('d-none', isTitular);
                
                if (parentescoInput) parentescoInput.required = !isTitular;
                if (cartaPoderInput) cartaPoderInput.required = !isTitular;

                this.validate(confirmBtn);
            });
        });

        // Escuchar cambios en campos de texto y archivo para habilitar el botón
        parentescoInput?.addEventListener('input', () => this.validate(confirmBtn));
        cartaPoderInput?.addEventListener('change', () => this.validate(confirmBtn));

        // Captura de geolocalización al seleccionar evidencia
        const evidenceInput = document.getElementById('sign_evidence_root');
        const locationInput = document.getElementById('sign_evidence_location');

        evidenceInput?.addEventListener('change', () => {
            // Eliminar advertencias previas
            document.getElementById('geolocation-warning')?.remove();

            if (evidenceInput.files.length > 0) {
                if (!navigator.geolocation) {
                    showGeolocationWarning('Su navegador no soporta geolocalización o requiere HTTPS.');
                    return;
                }

                navigator.geolocation.getCurrentPosition((pos) => {
                    const loc = {
                        lat: pos.coords.latitude,
                        lng: pos.coords.longitude,
                        accuracy: pos.coords.accuracy,
                        timestamp: new Date(pos.timestamp).toISOString()
                    };
                    locationInput.value = JSON.stringify(loc);
                }, (err) => {
                    console.warn('[Geolocation] No se pudo obtener ubicación:', err.message);
                    if (err.code === err.PERMISSION_DENIED) {
                        showGeolocationWarning('Permiso de ubicación denegado por el usuario o navegador.');
                    } else if (err.message.toLowerCase().includes('secure origin') || window.location.protocol !== 'https:') {
                        showGeolocationWarning('La geolocalización requiere HTTPS. Ejecute "herd secure" para habilitarlo.');
                    } else {
                        showGeolocationWarning('No se pudo obtener la geolocalización.');
                    }
                });
            }
        });

        function showGeolocationWarning(msg) {
            if (!evidenceInput) return;
            const warningEl = document.createElement('div');
            warningEl.id = 'geolocation-warning';
            warningEl.className = 'text-warning small mt-1 d-flex align-items-center gap-1';
            warningEl.innerHTML = `<span class="material-symbols-outlined fs-6 align-middle">warning</span> <span>${msg}</span>`;
            evidenceInput.parentNode.appendChild(warningEl);
        }

        // Envío de formulario
        form?.addEventListener('submit', (e) => {
            if (this.pad.isEmpty()) { e.preventDefault(); alert('Dibuje su firma.'); return; }
            
            // SignaturePad.toDataURL devuelve "data:image/svg+xml;base64,..."
            // Necesitamos decodificar el Base64 para obtener el XML puro del SVG
            const dataUrl = this.pad.toDataURL('image/svg+xml');
            const base64 = dataUrl.split(',')[1];
            const svgXml = atob(base64); // Decodifica Base64 a texto (XML)

            const input = document.getElementById('signature_data_input') || document.createElement('input');
            input.id = 'signature_data_input'; 
            input.type = 'hidden'; 
            input.name = 'firma';
            input.value = svgXml; // Enviamos el XML limpio
            if (!input.parentElement) form.appendChild(input);
        });
    },

    validate: function(btn) {
        if (!btn) return;
        const isEmpty = this.pad.isEmpty();
        const isTitular = document.getElementById('sign_titularidad_yes')?.checked ?? true;
        const parentescoVal = document.getElementById('sign_parentesco')?.value.trim() ?? '';
        const cartaPoderInput = document.getElementById('sign_carta_poder');
        const hasFile = cartaPoderInput ? cartaPoderInput.files.length > 0 : false;
        
        let canSubmit = false;

        if (isTitular) {
            // Si es titular, solo necesita la firma
            canSubmit = !isEmpty;
        } else {
            // Si NO es titular, necesita: firma Y parentesco Y carta poder
            canSubmit = !isEmpty && parentescoVal !== '' && hasFile;
        }

        btn.disabled = !canSubmit;
    },

    resize: function() {
        const canvas = document.getElementById('signature-pad');
        if (!canvas || !this.pad) return;
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        this.pad.clear();
        document.getElementById('confirm-signature').disabled = true;
    }
};
