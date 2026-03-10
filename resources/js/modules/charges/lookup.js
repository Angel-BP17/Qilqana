export const LookupModule = {
    init: function() {
        const bind = (id, type, prefix = '') => {
            document.getElementById(id)?.addEventListener('click', async () => {
                const val = document.getElementById(prefix + (type === 'dni' ? 'dni' : 'ruc'))?.value;
                if (!val) return;
                try {
                    const res = await fetch(`/api/${type === 'dni' ? 'natural-people/by-dni' : 'legal-entities/by-ruc'}/${encodeURIComponent(val)}`);
                    if (!res.ok) return;
                    const { data } = await res.json();
                    this.fill(data, type, prefix);
                } catch (e) { console.error('Lookup Error:', e); }
            });
        };
        bind('lookup_charge_dni_btn', 'dni');
        bind('lookup_charge_ruc_btn', 'ruc');
        bind('lookup_charge_dni_btn_edit', 'dni', 'edit_');
        bind('lookup_charge_ruc_btn_edit', 'ruc', 'edit_');
    },
    fill: (data, type, prefix) => {
        if (type === 'dni') {
            document.getElementById(prefix + 'nombres').value = data.nombres || '';
            document.getElementById(prefix + 'apellido_paterno').value = data.apellido_paterno || '';
            document.getElementById(prefix + 'apellido_materno').value = data.apellido_materno || '';
        } else {
            document.getElementById(prefix + 'razon_social').value = data.razon_social || '';
            document.getElementById(prefix + 'district').value = data.district || '';
        }
    }
};
