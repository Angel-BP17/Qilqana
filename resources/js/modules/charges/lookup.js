export const LookupModule = {
    init: function() {
        // Búsqueda para Persona Natural (Cargo)
        document.getElementById('lookup_charge_dni_btn')?.addEventListener('click', () => {
            const dni = document.getElementById('dni')?.value;
            this.handleDniLookup(dni, 'dni_api_error', '.natural-details');
        });

        // Búsqueda para RUC (Persona Jurídica - Fase 1)
        document.getElementById('lookup_charge_ruc_btn')?.addEventListener('click', () => {
            const ruc = document.getElementById('ruc')?.value;
            this.handleRucLookup(ruc);
        });

        // Búsqueda para DNI de Representante (Persona Jurídica - Fase 2)
        document.getElementById('lookup_representative_dni_btn')?.addEventListener('click', () => {
            const dni = document.getElementById('representative_dni')?.value;
            this.handleDniLookup(dni, 'rep_dni_api_error', '.rep-details', 'representative_');
        });
    },

    /**
     * Gestión de búsqueda por DNI (General)
     */
    handleDniLookup: async function(dni, errorId, detailsClass, prefix = '') {
        const clean = (dni || '').trim();
        const errorEl = document.getElementById(errorId);
        const details = document.querySelectorAll(detailsClass);
        const btnId = prefix === 'representative_' ? 'lookup_representative_dni_btn' : 'lookup_charge_dni_btn';
        const btn = document.getElementById(btnId);
        
        if (!clean) return;
        if (errorEl) errorEl.classList.add('d-none');

        this.setLoading(btn, true);

        try {
            const res = await fetch(`/api/natural-people/by-dni/${encodeURIComponent(clean)}`);
            const payload = await res.json();
            
            details.forEach(el => el.classList.remove('d-none'));

            if (res.ok && payload.data) {
                const data = payload.data;
                document.getElementById(prefix + 'nombres').value = data.nombres || '';
                document.getElementById(prefix + 'apellido_paterno').value = data.apellido_paterno || '';
                document.getElementById(prefix + 'apellido_materno').value = data.apellido_materno || '';
            } else {
                if (errorEl) {
                    errorEl.textContent = 'No se pudo autocompletar. Por favor, ingrese los datos manualmente.';
                    errorEl.classList.remove('d-none');
                }
            }
        } catch (e) {
            console.error('Lookup Error:', e);
            details.forEach(el => el.classList.remove('d-none'));
            if (errorEl) {
                errorEl.textContent = 'Límite de consultas excedido o error de red. Complete manualmente.';
                errorEl.classList.remove('d-none');
            }
        } finally {
            this.setLoading(btn, false);
        }
    },

    /**
     * Gestión de búsqueda por RUC (Entidad Legal)
     */
    handleRucLookup: async function(ruc) {
        const clean = (ruc || '').trim();
        const errorEl = document.getElementById('ruc_api_error');
        const entityDetails = document.querySelectorAll('.entity-details');
        const repSection = document.querySelector('.representative-section');
        const repErrorEl = document.getElementById('rep_dni_api_error');
        const btn = document.getElementById('lookup_charge_ruc_btn');

        if (!clean) return;
        if (errorEl) errorEl.classList.add('d-none');
        if (repErrorEl) repErrorEl.classList.add('d-none');

        this.setLoading(btn, true);

        try {
            const res = await fetch(`/api/legal-entities/by-ruc/${encodeURIComponent(clean)}`);
            const payload = await res.json();

            entityDetails.forEach(el => el.classList.remove('d-none'));
            repSection?.classList.remove('d-none');

            if (res.ok && payload.data) {
                const data = payload.data;
                document.getElementById('razon_social').value = data.razon_social || '';
                document.getElementById('district').value = data.district || '';

                if (data.representative && data.representative.natural_person) {
                    const rep = data.representative;
                    const person = rep.natural_person;
                    document.getElementById('representative_dni').value = person.dni || '';
                    document.querySelectorAll('.rep-details').forEach(el => el.classList.remove('d-none'));
                    document.getElementById('representative_nombres').value = person.nombres || '';
                    document.getElementById('representative_apellido_paterno').value = person.apellido_paterno || '';
                    document.getElementById('representative_apellido_materno').value = person.apellido_materno || '';
                    document.getElementById('representative_cargo').value = rep.cargo || '';
                    document.getElementById('representative_since').value = rep.fecha_desde || '';
                } else {
                    if (repErrorEl) {
                        repErrorEl.textContent = 'Representante no registrado. Ingrese DNI para buscar o completar.';
                        repErrorEl.classList.remove('d-none');
                    }
                }
            } else {
                if (errorEl) {
                    errorEl.textContent = 'Entidad no encontrada. Complete los datos manualmente.';
                    errorEl.classList.remove('d-none');
                }
            }
        } catch (e) {
            console.error('RUC Lookup Error:', e);
            entityDetails.forEach(el => el.classList.remove('d-none'));
            repSection?.classList.remove('d-none');
            if (errorEl) {
                errorEl.textContent = 'Error al conectar con la API. Ingrese datos manualmente.';
                errorEl.classList.remove('d-none');
            }
        } finally {
            this.setLoading(btn, false);
        }
    },

    setLoading: function(btn, loading) {
        if (!btn) return;
        if (loading) {
            btn.dataset.originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
            btn.disabled = true;
        } else {
            btn.innerHTML = btn.dataset.originalHtml || 'Buscar';
            btn.disabled = false;
        }
    }
};
