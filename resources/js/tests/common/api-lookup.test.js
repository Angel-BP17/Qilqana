import { describe, it, expect, vi, beforeEach } from 'vitest';
import { ApiLookup } from '../../modules/common/api-lookup';

describe('ApiLookup', () => {
    beforeEach(() => {
        global.fetch = vi.fn();
    });

    it('debe retornar datos de persona natural por DNI', async () => {
        const mockData = { data: { nombres: 'Juan', dni: '12345678' } };
        global.fetch.mockResolvedValue({
            ok: true,
            json: () => Promise.resolve(mockData)
        });

        const result = await ApiLookup.dni('12345678');
        expect(result.nombres).toBe('Juan');
        expect(global.fetch).toHaveBeenCalledWith('/api/natural-people/by-dni/12345678');
    });

    it('debe retornar null si la respuesta no es ok', async () => {
        global.fetch.mockResolvedValue({ ok: false });
        const result = await ApiLookup.ruc('20123456789');
        expect(result).toBeNull();
    });
});
