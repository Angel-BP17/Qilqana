import { describe, it, expect, vi, beforeEach } from 'vitest';
import { SignatureModule } from '../../modules/charges/signature';

describe('SignatureModule', () => {
    let confirmBtn;

    beforeEach(() => {
        // Mock del DOM del panel de firma
        document.body.innerHTML = `
            <form id="signChargeForm">
                <canvas id="signature-pad"></canvas>
                <input type="radio" name="titularidad" id="sign_titularidad_yes" value="1" checked>
                <input type="radio" name="titularidad" id="sign_titularidad_no" value="0">
                
                <div id="sign_parentesco_group" class="d-none">
                    <input type="text" id="sign_parentesco">
                </div>
                <div id="sign_carta_poder_group" class="d-none">
                    <input type="file" id="sign_carta_poder">
                </div>
                
                <button id="confirm-signature" disabled>Confirmar</button>
                <button id="clear-signature">Limpiar</button>
                <button id="undo">Deshacer</button>
            </form>
        `;

        confirmBtn = document.getElementById('confirm-signature');

        // Mock de SignaturePad como constructor válido para Vitest
        window.SignaturePad = vi.fn().mockImplementation(function() {
            this.isEmpty = vi.fn().mockReturnValue(true);
            this.clear = vi.fn();
            this.toData = vi.fn().mockReturnValue([]);
            this.fromData = vi.fn();
            this.addEventListener = vi.fn();
            this.off = vi.fn();
            this.toDataURL = vi.fn().mockReturnValue('data:image/svg+xml;base64,PHN2Zz48L3N2Zz4=');
        });
    });

    it('debe inicializar el pad correctamente', () => {
        SignatureModule.init();
        expect(window.SignaturePad).toHaveBeenCalled();
    });

    it('debe validar el botón si es titular y hay firma', () => {
        SignatureModule.init();
        // Simulamos que el pad NO está vacío
        SignatureModule.pad.isEmpty.mockReturnValue(false);
        
        SignatureModule.validate(confirmBtn);
        expect(confirmBtn.disabled).toBe(false);
    });

    it('debe mantener el botón deshabilitado si NO es titular y falta parentesco o archivo', () => {
        SignatureModule.init();
        SignatureModule.pad.isEmpty.mockReturnValue(false);
        
        // Cambiamos a NO titular
        document.getElementById('sign_titularidad_no').checked = true;
        document.getElementById('sign_titularidad_yes').checked = false;
        
        SignatureModule.validate(confirmBtn);
        expect(confirmBtn.disabled).toBe(true);
    });

    it('debe habilitar el botón si NO es titular pero tiene firma, parentesco y archivo', () => {
        SignatureModule.init();
        SignatureModule.pad.isEmpty.mockReturnValue(false);
        
        document.getElementById('sign_titularidad_no').checked = true;
        document.getElementById('sign_titularidad_yes').checked = false;
        document.getElementById('sign_parentesco').value = 'Hijo';
        
        // Mock de input file
        const fileInput = document.getElementById('sign_carta_poder');
        Object.defineProperty(fileInput, 'files', {
            value: [{ name: 'test.pdf' }]
        });

        SignatureModule.validate(confirmBtn);
        expect(confirmBtn.disabled).toBe(false);
    });
});
