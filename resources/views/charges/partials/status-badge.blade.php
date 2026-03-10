@if ($status === 'firmado')
    <span class="badge bg-primary">
        <i class="fa-solid fa-circle-check me-1"></i>Firmado
    </span>
@elseif ($status === 'rechazado')
    <span class="badge bg-danger">
        <i class="fa-solid fa-circle-xmark me-1"></i>Rechazado
    </span>
@else
    <span class="badge bg-warning text-dark">
        <i class="fa-solid fa-hourglass-half me-1"></i>Pendiente
    </span>
@endif
