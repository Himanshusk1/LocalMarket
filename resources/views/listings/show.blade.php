@extends('layouts.app')

@section('title', $listing->title . ' — ' . $listing->city . ' — LocalMarket')
@section('meta_description', Str::limit(strip_tags($listing->description), 150))

@section('content')

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('browse') }}">Browse</a></li>
        <li class="breadcrumb-item"><a href="{{ route('category.show', $listing->category) }}">{{ $listing->category->name }}</a></li>
        @if($listing->subcategory)
            <li class="breadcrumb-item"><a href="{{ route('browse', ['category' => $listing->category->slug, 'subcategory' => $listing->subcategory->slug]) }}">{{ $listing->subcategory->name }}</a></li>
        @endif
        <li class="breadcrumb-item active text-truncate" style="max-width: 250px;" aria-current="page">{{ $listing->title }}</li>
    </ol>
</nav>

<div class="row g-4 mb-5">
    
    <!-- Left Column: Gallery & Details -->
    <div class="col-lg-8">
        
        <!-- Image Gallery -->
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white mb-4">
            <div class="position-relative text-center p-2" style="background-color: #0f172a; min-height: 400px; display: flex; align-items: center; justify-content: center;">
                <img id="mainListingImage" 
                     src="{{ $listing->image_url }}" 
                     alt="{{ $listing->title }}" 
                     class="img-fluid rounded" 
                     style="max-height: 460px; width: auto; object-fit: contain;">

                <!-- Badges -->
                <div class="position-absolute top-0 start-0 m-3 d-flex flex-column gap-1" style="z-index: 5;">
                    @if($listing->is_featured)
                        <span class="badge bg-warning text-dark fw-bold px-3 py-2 shadow-sm text-uppercase">
                            <i class="bi bi-star-fill me-1"></i> Featured
                        </span>
                    @endif
                    <span class="badge {{ $listing->listing_type === 'service' ? 'bg-info text-white' : 'bg-primary' }} fw-bold px-3 py-2 shadow-sm text-uppercase">
                        {{ ucfirst($listing->listing_type) }}
                    </span>
                    @if($listing->status === 'sold')
                        <span class="badge bg-danger text-white fw-bold px-3 py-2 shadow-sm text-uppercase">
                            Sold Out
                        </span>
                    @endif
                </div>
            </div>

            <!-- Thumbnail Strip if multiple images -->
            @if($listing->images->count() > 1)
                <div class="p-3 bg-light border-top d-flex align-items-center gap-2 overflow-auto">
                    @foreach($listing->images as $index => $img)
                        <img src="{{ $img->url }}" 
                             alt="Thumbnail {{ $index + 1 }}" 
                             class="lm-details-thumb {{ $index === 0 ? 'active' : '' }}" 
                             onclick="switchMainImage(this.src, this)">
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Description & Specifications Card -->
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
            <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">Description</h4>
            <div class="text-secondary lh-base mb-4" style="white-space: pre-line; font-size: 1rem;">
                {{ $listing->description }}
            </div>

            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Overview & Details</h5>
            <div class="row g-3 small">
                <div class="col-sm-6">
                    <div class="p-2 bg-light rounded d-flex justify-content-between">
                        <span class="text-muted">Type:</span>
                        <span class="fw-bold text-capitalize">{{ $listing->listing_type }}</span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="p-2 bg-light rounded d-flex justify-content-between">
                        <span class="text-muted">Category:</span>
                        <span class="fw-bold">{{ $listing->category->name }}</span>
                    </div>
                </div>
                @if($listing->subcategory)
                    <div class="col-sm-6">
                        <div class="p-2 bg-light rounded d-flex justify-content-between">
                            <span class="text-muted">Subcategory:</span>
                            <span class="fw-bold">{{ $listing->subcategory->name }}</span>
                        </div>
                    </div>
                @endif
                <div class="col-sm-6">
                    <div class="p-2 bg-light rounded d-flex justify-content-between">
                        <span class="text-muted">Ad ID:</span>
                        <span class="fw-bold text-monospace">#LM-{{ $listing->id }}</span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="p-2 bg-light rounded d-flex justify-content-between">
                        <span class="text-muted">Posted:</span>
                        <span class="fw-bold">{{ $listing->created_at->format('M d, Y') }} ({{ $listing->created_at->diffForHumans() }})</span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="p-2 bg-light rounded d-flex justify-content-between">
                        <span class="text-muted">Views:</span>
                        <span class="fw-bold">{{ $listing->views_count }} {{ Str::plural('view', $listing->views_count) }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Right Column: Price & Seller Info -->
    <div class="col-lg-4">
        
        <!-- Price & Title Box -->
        <div class="lm-price-box shadow-sm mb-4">
            <div class="text-muted small text-uppercase fw-bold mb-1">
                {{ $listing->listing_type === 'service' ? 'Service Rate' : 'Selling Price' }}
            </div>
            <div class="display-6 fw-extrabold text-primary mb-2">
                {{ $listing->formatted_price }}
            </div>
            <h1 class="h5 fw-bold text-dark mb-3">{{ $listing->title }}</h1>
            
            <div class="d-flex align-items-center text-muted small border-top pt-3">
                <i class="bi bi-geo-alt-fill text-danger me-1 fs-5"></i>
                <div>
                    <div class="fw-semibold text-dark">{{ $listing->full_location }}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">{{ $listing->country }}</div>
                </div>
            </div>
        </div>

        <!-- Owner Controls (if current user owns this listing) -->
        @can('update', $listing)
            <div class="card border-primary border-opacity-25 bg-primary bg-opacity-10 rounded-3 p-3 shadow-sm mb-4">
                <div class="fw-bold text-primary mb-2 d-flex align-items-center">
                    <i class="bi bi-person-gear me-2"></i> Manage Your Listing
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('listings.edit', $listing) }}" class="btn btn-outline-primary btn-sm flex-grow-1 fw-bold">
                        <i class="bi bi-pencil-square me-1"></i> Edit Ad
                    </a>
                    
                    <button type="button" class="btn btn-outline-danger btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#deleteAdModal">
                        <i class="bi bi-trash3 me-1"></i> Delete
                    </button>
                </div>
            </div>
        @endcan

        <!-- Seller Profile Card -->
        <div class="lm-seller-box shadow-sm mb-4">
            <div class="text-muted small text-uppercase fw-bold mb-3">Seller Information</div>
            
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 56px; height: 56px;">
                    {{ strtoupper(substr($listing->user->name, 0, 1)) }}
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0">{{ $listing->user->name }}</h5>
                    <div class="small text-muted">
                        <i class="bi bi-check-circle-fill text-success"></i> Verified Member
                    </div>
                    <div class="text-muted" style="font-size: 0.75rem;">
                        Member since {{ $listing->user->created_at->format('M Y') }}
                    </div>
                </div>
            </div>

            <!-- Contact Seller Action -->
            <button type="button" class="btn btn-primary w-100 py-2 fw-bold mb-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#contactSellerModal" id="contactSellerBtn">
                <i class="bi bi-chat-dots-fill me-2"></i> Contact Seller
            </button>

            @if($listing->contact_phone)
                <button type="button" class="btn btn-outline-dark w-100 py-2 fw-semibold mb-2" onclick="revealPhone(this, '{{ $listing->contact_phone }}')">
                    <i class="bi bi-telephone-fill me-2 text-success"></i> <span id="phoneRevealText">Show Phone Number</span>
                </button>
            @endif

            <div class="small text-muted text-center mt-2">
                <i class="bi bi-clock me-1"></i> Usually responds within a few hours
            </div>
        </div>

        <!-- Safety Guidelines Box -->
        <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
            <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                <i class="bi bi-shield-check text-success fs-5"></i> Safety Tips for Buyers
            </h6>
            <ul class="small text-muted ps-3 mb-0" style="line-height: 1.6;">
                <li>Meet the seller in a safe, public place.</li>
                <li>Check the item thoroughly before making payment.</li>
                <li>Pay only after taking possession of the item.</li>
                <li>Beware of unrealistic offers and advance payments.</li>
            </ul>
        </div>

    </div>
