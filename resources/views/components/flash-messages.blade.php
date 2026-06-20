@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 border-start border-danger border-4 d-flex align-items-start gap-2 mb-3" role="alert">
        <span class="material-symbols-outlined text-danger fs-4 mt-1">error</span>
        <div class="flex-grow-1">
            <h6 class="alert-heading fw-bold mb-1">Por favor corrija los siguientes errores:</h6>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="btn-close ms-auto mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 border-start border-success border-4 d-flex align-items-center gap-2 mb-3" role="alert">
        <span class="material-symbols-outlined text-success fs-4">check_circle</span>
        <div class="flex-grow-1">
            {{ session('success') }}
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 border-start border-danger border-4 d-flex align-items-center gap-2 mb-3" role="alert">
        <span class="material-symbols-outlined text-danger fs-4">error</span>
        <div class="flex-grow-1">
            {{ session('error') }}
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show shadow-sm border-0 border-start border-warning border-4 d-flex align-items-center gap-2 mb-3" role="alert">
        <span class="material-symbols-outlined text-warning fs-4">warning</span>
        <div class="flex-grow-1">
            {{ session('warning') }}
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('errores'))
    <div class="alert alert-warning alert-dismissible fade show shadow-sm border-0 border-start border-warning border-4 d-flex align-items-start gap-2 mb-3" role="alert">
        <span class="material-symbols-outlined text-warning fs-4 mt-1">warning_amber</span>
        <div class="flex-grow-1">
            <h6 class="alert-heading fw-bold mb-1">Errores durante la importación:</h6>
            <ul class="mb-0 ps-3">
                @foreach (session('errores') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="btn-close ms-auto mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
