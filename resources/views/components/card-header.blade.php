@props([
    'title',
    'badge' => null,
    'badgeClass' => 'bg-light text-dark',
    'bg' => 'bg-info',
    'textColor' => 'text-white',
    'colSize' => 'md',
])

<div {{ $attributes->merge(['class' => "card-header {$bg} border-0 py-3"]) }}>
    <div class="row g-3 align-items-center">
        <div class="col-12 col-{{ $colSize }}-auto">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <h5 class="mb-0 fw-bold {{ $textColor }}">{{ $title }}</h5>
                @if($badge !== null)
                    <span class="badge {{ $badgeClass }}">{{ $badge }}</span>
                @endif
                {{ $left ?? '' }}
            </div>
        </div>
        <div class="col-12 col-{{ $colSize }} {{ $colSize === 'xl' || $colSize === 'md' ? 'text-end' : '' }}">
            <div class="d-flex flex-wrap gap-2 justify-content-{{ $colSize }}-end align-items-center">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
