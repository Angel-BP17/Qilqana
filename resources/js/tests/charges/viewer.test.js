import { describe, it, expect, vi, beforeEach } from 'vitest';
import { ViewerModule } from '../../modules/charges/viewer';

describe('ViewerModule', () => {
    let container;

    beforeEach(() => {
        document.body.innerHTML = '<div id="content"></div>';
        container = document.getElementById('content');
        global.fetch = vi.fn();
    });

    it('debe cargar contenido SVG en el contenedor', async () => {
        const svgContent = '<svg>firma</svg>';
        global.fetch.mockResolvedValue({
            ok: true,
            text: () => Promise.resolve(svgContent)
        });

        await ViewerModule.load('http://test.com/firma.svg', container, true);
        expect(container.innerHTML).toBe(svgContent);
    });

    it('debe cargar un iframe para archivos PDF', async () => {
        global.fetch.mockResolvedValue({
            ok: true,
            headers: { get: () => 'application/pdf' },
            blob: () => Promise.resolve(new Blob(['pdf'], { type: 'application/pdf' }))
        });

        // Mock URL.createObjectURL
        global.URL.createObjectURL = vi.fn(() => 'blob:url');

        await ViewerModule.load('http://test.com/archivo.pdf', container, false);
        expect(container.innerHTML).toContain('<iframe src="blob:url"');
    });

    it('debe mostrar error si el fetch falla', async () => {
        global.fetch.mockResolvedValue({ ok: false });
        await ViewerModule.load('http://test.com/error', container, true);
        expect(container.innerHTML).toContain('Error al cargar archivo');
    });
});
