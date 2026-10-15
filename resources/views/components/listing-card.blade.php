@props(['listing'])

<a href="{{ route('listings.show', $listing) }}" class="lm-card" id="listing-card-{{ $listing->id }}">
    <div class="lm-card-img-wrap">
        <img src="{{ $listing->image_url }}" alt="{{ $listing->title }}" class="lm-card-img" loading="lazy" onerror="this.src='{{ asset('images/placeholder.svg') }}'">
        
        <div class="d-flex flex-column gap-1 position-absolute top-0 start-0 m-2" style="z-index: 2;">
            @if($listing->is_featured)
                <span class="lm-card-badge lm-badge-featured shadow-sm">
                    <i class="bi bi-star-fill text-dark me-1"></i> Featured
                </span>
            @endif

            @if($listing->listing_type === 'service')
                <span class="lm-card-badge lm-badge-service shadow-sm">
                    <i class="bi bi-tools me-1"></i> Service
                </span>
            @endif
        </div>

        @if($listing->status === 'sold')
            <div class="position-absolute top-0 end-0 m-2" style="z-index: 2;">
                <span class="badge bg-danger shadow-sm text-uppercase">Sold</span>
            </div>
        @endif
    </div>

    <div class="lm-card-body">
        <div class="lm-card-price">{{ $listing->formatted_price }}</div>
        <div class="lm-card-title" title="{{ $listing->title }}">{{ $listing->title }}</div>

        <div class="lm-card-footer">
            <span class="text-truncate me-2" title="{{ $listing->full_location }}">
                <i class="bi bi-geo-alt me-1 text-secondary"></i>{{ $listing->city }}@if($listing->state), {{ $listing->state }}@endif
            </span>
            <span class="text-nowrap">{{ $listing->created_at->diffForHumans(null, true) }}</span>
        </div>
    </div>
</a>
