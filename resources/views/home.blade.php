@extends('layouts.app')

@section('title', 'LocalMarket — India\'s Community Classifieds & Marketplace')
@section('meta_description', 'Discover thousands of local classified ads in Gwalior, Delhi, Indore, and across India. Buy used cars, phones, furniture, and book trusted local services.')

@section('content')

<!-- Hero Quick Search Banner -->
<div class="card border-0 rounded-4 overflow-hidden mb-5 text-white shadow-sm" style="background: linear-gradient(135deg, #002f34 0%, #004b53 100%);">
    <div class="card-body p-4 p-md-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge mb-2 px-3 py-2 rounded-pill fw-bold" style="background-color: #ffce32; color: #002f34; font-size: 0.8rem;">
                    <i class="bi bi-shield-check me-1"></i> Verified Local Ads in India
                </span>
                <h1 class="display-6 fw-extrabold text-white mb-2">Buy, Sell & Find Services Near You</h1>
                <p class="text-white-50 mb-4 fs-6">Find great deals on pre-owned phones, vehicles, laptops, flats, or hire trusted electricians and tutors in your city.</p>

                <!-- Search form in Hero -->
                <form action="{{ route('browse') }}" method="GET" class="d-flex flex-column flex-sm-row gap-2 bg-white p-2 rounded-3 shadow">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" class="form-control border-0 shadow-none ps-0" placeholder="What are you looking for? (e.g. Swift, iPhone, AC service)" value="{{ request('q') }}">
                    </div>
                    <div class="d-flex gap-2">
                        <select name="city" class="form-select border-0 shadow-none text-muted" style="min-width: 140px;">
                            <option value="">All Cities</option>
                            @foreach($popularCities as $cityRecord)
                                <option value="{{ $cityRecord->city }}">{{ $cityRecord->city }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary px-4 fw-bold text-nowrap rounded-2">
                            Search
                        </button>
                    </div>
                </form>

                <!-- Quick popular search tags -->
                <div class="d-flex flex-wrap align-items-center gap-2 mt-3 small text-white-50">
                    <span>Popular:</span>
                    <a href="{{ route('browse', ['q' => 'iPhone']) }}" class="badge bg-white bg-opacity-25 text-white text-decoration-none px-2 py-1">iPhone</a>
                    <a href="{{ route('browse', ['q' => 'Swift']) }}" class="badge bg-white bg-opacity-25 text-white text-decoration-none px-2 py-1">Swift</a>
                    <a href="{{ route('browse', ['q' => 'Flat']) }}" class="badge bg-white bg-opacity-25 text-white text-decoration-none px-2 py-1">Flats</a>
                    <a href="{{ route('browse', ['q' => 'Tutor']) }}" class="badge bg-white bg-opacity-25 text-white text-decoration-none px-2 py-1">Home Tutor</a>
                    <a href="{{ route('city.show', 'gwalior') }}" class="badge bg-white bg-opacity-25 text-white text-decoration-none px-2 py-1">Gwalior Ads</a>
                </div>
            </div>
            
            <div class="col-lg-4 d-none d-lg-block text-end">
                <div class="p-3 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-10 text-center">
                    <div class="fs-1 text-warning mb-2"><i class="bi bi-tag-fill"></i></div>
                    <h5 class="fw-bold text-white mb-1">Got something to sell?</h5>
                    <p class="small text-white-50 mb-3">Post your free ad in 2 minutes and connect with thousands of local buyers.</p>
                    <a href="{{ route('listings.create') }}" class="btn btn-warning w-100 fw-bold text-dark py-2 rounded-3 shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> Post Free Ad Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Explore Categories -->
<section class="mb-5">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h2 class="h4 fw-bold text-dark mb-1">Browse Categories</h2>
            <p class="text-muted small mb-0">Select a category to explore active listings in your area</p>
        </div>
        <a href="{{ route('browse') }}" class="btn btn-sm btn-link text-decoration-none fw-bold text-primary">
            View All <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-5 g-3">
        @foreach($categories as $category)
            <div class="col">
                <a href="{{ route('category.show', $category) }}" class="lm-cat-card" id="cat-card-{{ $category->slug }}">
                    <div class="lm-cat-icon">
                        <i class="bi {{ $category->icon ?? 'bi-box-seam' }}"></i>
                    </div>
                    <div class="lm-cat-title">{{ $category->name }}</div>
                    <div class="lm-cat-count">{{ $category->listings_count }} {{ Str::plural('ad', $category->listings_count) }}</div>
                </a>
            </div>
        @endforeach
    </div>
</section>

<!-- Featured Ads Section -->
@if($featuredListings->isNotEmpty())
    <section class="mb-5">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <span class="badge bg-warning text-dark mb-1 fw-bold text-uppercase" style="font-size: 0.7rem;">Verified</span>
                <h2 class="h4 fw-bold text-dark mb-0">Featured Listings</h2>
            </div>
            <a href="{{ route('browse', ['sort' => 'newest']) }}" class="btn btn-sm btn-link text-decoration-none fw-bold text-primary">
                See all featured <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
            @foreach($featuredListings as $listing)
                <div class="col">
                    <x-listing-card :listing="$listing" />
                </div>
            @endforeach
        </div>
    </section>
@endif

<!-- Fresh Recommendations (Recent Listings) -->
<section class="mb-5">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h2 class="h4 fw-bold text-dark mb-1">Fresh Recommendations</h2>
            <p class="text-muted small mb-0">Recently posted items and services ready for immediate deal</p>
        </div>
        <a href="{{ route('browse') }}" class="btn btn-sm btn-link text-decoration-none fw-bold text-primary">
            Explore All Ads ({{ \App\Models\Listing::where('status', 'active')->count() }}) <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    @if($recentListings->isEmpty())
        <x-empty-state 
            title="No Listings Found"
            message="No classified ads have been published yet. Be the first one to post!"
            :actionUrl="route('listings.create')"
            actionText="Post Free Ad Now"
        />
    @else
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
            @foreach($recentListings as $listing)
                <div class="col">
                    <x-listing-card :listing="$listing" />
                </div>
            @endforeach
        </div>
    @endif
</section>

<!-- Popular Indian Cities Bar -->
<section class="mb-5">
    <div class="card border-0 rounded-3 shadow-sm bg-white p-4">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-geo-alt-fill text-danger fs-4"></i>
            <div>
                <h3 class="h5 fw-bold text-dark mb-0">Browse by City</h3>
                <p class="text-muted small mb-0">Find second-hand products and local services in your city</p>
            </div>
        </div>

        <div class="row row-cols-2 row-cols-sm-4 row-cols-lg-8 g-2">
            @foreach($popularCities as $cityRecord)
                <div class="col">
                    <a href="{{ route('city.show', ['city' => strtolower($cityRecord->city)]) }}" 
                       class="btn btn-light w-100 py-2 px-1 text-center border text-truncate"
                       title="{{ $cityRecord->city }}">
                        <div class="fw-bold small text-dark">{{ $cityRecord->city }}</div>
                        <div class="text-muted" style="font-size: 0.72rem;">{{ $cityRecord->count }} {{ Str::plural('ad', $cityRecord->count) }}</div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Hire Trusted Local Services -->
@if($serviceListings->isNotEmpty())
    <section class="mb-5">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <span class="badge bg-info text-white mb-1 fw-bold text-uppercase" style="font-size: 0.7rem;">Verified Pros</span>
                <h2 class="h4 fw-bold text-dark mb-0">Popular Local Services</h2>
            </div>
            <a href="{{ route('browse', ['type' => 'service']) }}" class="btn btn-sm btn-link text-decoration-none fw-bold text-primary">
                View all services <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
            @foreach($serviceListings as $listing)
                <div class="col">
                    <x-listing-card :listing="$listing" />
                </div>
            @endforeach
        </div>
    </section>
@endif

@endsection
