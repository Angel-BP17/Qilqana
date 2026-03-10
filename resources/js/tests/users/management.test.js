import { describe, it, expect, vi, beforeEach } from 'vitest';
import { UserManagement } from '../../modules/users/management';

describe('UserManagement', () => {
    beforeEach(() => {
        document.body.innerHTML = `
            <div id="editUserModal">
                <form id="editUserForm"></form>
                <input id="edit_name">
                <input id="edit_last_name">
                <input id="edit_dni">
            </div>
            <button class="btn-edit-user" 
                data-action="/user/1" 
                data-name="John" 
                data-last_name="Doe" 
                data-dni="12345678" 
                data-roles='["admin"]'>Edit</button>
        `;
        
        // Mock bootstrap como constructor
        global.bootstrap = {
            Modal: vi.fn().mockImplementation(function() {
                this.show = vi.fn();
                this.hide = vi.fn();
            })
        };
    });

    it('debe llenar el formulario de edición al hacer clic', () => {
        UserManagement.setupEditModal();
        const btn = document.querySelector('.btn-edit-user');
        btn.click();

        expect(document.getElementById('edit_name').value).toBe('John');
        expect(document.getElementById('edit_last_name').value).toBe('Doe');
        expect(document.getElementById('edit_dni').value).toBe('12345678');
        expect(document.getElementById('editUserForm').action).toContain('/user/1');
    });
});
