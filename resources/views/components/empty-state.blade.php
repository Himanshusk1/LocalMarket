@props([
    'icon' => 'bi-search',
    'title' => 'No Listings Found',
    'message' => 'We couldn\'t find any listings matching your search or filters.',
    'actionUrl' => route('browse'),
    'actionText' => 'Clear All Filters',
])

<div class="card border-0 shadow-sm rounded-3 py-5 px-4 text-center my-4 bg-white">
    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background-color: #f1f5f9; color: var(--lm-primary);">
        <i class="bi {{ $icon }} fs-1"></i>
    </div>
    <h4 class="fw-bold text-dark mb-2">{{ $title }}</h4>
    <p class="text-muted mx-auto mb-4" style="max-width: 480px;">{{ $message }}</p>
    @if($actionUrl && $actionText)
        <div>
            <a href="{{ $actionUrl }}" class="btn btn-outline-dark px-4 py-2 rounded-pill fw-semibold">
                {{ $actionText }}
            </a>
        </div>
    @endif
</div>
