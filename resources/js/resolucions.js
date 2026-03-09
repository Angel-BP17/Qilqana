            $(document).ready(function() {
                // Mostrar nombre de archivo seleccionado
                $('.custom-file-input').on('change', function() {
                    let fileName = $(this).val().split('\\').pop();
                    $(this).next('.custom-file-label').addClass("selected").html(fileName);
                });

                // Configurar modal de detalles
                $('#detailModal').on('show.bs.modal', function(event) {
                    var button = $(event.relatedTarget);

                    $('#modal-rd').text(button.data('rd'));
                    $('#modal-fecha').text(button.data('fecha'));
                    $('#modal-dni').text(button.data('dni'));
                    $('#modal-apellidos').text(button.data('apellidos'));
                    $('#modal-nombres').text(button.data('nombres'));
                    $('#modal-asunto').text(button.data('asunto'));
                    $('#modal-proyecto').text(button.data('proyecto'));
                    $('#modal-expediente').text(button.data('expediente'));
                    $('#modal-fecha2').text(button.data('fecha2'));
                    $('#modal-folios').text(button.data('folios'));
                });
            });

            window.addEventListener('load', () => {
                const signModalElement = document.getElementById('signChargeModal');
                const signModal = signModalElement ? new bootstrap.Modal(signModalElement) : null;
                const signForm = document.getElementById('signChargeForm');
                const signExternalFields = document.getElementById('sign_external_fields');
                const signTitularidad = document.getElementById('sign_titularidad');
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

                if (!signModal || !signForm || !canvas) return;

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

                function resetExternalInputs() {
                    if (!signTitularidad) return;
                    signTitularidad.checked = false;
                    if (signParentesco) signParentesco.value = '';
                    if (signCartaPoder) signCartaPoder.value = '';
                    if (signParentesco) signParentesco.required = false;
                    if (signCartaPoder) signCartaPoder.required = false;
                    if (signParentescoGroup) signParentescoGroup.classList.add('d-none');
                    if (signCartaPoderGroup) signCartaPoderGroup.classList.add('d-none');
                }

                function updateTitularidadState() {
                    if (!signTitularidad) return;
                    const showExtra = !signTitularidad.checked;
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

                function showExternalOptional() {
                    if (!signExternalFields) return;
                    signExternalFields.classList.remove('d-none');
                    if (signParentescoGroup) signParentescoGroup.classList.remove('d-none');
                    if (signCartaPoderGroup) signCartaPoderGroup.classList.remove('d-none');
                    if (signParentesco) signParentesco.required = false;
                    if (signCartaPoder) signCartaPoder.required = false;
                    if (signTitularidad) signTitularidad.checked = false;
                    if (signParentesco) signParentesco.value = '';
                    if (signCartaPoder) signCartaPoder.value = '';
                }

                if (signTitularidad) {
                    signTitularidad.addEventListener('change', () => {
                        updateTitularidadState();
                    });
                }

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
                });

                const bindSignButtons = () => {
                    document.querySelectorAll('.btn-sign-resolution').forEach(btn => {
                        btn.addEventListener('click', () => {
                            const charge = btn.dataset.charge ? JSON.parse(btn.dataset.charge) :
                                null;
                            const forceExternal = btn.dataset.showExternal === '1';
                            existingSignatureContent = btn.dataset.signature ? JSON.parse(btn
                                .dataset.signature) : '';
                            signForm.action = btn.dataset.action;
                            const isSigned = charge?.signature?.signature_status === 'firmado';
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
                            if (forceExternal) {
                                showExternalOptional();
                            } else {
                                toggleSignatureExternalFields(charge?.tipo_interesado ||
                                    '');
                            }
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
                        updateConfirmButtonState();
                    };
                }

                setupCanvasEvents();
                bindSignButtons();
                updateConfirmButtonState();
            });

            window.addEventListener('load', () => {
                const deleteModalElement = document.getElementById('deleteResolutionModal');
                const deleteModal = deleteModalElement ? new bootstrap.Modal(deleteModalElement) : null;
                const deleteForm = document.getElementById('deleteResolutionForm');
                const deleteReason = document.getElementById('delete_resolution_reason');

                document.querySelectorAll('.btn-delete-resolution').forEach(btn => {
                    btn.addEventListener('click', () => {
                        if (!deleteModal || !deleteForm) return;
                        deleteForm.action = btn.dataset.action;
                        if (deleteReason) {
                            deleteReason.value = '';
                        }
                        deleteModal.show();
                    });
                });
            });

            window.addEventListener('load', () => {
                const editModalElement = document.getElementById('editResolutionModal');
                const editModal = editModalElement ? new bootstrap.Modal(editModalElement) : null;
                const editForm = document.getElementById('editResolutionForm');
                const editRd = document.getElementById('edit_resolution_rd');
                const editFecha = document.getElementById('edit_resolution_fecha');
                const editDni = document.getElementById('edit_resolution_dni');
                const editNombres = document.getElementById('edit_resolution_nombres');
                const editProcedencia = document.getElementById('edit_resolution_procedencia');
                const editAsunto = document.getElementById('edit_resolution_asunto');

                document.querySelectorAll('.btn-edit-resolution').forEach(btn => {
                    btn.addEventListener('click', () => {
                        if (!editModal || !editForm) return;
                        editForm.action = btn.dataset.action;
                        if (editRd) editRd.value = btn.dataset.rd || '';
                        if (editFecha) editFecha.value = btn.dataset.fecha || '';
                        if (editDni) editDni.value = btn.dataset.dni || '';
                        if (editNombres) editNombres.value = btn.dataset.nombres || '';
                        if (editProcedencia) editProcedencia.value = btn.dataset.procedencia || '';
                        if (editAsunto) editAsunto.value = btn.dataset.asunto || '';
                        editModal.show();
                    });
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
                const fileInput = document.querySelector('[data-import-input="1"]');
                const importBtn = document.getElementById('importExcelButton');
                if (!fileInput || !importBtn) return;
                const toggleImportButton = () => {
                    importBtn.disabled = !fileInput.files || fileInput.files.length === 0;
                };
                toggleImportButton();
                fileInput.addEventListener('change', toggleImportButton);
            });
