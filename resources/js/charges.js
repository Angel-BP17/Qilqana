        window.addEventListener('load', () => {
            const dashboardEl = document.getElementById('charges-dashboard');
            if (!dashboardEl) return;
            const refreshIntervalSeconds = Number(dashboardEl.dataset.refreshInterval) || 5;
            const refreshUrl = dashboardEl.dataset.refreshUrl || '';
            const tipoInteresadoCreate = document.getElementById('tipo_interesado');
            const tipoInteresadoEdit = document.getElementById('edit_tipo_interesado');
            const naturalGroupCreate = document.querySelector('.persona-natural-fields');
            const juridicaGroupCreate = document.querySelector('.persona-juridica-fields');
            const assignedGroupCreate = document.querySelector('.assigned-user-field');
            const assignedValueCreate = document.getElementById('assigned_to');
            const naturalGroupEdit = document.querySelector('.persona-natural-fields-edit');
            const juridicaGroupEdit = document.querySelector('.persona-juridica-fields-edit');
            const assignedGroupEdit = document.querySelector('.assigned-user-field-edit');
            const assignedValueEdit = document.getElementById('edit_assigned_to');

            const toggleFields = (typeValue, naturalGroup, juridicaGroup, assignedGroup, assignedValue) => {
                const showNatural = typeValue === 'Persona Natural';
                const showJuridica = typeValue === 'Persona Juridica';
                const showUsuario = typeValue === 'Trabajador UGEL';

                if (naturalGroup) {
                    naturalGroup.classList.toggle('d-none', !showNatural);
                    naturalGroup.querySelectorAll('input').forEach(input => {
                        input.required = showNatural;
                    });
                }
                if (juridicaGroup) {
                    juridicaGroup.classList.toggle('d-none', !showJuridica);
                    juridicaGroup.querySelectorAll('input').forEach(input => {
                        const isOptional = input.dataset.optional === 'true';
                        input.required = showJuridica && !isOptional;
                    });
                }
                if (assignedGroup) {
                    assignedGroup.classList.toggle('d-none', !showUsuario);
                }
                if (assignedValue) {
                    assignedValue.required = showUsuario;
                }
            };

            if (tipoInteresadoCreate) {
                tipoInteresadoCreate.addEventListener('change', () => {
                    toggleFields(
                        tipoInteresadoCreate.value,
                        naturalGroupCreate,
                        juridicaGroupCreate,
                        assignedGroupCreate,
                        assignedValueCreate
                    );
                });
                toggleFields(
                    tipoInteresadoCreate.value,
                    naturalGroupCreate,
                    juridicaGroupCreate,
                    assignedGroupCreate,
                    assignedValueCreate
                );
            }
            if (tipoInteresadoEdit) {
                tipoInteresadoEdit.addEventListener('change', () => {
                    toggleFields(
                        tipoInteresadoEdit.value,
                        naturalGroupEdit,
                        juridicaGroupEdit,
                        assignedGroupEdit,
                        assignedValueEdit
                    );
                });
            }

            
            const lookupDni = async (dni, target) => {
                const clean = (dni || '').trim();
                if (!clean) return;
                try {
                    const res = await fetch(`/api/natural-people/by-dni/${encodeURIComponent(clean)}`);
                    if (!res.ok) return;
                    const payload = await res.json();
                    const data = payload.data || {};
                    if (target.dni) target.dni.value = data.dni || '';
                    if (target.nombres) target.nombres.value = data.nombres || '';
                    if (target.apellidoPaterno) target.apellidoPaterno.value = data.apellido_paterno || '';
                    if (target.apellidoMaterno) target.apellidoMaterno.value = data.apellido_materno || '';
                } catch (e) {
                    console.error(e);
                }
            };

            const lookupRuc = async (ruc, target) => {
                const clean = (ruc || '').trim();
                if (!clean) return;
                try {
                    const res = await fetch(`/api/legal-entities/by-ruc/${encodeURIComponent(clean)}`);
                    if (!res.ok) return;
                    const payload = await res.json();
                    const data = payload.data || {};
                    if (target.ruc) target.ruc.value = data.ruc || '';
                    if (target.razon) target.razon.value = data.razon_social || '';
                    if (target.district) target.district.value = data.district || '';
                    if (target.repDni) target.repDni.value = data.representative?.dni || '';
                    if (target.repName) target.repName.value = data.representative?.nombre || '';
                    if (target.repCargo) target.repCargo.value = data.representative?.cargo || '';
                    if (target.repSince) target.repSince.value = data.representative?.fecha_desde || '';

                } catch (e) {
                    console.error(e);
                }
            };

            const lookupChargeDniBtn = document.getElementById('lookup_charge_dni_btn');
            if (lookupChargeDniBtn) {
                lookupChargeDniBtn.addEventListener('click', () => {
                lookupDni(document.getElementById('dni')?.value, {
                    dni: document.getElementById('dni'),
                    nombres: document.getElementById('nombres'),
                    apellidoPaterno: document.getElementById('apellido_paterno'),
                    apellidoMaterno: document.getElementById('apellido_materno'),
                });
            });
        }

            const lookupChargeDniBtnEdit = document.getElementById('lookup_charge_dni_btn_edit');
            if (lookupChargeDniBtnEdit) {
                lookupChargeDniBtnEdit.addEventListener('click', () => {
                lookupDni(document.getElementById('edit_dni')?.value, {
                    dni: document.getElementById('edit_dni'),
                    nombres: document.getElementById('edit_nombres'),
                    apellidoPaterno: document.getElementById('edit_apellido_paterno'),
                    apellidoMaterno: document.getElementById('edit_apellido_materno'),
                });
            });
        }

            const lookupChargeRucBtn = document.getElementById('lookup_charge_ruc_btn');
            if (lookupChargeRucBtn) {
                lookupChargeRucBtn.addEventListener('click', () => {
                    lookupRuc(document.getElementById('ruc')?.value, {
                        ruc: document.getElementById('ruc'),
                        razon: document.getElementById('razon_social'),
                        district: document.getElementById('district'),
                        repDni: document.getElementById('representative_dni'),
                        repName: document.getElementById('representative_name'),
                        repCargo: document.getElementById('representative_cargo'),
                        repSince: document.getElementById('representative_since'),
                    });
                });
            }

            const lookupChargeRucBtnEdit = document.getElementById('lookup_charge_ruc_btn_edit');
            if (lookupChargeRucBtnEdit) {
                lookupChargeRucBtnEdit.addEventListener('click', () => {
                    lookupRuc(document.getElementById('edit_ruc')?.value, {
                        ruc: document.getElementById('edit_ruc'),
                        razon: document.getElementById('edit_razon_social'),
                        district: document.getElementById('edit_district'),
                        repDni: document.getElementById('edit_representative_dni'),
                        repName: document.getElementById('edit_representative_name'),
                        repCargo: document.getElementById('edit_representative_cargo'),
                        repSince: document.getElementById('edit_representative_since'),
                    });
                });
            }
            const initSelect2 = (selectEl, modalEl) => {
                if (!window.jQuery || !window.jQuery.fn.select2 || !selectEl) return;
                const $select = jQuery(selectEl);
                if ($select.data('select2')) {
                    $select.select2('destroy');
                }
                $select.select2({
                    placeholder: 'Buscar usuario',
                    width: '100%',
                    allowClear: true,
                    dropdownParent: modalEl ? jQuery(modalEl) : undefined,
                });
            };

            const createModalEl = document.getElementById('createChargeModal');
            const editModalEl = document.getElementById('editChargeModal');
            if (createModalEl) {
                createModalEl.addEventListener('shown.bs.modal', () => {
                    initSelect2(assignedValueCreate, createModalEl);
                });
            }
            if (editModalEl) {
                editModalEl.addEventListener('shown.bs.modal', () => {
                    initSelect2(assignedValueEdit, editModalEl);
                });
            }

            const tabStorageKey = 'charges.activeTab';
            let userInteracting = false;
            let interactionTimeoutId = null;

            const bindTabPersistence = () => {
                const tabList = document.getElementById('charges-tabs');
                if (!tabList) return;
                tabList.querySelectorAll('[data-bs-toggle="tab"]').forEach(btn => {
                    btn.addEventListener('shown.bs.tab', event => {
                        const id = event.target?.id;
                        if (id) {
                            localStorage.setItem(tabStorageKey, id);
                        }
                    });
                });
            };

            const getSavedTabId = () => localStorage.getItem(tabStorageKey);

            const getActiveTabId = () => {
                const activeBtn = document.querySelector('#charges-tabs .nav-link.active');
                return activeBtn?.id || getSavedTabId();
            };

            const restoreActiveTab = (tabId) => {
                if (!tabId) return;
                const btn = document.getElementById(tabId);
                if (!btn || !btn.matches('[data-bs-toggle="tab"]')) return;
                bootstrap.Tab.getOrCreateInstance(btn).show();
            };

            // Editar modal
            const editModal = new bootstrap.Modal(document.getElementById('editChargeModal'));
            const editForm = document.getElementById('editChargeForm');
            const editAssigned = document.getElementById('edit_assigned_to');
            const editDocumentDate = document.getElementById('edit_document_date');

            const bindEditButtons = () => {
                if (!dashboardEl) return;
                dashboardEl.querySelectorAll('.btn-edit-charge').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const charge = JSON.parse(btn.dataset.charge);
                        editForm.action = btn.dataset.action;
                        document.getElementById('edit_asunto').value = charge.asunto ?? '';
                        if (tipoInteresadoEdit) {
                            tipoInteresadoEdit.value = charge.tipo_interesado || '';
                            toggleFields(
                                tipoInteresadoEdit.value,
                                naturalGroupEdit,
                                juridicaGroupEdit,
                                assignedGroupEdit,
                                assignedValueEdit
                            );
                        }
                        const editRuc = document.getElementById('edit_ruc');
                        const editRazon = document.getElementById('edit_razon_social');
                        const editDistrict = document.getElementById('edit_district');
                        const editRepDni = document.getElementById('edit_representative_dni');
                        const editRepName = document.getElementById('edit_representative_name');
                        const editRepCargo = document.getElementById('edit_representative_cargo');
                        const editRepSince = document.getElementById('edit_representative_since');
                        const editDni = document.getElementById('edit_dni');
                        const editNombres = document.getElementById('edit_nombres');
                        const editApellidoPaterno = document.getElementById('edit_apellido_paterno');
                        const editApellidoMaterno = document.getElementById('edit_apellido_materno');
                        if (editAssigned) {
                            const assignedValue = btn.dataset.assigned || '';
                            editAssigned.value = assignedValue;
                            if (window.jQuery && window.jQuery.fn.select2) {
                                jQuery(editAssigned).val(assignedValue).trigger('change');
                            }
                        }
                        if (editRuc) editRuc.value = charge.legal_entity?.ruc ?? '';
                        if (editRazon) editRazon.value = charge.legal_entity?.razon_social ?? '';
                        if (editDistrict) editDistrict.value = charge.legal_entity?.district ?? '';
                        if (editRepDni) editRepDni.value = charge.legal_entity?.representative?.dni ?? '';
                        if (editRepName) editRepName.value = charge.legal_entity?.representative?.nombre ?? '';
                        if (editRepCargo) editRepCargo.value = charge.legal_entity?.representative?.cargo ?? '';
                        if (editRepSince) editRepSince.value = charge.legal_entity?.representative?.fecha_desde ?? '';
                        if (editDni) editDni.value = charge.natural_person?.dni ?? '';
                        if (editNombres) editNombres.value = charge.natural_person?.nombres ?? '';
                        if (editApellidoPaterno) {
                            editApellidoPaterno.value = charge.natural_person?.apellido_paterno ?? '';
                        }
                        if (editApellidoMaterno) {
                            editApellidoMaterno.value = charge.natural_person?.apellido_materno ?? '';
                        }
                        if (editDocumentDate) editDocumentDate.value = charge.document_date ??
                            '';
                        editModal.show();
                    });
                });
            };

            // Rechazar modal
            const rejectModal = new bootstrap.Modal(document.getElementById('rejectChargeModal'));
            const rejectForm = document.getElementById('rejectChargeForm');
            const rejectComment = document.getElementById('signature_comment');

            const bindRejectButtons = () => {
                if (!dashboardEl) return;
                dashboardEl.querySelectorAll('.btn-reject-charge').forEach(btn => {
                    btn.addEventListener('click', () => {
                        if (rejectForm) {
                            rejectForm.action = btn.dataset.action;
                        }
                        if (rejectComment) {
                            rejectComment.value = '';
                        }
                        rejectModal.show();
                    });
                });
            };

            // Eliminar modal
            const deleteModalElement = document.getElementById('deleteChargeModal');
            const deleteModal = deleteModalElement ? new bootstrap.Modal(deleteModalElement) : null;
            const deleteForm = document.getElementById('deleteChargeForm');
            const deleteReason = document.getElementById('delete_reason');

            const bindDeleteButtons = () => {
                if (!dashboardEl || !deleteModal || !deleteForm) return;
                dashboardEl.querySelectorAll('.btn-delete-charge').forEach(btn => {
                    btn.addEventListener('click', () => {
                        deleteForm.action = btn.dataset.action;
                        if (deleteReason) {
                            deleteReason.value = '';
                        }
                        deleteModal.show();
                    });
                });
            };

            // Firmar modal
            const signModalElement = document.getElementById('signChargeModal');
            const signModal = new bootstrap.Modal(signModalElement);
            const signForm = document.getElementById('signChargeForm');
            const signExternalFields = document.getElementById('sign_external_fields');
            const signTitularidadYes = document.getElementById('sign_titularidad_yes');
            const signTitularidadNo = document.getElementById('sign_titularidad_no');
            const signParentescoGroup = document.getElementById('sign_parentesco_group');
            const signCartaPoderGroup = document.getElementById('sign_carta_poder_group');
            const signParentesco = document.getElementById('sign_parentesco');
            const signCartaPoder = document.getElementById('sign_carta_poder');
            const signFirmadoState = document.getElementById('sign_firmado_state');
            const signSignedBy = document.getElementById('sign_signed_by');
            const signSignedByName = document.getElementById('sign_signed_by_name');
            const canvas = document.getElementById('signature-pad');
            const clearBtn = document.getElementById('clear-signature');
            const confirmBtn = document.getElementById('confirm-signature');
            const undoBtn = document.getElementById('undo');
            const signaturePreview = document.getElementById('signaturePreview');
            const signaturePreviewContainer = document.getElementById('signaturePreviewContainer');
            let existingSignatureContent = '';
            let isDrawing = false;
            let lastX = 0;
            let lastY = 0;
            let currentColor = '#000000';
            let currentLineWidth = 1.5;
            let history = [];
            let currentStrokes = [];

            function resizeCanvas() {
                const container = canvas.parentElement;
                const displayWidth = Math.max(container.clientWidth, 300);
                const displayHeight = 240;

                if (canvas.width !== displayWidth || canvas.height !== displayHeight) {
                    canvas.width = displayWidth;
                    canvas.height = displayHeight;
                }

                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = 'rgba(255, 255, 255, 0)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
            }

            function getCanvasCoordinates(clientX, clientY) {
                const rect = canvas.getBoundingClientRect();
                return {
                    x: (clientX - rect.left) * (canvas.width / rect.width),
                    y: (clientY - rect.top) * (canvas.height / rect.height)
                };
            }

            function drawLine(fromX, fromY, toX, toY) {
                const ctx = canvas.getContext('2d');
                ctx.beginPath();
                ctx.moveTo(fromX, fromY);
                ctx.lineTo(toX, toY);
                ctx.strokeStyle = currentColor;
                ctx.lineWidth = currentLineWidth;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
                ctx.stroke();
            }

            function updateSignaturePreview() {
                if (currentStrokes.length > 0) {
                    const svg = generateSVG();
                    signaturePreview.innerHTML = svg;
                    signaturePreviewContainer.style.display = 'block';
                } else if (existingSignatureContent) {
                    signaturePreview.innerHTML = existingSignatureContent;
                    signaturePreviewContainer.style.display = 'block';
                } else {
                    signaturePreviewContainer.style.display = 'none';
                }
            }

            function generateSVG() {
                if (currentStrokes.length === 0) return '';

                let minX = Infinity,
                    minY = Infinity,
                    maxX = -Infinity,
                    maxY = -Infinity;

                for (const stroke of currentStrokes) {
                    for (const point of stroke.points) {
                        minX = Math.min(minX, point.x);
                        minY = Math.min(minY, point.y);
                        maxX = Math.max(maxX, point.x);
                        maxY = Math.max(maxY, point.y);
                    }
                }

                const margin = 10;
                const viewBoxX = Math.max(0, minX - margin);
                const viewBoxY = Math.max(0, minY - margin);
                const viewBoxWidth = maxX - minX + margin * 2;
                const viewBoxHeight = maxY - minY + margin * 2;

                let svgPaths = '';
                for (const stroke of currentStrokes) {
                    if (stroke.points.length < 2) continue;
                    let pathData = `M ${stroke.points[0].x - viewBoxX} ${stroke.points[0].y - viewBoxY}`;
                    for (let i = 1; i < stroke.points.length; i++) {
                        pathData += ` L ${stroke.points[i].x - viewBoxX} ${stroke.points[i].y - viewBoxY}`;
                    }
                    svgPaths +=
                        `<path d="${pathData}" stroke="${stroke.color}" stroke-width="${stroke.width}" fill="none" stroke-linecap="round" stroke-linejoin="round"/>`;
                }

                return `<svg xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 ${viewBoxWidth} ${viewBoxHeight}"
                        preserveAspectRatio="xMidYMid meet"
                        style="width: 100%; height: auto; max-height: 120px;">
                        ${svgPaths}
                    </svg>`;
            }

            function updateConfirmButtonState() {
                confirmBtn.disabled = currentStrokes.length === 0;
            }

            function getTitularidadValue() {
                const selected = signForm?.querySelector('input[name="titularidad"]:checked');
                return selected ? selected.value === '1' : true;
            }

            function setTitularidadValue(isTitular) {
                if (signTitularidadYes) signTitularidadYes.checked = isTitular;
                if (signTitularidadNo) signTitularidadNo.checked = !isTitular;
            }

            function resetExternalInputs() {
                setTitularidadValue(true);
                if (signParentesco) signParentesco.value = '';
                if (signCartaPoder) signCartaPoder.value = '';
                if (signParentesco) signParentesco.required = false;
                if (signCartaPoder) signCartaPoder.required = false;
                if (signParentescoGroup) signParentescoGroup.classList.add('d-none');
                if (signCartaPoderGroup) signCartaPoderGroup.classList.add('d-none');
            }

            function updateTitularidadState() {
                const showExtra = !getTitularidadValue();
                if (signParentescoGroup) signParentescoGroup.classList.toggle('d-none', !showExtra);
                if (signCartaPoderGroup) signCartaPoderGroup.classList.toggle('d-none', !showExtra);
                if (signParentesco) signParentesco.required = showExtra;
                if (signCartaPoder) signCartaPoder.required = showExtra;
                if (!showExtra) {
                    if (signParentesco) signParentesco.value = '';
                    if (signCartaPoder) signCartaPoder.value = '';
                }
            }

            function toggleSignatureExternalFields(tipoInteresado) {
                if (!signExternalFields) return;
                const needsExternal = ['Persona Juridica', 'Persona Natural'].includes(tipoInteresado);
                signExternalFields.classList.toggle('d-none', !needsExternal);
                if (!needsExternal) {
                    resetExternalInputs();
                    return;
                }
                updateTitularidadState();
            }

            if (signTitularidadYes || signTitularidadNo) {
                const titularidadInputs = signForm?.querySelectorAll('input[name="titularidad"]') || [];
                titularidadInputs.forEach(input => {
                    input.addEventListener('change', () => {
                        updateTitularidadState();
                    });
                });
            }

            // Guarda la firma como SVG en un campo oculto
            const signatureDataInput = document.createElement('input');
            signatureDataInput.type = 'hidden';
            signatureDataInput.name = 'firma';
            signForm.appendChild(signatureDataInput);

            function clearCanvas(preserveExistingSignature = false) {
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = 'rgba(255, 255, 255, 0)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                currentStrokes = [];
                history = [];
                if (preserveExistingSignature && existingSignatureContent) {
                    signaturePreview.innerHTML = existingSignatureContent;
                    signaturePreviewContainer.style.display = 'block';
                } else {
                    existingSignatureContent = preserveExistingSignature ? existingSignatureContent : '';
                    signaturePreview.innerHTML = '';
                    signaturePreviewContainer.style.display = 'none';
                }
                updateConfirmButtonState();
            }

            function undoLast() {
                if (currentStrokes.length > 0) {
                    history.push(currentStrokes.pop());
                    redraw();
                }
            }

            function redraw() {
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = 'rgba(255, 255, 255, 0)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                for (const stroke of currentStrokes) {
                    if (stroke.points.length > 1) {
                        for (let i = 1; i < stroke.points.length; i++) {
                            const from = stroke.points[i - 1];
                            const to = stroke.points[i];
                            ctx.beginPath();
                            ctx.moveTo(from.x, from.y);
                            ctx.lineTo(to.x, to.y);
                            ctx.strokeStyle = stroke.color;
                            ctx.lineWidth = stroke.width;
                            ctx.lineCap = 'round';
                            ctx.lineJoin = 'round';
                            ctx.stroke();
                        }
                    }
                }
            }

            clearBtn.addEventListener('click', function(e) {
                e.preventDefault();
                clearCanvas();
            });

            undoBtn.addEventListener('click', function(e) {
                e.preventDefault();
                undoLast();
                updateSignaturePreview();
                updateConfirmButtonState();
            });

            signForm.addEventListener('submit', function(e) {
                if (currentStrokes.length === 0) {
                    e.preventDefault();
                    alert('Por favor, dibuje su firma antes de confirmar.');
                } else {
                    signatureDataInput.value = generateSVG();
                }
            });

            window.addEventListener('resize', function() {
                resizeCanvas();
                redraw();
                updateSignaturePreview();
            });

            const bindSignButtons = () => {
                if (!dashboardEl) return;
                dashboardEl.querySelectorAll('.btn-sign-charge').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const charge = JSON.parse(btn.dataset.charge);
                        existingSignatureContent = btn.dataset.signature ? JSON.parse(btn
                            .dataset
                            .signature) : '';
                        signForm.action = btn.dataset.action;
                        const isSigned = charge.signature?.signature_status === 'firmado';
                        signFirmadoState.className = isSigned ? 'badge bg-primary' :
                            'badge bg-warning text-dark';
                        signFirmadoState.textContent = isSigned ? 'Ya firmado' :
                            'Pendiente de firma';
                        const signerName = btn.dataset.signer || '';
                        if (signerName) {
                            signSignedBy.style.display = 'inline';
                            signSignedByName.textContent = signerName;
                        } else {
                            signSignedBy.style.display = 'none';
                            signSignedByName.textContent = '';
                        }
                        clearCanvas(true);
                        toggleSignatureExternalFields(charge.tipo_interesado || '');
                        if (existingSignatureContent) {
                            signaturePreview.innerHTML = existingSignatureContent;
                            signaturePreviewContainer.style.display = 'block';
                            confirmBtn.disabled = true;
                        }
                        signModal.show();
                        resizeCanvas();
                    });
                });
            };

            function initSignaturePad() {
                resizeCanvas();
                redraw();
                updateConfirmButtonState();
            }

            if (signModalElement) {
                signModalElement.addEventListener('shown.bs.modal', () => {
                    if (existingSignatureContent) {
                        signaturePreview.innerHTML = existingSignatureContent;
                        signaturePreviewContainer.style.display = 'block';
                    }
                    initSignaturePad();
                });
            }

            function setupCanvasEvents() {
                canvas.onmousedown = function(e) {
                    isDrawing = true;
                    const point = getCanvasCoordinates(e.clientX, e.clientY);
                    lastX = point.x;
                    lastY = point.y;
                    currentStrokes.push({
                        color: currentColor,
                        width: currentLineWidth,
                        points: [point]
                    });
                };

                canvas.onmousemove = function(e) {
                    if (!isDrawing) return;
                    const point = getCanvasCoordinates(e.clientX, e.clientY);
                    drawLine(lastX, lastY, point.x, point.y);
                    currentStrokes[currentStrokes.length - 1].points.push(point);
                    lastX = point.x;
                    lastY = point.y;
                };

                canvas.onmouseup = function() {
                    isDrawing = false;
                    updateSignaturePreview();
                    updateConfirmButtonState();
                };

                canvas.onmouseout = function() {
                    isDrawing = false;
                };

                canvas.ontouchstart = function(e) {
                    e.preventDefault();
                    if (e.touches.length === 1) {
                        isDrawing = true;
                        const point = getCanvasCoordinates(e.touches[0].clientX, e.touches[0].clientY);
                        lastX = point.x;
                        lastY = point.y;
                        currentStrokes.push({
                            color: currentColor,
                            width: currentLineWidth,
                            points: [point]
                        });
                    }
                };

                canvas.ontouchmove = function(e) {
                    e.preventDefault();
                    if (!isDrawing || e.touches.length !== 1) return;
                    const point = getCanvasCoordinates(e.touches[0].clientX, e.touches[0].clientY);
                    drawLine(lastX, lastY, point.x, point.y);
                    currentStrokes[currentStrokes.length - 1].points.push(point);
                    lastX = point.x;
                    lastY = point.y;
                };

                canvas.ontouchend = function() {
                    isDrawing = false;
                    updateSignaturePreview();
                    updateConfirmButtonState();
                };
            }

            resizeCanvas();
            setupCanvasEvents();
            updateConfirmButtonState();

            // Ver firma en modal
            const viewSignatureModalEl = document.getElementById('viewSignatureModal');
            const viewSignatureContentEl = document.getElementById('viewSignatureContent');
            const viewSignatureModal = viewSignatureModalEl ? new bootstrap.Modal(viewSignatureModalEl) : null;
            const viewSignatureSignerEl = document.getElementById('viewSignatureSigner');
            const viewSignatureExtraEl = document.getElementById('viewSignatureExtra');
            const bindSignatureViewButtons = () => {
                if (!dashboardEl || !viewSignatureModal || !viewSignatureContentEl) return;
                dashboardEl.querySelectorAll('.btn-signature-view').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const signature = btn.dataset.signature ? JSON.parse(btn.dataset
                            .signature) : '';
                        const signerName = btn.dataset.signer || '';
                        const isTitular = btn.dataset.titularidad === '1';
                        const titularName = btn.dataset.titularName || '';
                        const titularDni = btn.dataset.titularDni || '';
                        const parentesco = btn.dataset.parentesco || '';
                        const evidence = btn.dataset.evidence ? JSON.parse(btn.dataset.evidence) : '';

                        viewSignatureExtraEl.style.display = 'none';
                        viewSignatureExtraEl.innerHTML = '';
                        if (isTitular && (titularName || titularDni)) {
                            const titularLabel = [titularName, titularDni].filter(Boolean).join(
                                ' - ');
                            viewSignatureSignerEl.style.display = 'block';
                            viewSignatureSignerEl.textContent =
                                `Firmado por el titular: ${titularLabel}`;
                        } else if (!isTitular && parentesco) {
                            viewSignatureSignerEl.style.display = 'block';
                            viewSignatureSignerEl.textContent =
                                `Firmado por: ${parentesco || 'No titular'}`;
                        } else if (signerName) {
                            viewSignatureSignerEl.style.display = 'block';
                            viewSignatureSignerEl.textContent = `Firmado por: ${signerName}`;
                        } else {
                            viewSignatureSignerEl.style.display = 'none';
                            viewSignatureSignerEl.textContent = '';
                        }
                        if (evidence) {
                            viewSignatureExtraEl.style.display = 'block';
                            viewSignatureExtraEl.innerHTML =
                                `<div class="text-muted small mb-2">Evidencia</div>` +
                                `<img src="${evidence}" alt="Evidencia" class="img-fluid rounded">`;
                        }
                        viewSignatureContentEl.innerHTML = signature ? signature :
                            '<p class="text-muted mb-0">No hay firma disponible.</p>';
                        viewSignatureModal.show();
                    });
                });
            };

            const viewCartaPoderModalEl = document.getElementById('viewCartaPoderModal');
            const viewCartaPoderContentEl = document.getElementById('viewCartaPoderContent');
            const viewCartaPoderModal = viewCartaPoderModalEl ? new bootstrap.Modal(viewCartaPoderModalEl) : null;
            const bindCartaPoderButtons = () => {
                if (!dashboardEl || !viewCartaPoderModal || !viewCartaPoderContentEl) return;
                dashboardEl.querySelectorAll('.btn-carta-poder-view').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const carta = btn.dataset.carta ? JSON.parse(btn.dataset.carta) : '';
                        if (!carta) {
                            viewCartaPoderContentEl.innerHTML =
                                '<p class="text-muted mb-0">No hay carta poder disponible.</p>';
                        } else if (carta.startsWith('data:application/pdf')) {
                            viewCartaPoderContentEl.innerHTML =
                                `<iframe src="${carta}" style="width: 100%; height: 60vh;" frameborder="0"></iframe>`;
                        } else {
                            viewCartaPoderContentEl.innerHTML =
                                `<img src="${carta}" alt="Carta poder" class="img-fluid rounded">`;
                        }
                        viewCartaPoderModal.show();
                    });
                });
            };

            const bindDashboardHandlers = () => {
                bindEditButtons();
                bindRejectButtons();
                bindSignButtons();
                bindSignatureViewButtons();
                bindCartaPoderButtons();
                bindTabPersistence();
                bindDeleteButtons();
            };

            const refreshDashboard = () => {
                if (!dashboardEl || !refreshUrl) return;
                if (userInteracting) return;
                const activeEl = document.activeElement;
                if (
                    activeEl &&
                    dashboardEl.contains(activeEl) &&
                    (activeEl.matches('input, textarea, select') ||
                        activeEl.classList.contains('select2-search__field'))
                ) {
                    return;
                }
                const activeTabId = getActiveTabId();
                const url = new URL(refreshUrl, window.location.origin);
                url.search = window.location.search;
                fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error al actualizar');
                        }
                        return response.text();
                    })
                    .then(html => {
                        dashboardEl.innerHTML = html;
                        bindDashboardHandlers();
                        restoreActiveTab(activeTabId);
                    })
                    .catch(() => {});
            };

            const markUserInteracting = () => {
                userInteracting = true;
                if (interactionTimeoutId) {
                    clearTimeout(interactionTimeoutId);
                }
                interactionTimeoutId = setTimeout(() => {
                    userInteracting = false;
                }, 1500);
            };

            bindDashboardHandlers();
            restoreActiveTab(getSavedTabId());
            setInterval(refreshDashboard, refreshIntervalSeconds * 1000);

            if (dashboardEl) {
                dashboardEl.addEventListener('input', markUserInteracting);
                dashboardEl.addEventListener('change', markUserInteracting);
                dashboardEl.addEventListener('focusin', markUserInteracting);
                dashboardEl.addEventListener('keydown', markUserInteracting);
            }
        });
