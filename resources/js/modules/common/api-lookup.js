import { route } from './url-helper';

export const ApiLookup = {
    dni: async (dni) => {
        const clean = (dni || '').trim();
        if (!clean) return null;
        try {
            const res = await fetch(route(`/search/natural-people/by-dni/${encodeURIComponent(clean)}`), {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return null;
            const payload = await res.json();
            return payload.data || null;
        } catch (e) {
            console.error('Lookup DNI Error:', e);
            return null;
        }
    },
    ruc: async (ruc) => {
        const clean = (ruc || '').trim();
        if (!clean) return null;
        try {
            const res = await fetch(route(`/search/legal-entities/by-ruc/${encodeURIComponent(clean)}`), {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return null;
            const payload = await res.json();
            return payload.data || null;
        } catch (e) {
            console.error('Lookup RUC Error:', e);
            return null;
        }
    }
};