</div>

<!-- Related Listings -->
@if($relatedListings->isNotEmpty())
    <section class="mt-4 mb-5 border-top pt-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold text-dark mb-0">Similar Ads in {{ $listing->category->name }}</h4>
            <a href="{{ route('category.show', $listing->category) }}" class="btn btn-sm btn-link text-decoration-none fw-bold text-primary">
                View more <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
            @foreach($relatedListings as $rel)
                <div class="col">
                    <x-listing-card :listing="$rel" />
                </div>
            @endforeach
        </div>
    </section>
@endif

<!-- Contact Seller Modal -->
<div class="modal fade" id="contactSellerModal" tabindex="-1" aria-labelledby="contactSellerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="contactSellerModalLabel">
                    <i class="bi bi-chat-quote-fill me-2 text-primary"></i> Contact {{ $listing->user->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 bg-light rounded-3 mb-3 d-flex align-items-center gap-3">
                    <img src="{{ $listing->image_url }}" alt="{{ $listing->title }}" class="rounded" style="width: 60px; height: 50px; object-fit: cover;">
                    <div class="overflow-hidden">
                        <div class="fw-bold text-truncate">{{ $listing->title }}</div>
                        <div class="text-primary fw-bold">{{ $listing->formatted_price }}</div>
                    </div>
                </div>

                <!-- Simulation form for demonstration -->
                <div class="mb-3">
                    <label class="form-label small fw-bold">Your Message to Seller</label>
                    <textarea class="form-control" rows="3" id="sellerMessageInput">Hi {{ $listing->user->name }}, I am interested in your ad "{{ $listing->title }}". Is it still available?</textarea>
                </div>

                @if($listing->contact_phone)
                    <div class="mb-3 p-3 bg-light rounded text-center">
                        <div class="text-muted small mb-1">Direct Phone / WhatsApp:</div>
                        <div class="fs-5 fw-bold text-dark text-monospace">{{ $listing->contact_phone }}</div>
                    </div>
                @endif

                <div id="contactSentAlert" class="alert alert-success d-none mb-3 py-2 small">
                    <i class="bi bi-check-circle-fill me-1"></i> Message sent successfully to {{ $listing->user->name }}!
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary fw-bold" onclick="simulateSendMessage()">
                    <i class="bi bi-send-fill me-1"></i> Send Inquiry
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal (Owner Only) -->
@can('delete', $listing)
    <div class="modal fade" id="deleteAdModal" tabindex="-1" aria-labelledby="deleteAdModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold" id="deleteAdModalLabel">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Ad Deletion
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-2">Are you sure you want to permanently delete this listing?</p>
                    <div class="p-2 bg-light rounded text-dark fw-bold mb-3">{{ $listing->title }}</div>
                    <p class="text-muted small mb-0">This action will remove the listing and all its photos permanently from LocalMarket.</p>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('listings.destroy', $listing) }}" method="POST" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger fw-bold">
                            <i class="bi bi-trash3 me-1"></i> Yes, Delete Listing
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endcan

@endsection

@push('scripts')
<script>
    function switchMainImage(src, element) {
        document.getElementById('mainListingImage').src = src;
        document.querySelectorAll('.lm-details-thumb').forEach(t => t.classList.remove('active'));
        element.classList.add('active');
    }

    function revealPhone(button, phone) {
        document.getElementById('phoneRevealText').innerText = phone;
        button.classList.remove('btn-outline-dark');
        button.classList.add('btn-dark');
    }

    function simulateSendMessage() {
        const alert = document.getElementById('contactSentAlert');
        alert.classList.remove('d-none');
        setTimeout(() => {
            const modalEl = document.getElementById('contactSellerModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
            alert.classList.add('d-none');
        }, 1500);
    }
</script>
@endpush
