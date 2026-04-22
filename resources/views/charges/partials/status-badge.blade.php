@if ($status === 'firmado')
    <span class="badge bg-primary">
        <span class="material-symbols-outlined me-1">check_circle</span>Firmado
    </span>
@elseif ($status === 'rechazado')
    <span class="badge bg-danger">
        <span class="material-symbols-outlined me-1">cancel</span>Rechazado
    </span>
@else
    <span class="badge bg-warning text-dark">
        <span class="material-symbols-outlined me-1">hourglass_empty</span>Pendiente
    </span>
@endif
