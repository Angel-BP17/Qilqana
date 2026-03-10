export const SignatureModule = {
    pad: null,
    init: function() {
        const canvas = document.getElementById('signature-pad');
        const form = document.getElementById('signChargeForm');
        if (!canvas || typeof window.SignaturePad === 'undefined') return;

        this.pad = new window.SignaturePad(canvas, { 
            backgroundColor: 'rgba(255, 255, 255, 0)', 
            penColor: 'rgb(0, 0, 0)' 
        });
        
        document.getElementById('clear-signature')?.addEventListener('click', () => this.pad.clear());
        document.getElementById('undo')?.addEventListener('click', () => {
            const data = this.pad.toData();
            if (data.length) { data.pop(); this.pad.fromData(data); }
        });

        form?.addEventListener('submit', (e) => {
            if (this.pad.isEmpty()) { e.preventDefault(); alert('Dibuje su firma.'); return; }
            const input = document.getElementById('signature_data_input') || document.createElement('input');
            input.id = 'signature_data_input'; input.type = 'hidden'; input.name = 'firma';
            input.value = this.pad.toDataURL('image/svg+xml');
            if (!input.parentElement) form.appendChild(input);
        });
    },
    resize: function() {
        const canvas = document.getElementById('signature-pad');
        if (!canvas || !this.pad) return;
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        this.pad.clear();
    }
};
