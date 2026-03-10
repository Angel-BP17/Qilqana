import { describe, it, expect, vi, beforeEach } from 'vitest';
import { NaturalPeopleManagement } from '../../modules/natural-people/management';

describe('NaturalPeopleManagement', () => {
    beforeEach(() => {
        document.body.innerHTML = `
            <div id="editNaturalPersonModal">
                <form id="editNaturalPersonForm"></form>
                <input id="edit_dni">
                <input id="edit_nombres">
                <input id="edit_apellido_paterno">
                <input id="edit_apellido_materno">
            </div>
            <button class="btn-edit-natural-person" 
                data-action="/person/1" 
                data-dni="12345678" 
                data-nombres="Juan" 
                data-apellido-paterno="Perez"
                data-apellido-materno="Gomez">Edit</button>
        `;
        
        global.bootstrap = {
            Modal: vi.fn().mockImplementation(function() {
                this.show = vi.fn();
                this.hide = vi.fn();
            })
        };
    });

    it('debe llenar el formulario de edición de persona natural', () => {
        NaturalPeopleManagement.setupEditModal();
        const btn = document.querySelector('.btn-edit-natural-person');
        btn.click();

        expect(document.getElementById('edit_dni').value).toBe('12345678');
        expect(document.getElementById('edit_nombres').value).toBe('Juan');
        expect(document.getElementById('edit_apellido_paterno').value).toBe('Perez');
        expect(document.getElementById('edit_apellido_materno').value).toBe('Gomez');
    });
});
