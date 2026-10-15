@extends('layouts.app')

@section('title', $pageTitle . ' — LocalMarket')
@section('meta_description', 'Search and explore active classified listings for products and services on LocalMarket.')

@section('content')

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('browse') }}">Browse</a></li>
        @if(isset($normalizedCity))
            <li class="breadcrumb-item"><a href="{{ route('city.show', ['city' => strtolower($normalizedCity)]) }}">{{ ucfirst($normalizedCity) }}</a></li>
        @endif
        @if(isset($selectedCategory))
            <li class="breadcrumb-item active" aria-current="page">{{ $selectedCategory->name }}</li>
        @elseif(isset($filters['q']) && !empty($filters['q']))
            <li class="breadcrumb-item active" aria-current="page">"{{ $filters['q'] }}"</li>
        @endif
    </ol>
</nav>

<div class="row g-4">
    
    <!-- Sidebar Filters (Desktop) -->
    <aside class="col-lg-3 d-none d-lg-block">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white sticky-top" style="top: 80px; z-index: 10;">
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                <h5 class="fw-bold text-dark mb-0 fs-6">
                    <i class="bi bi-funnel me-1 text-primary"></i> Filters
                </h5>
                <a href="{{ route('browse') }}" class="btn btn-sm btn-link text-decoration-none text-muted small p-0">
                    Reset All
                </a>
            </div>

            <form action="{{ route('browse') }}" method="GET" id="desktopFilterForm">
                
                <!-- Keyword -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Keywords</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="q" class="form-control" placeholder="Search title or text..." value="{{ $filters['q'] ?? '' }}">
                        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </div>

                <!-- City / Location -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">City / Location</label>
                    <select name="city" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All India</option>
                        @foreach($cities as $c)
                            <option value="{{ $c->city }}" {{ (isset($filters['city']) && strtolower($filters['city']) === strtolower($c->city)) ? 'selected' : '' }}>
                                {{ $c->city }} ({{ $c->count }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Listing Type (Product vs Service) -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Listing Type</label>
                    <div class="d-flex flex-column gap-1 small">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" id="type_all" value="" {{ empty($filters['type']) ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="form-check-label" for="type_all">All Listings</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" id="type_product" value="product" {{ (isset($filters['type']) && $filters['type'] === 'product') ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="form-check-label" for="type_product">Products Only</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" id="type_service" value="service" {{ (isset($filters['type']) && $filters['type'] === 'service') ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="form-check-label" for="type_service">Services Only</label>
                        </div>
                    </div>
                </div>

                <!-- Categories -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Category</label>
                    <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}" {{ (isset($filters['category']) && $filters['category'] === $cat->slug) ? 'selected' : '' }}>
                                {{ $cat->name }} ({{ $cat->listings_count }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Subcategories if Category Selected -->
                @if($subcategories->isNotEmpty())
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Subcategory</label>
                        <select name="subcategory" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All {{ $selectedCategory ? $selectedCategory->name : 'Subcategories' }}</option>
                            @foreach($subcategories as $sub)
                                <option value="{{ $sub->slug }}" {{ (isset($filters['subcategory']) && $filters['subcategory'] === $sub->slug) ? 'selected' : '' }}>
                                    {{ $sub->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Price Range -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Price Range (₹)</label>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Min" value="{{ $filters['min_price'] ?? '' }}" min="0">
                        </div>
                        <div class="col-6">
                            <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Max" value="{{ $filters['max_price'] ?? '' }}" min="0">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-outline-dark w-100 fw-semibold">
                        Apply Price Filter
                    </button>
                </div>

                <!-- Hidden Sort Field -->
                @if(isset($filters['sort']))
                    <input type="hidden" name="sort" value="{{ $filters['sort'] }}">
                @endif

                <div class="mt-3 pt-3 border-top">
                    <a href="{{ route('browse') }}" class="btn btn-light w-100 btn-sm text-muted">
                        Clear All Filters
                    </a>
                </div>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="col-lg-9">
        
        <!-- Header Bar: Results title, Mobile Filter Trigger, and Sort -->
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-3 bg-white p-3 rounded-3 shadow-sm border">
            <div>
                <h1 class="h5 fw-bold text-dark mb-0">{{ $pageTitle }}</h1>
                <div class="text-muted small">
                    Showing {{ $listings->total() }} {{ Str::plural('listing', $listings->total()) }}
                    @if(isset($filters['city']) && !empty($filters['city']))
                        in <span class="fw-semibold text-dark">{{ ucfirst($filters['city']) }}</span>
                    @endif
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- Mobile Filter Button -->
                <button class="btn btn-outline-secondary btn-sm d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileFilterDrawer" aria-controls="mobileFilterDrawer">
                    <i class="bi bi-funnel"></i> Filters
                </button>

                <!-- Sorting Dropdown -->
                <form action="{{ url()->current() }}" method="GET" class="d-flex align-items-center gap-1" id="sortForm">
                    <!-- Preserve existing active query parameters -->
                    @foreach($filters as $k => $v)
                        @if($k !== 'sort' && !empty($v))
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endif
                    @endforeach

                    <label class="small text-muted text-nowrap d-none d-sm-inline" for="sortSelect">Sort by:</label>
                    <select name="sort" id="sortSelect" class="form-select form-select-sm" style="min-width: 150px;" onchange="this.form.submit()">
                        <option value="newest" {{ (!isset($filters['sort']) || $filters['sort'] === 'newest') ? 'selected' : '' }}>Newest First</option>
                        <option value="price_asc" {{ (isset($filters['sort']) && $filters['sort'] === 'price_asc') ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ (isset($filters['sort']) && $filters['sort'] === 'price_desc') ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="popular" {{ (isset($filters['sort']) && $filters['sort'] === 'popular') ? 'selected' : '' }}>Most Popular</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- Active Filter Pills -->
        @php
            $hasActiveFilters = array_filter($filters, function($v) { return $v !== null && $v !== ''; });
        @endphp
        @if(!empty($hasActiveFilters))
            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                <span class="small text-muted fw-bold">Active Filters:</span>
                
                @if(!empty($filters['q']))
                    <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1 py-1 px-2">
                        Keyword: {{ $filters['q'] }}
                        <a href="{{ request()->fullUrlWithQuery(['q' => null]) }}" class="text-danger"><i class="bi bi-x"></i></a>
                    </span>
                @endif

                @if(!empty($filters['city']))
                    <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1 py-1 px-2">
                        City: {{ ucfirst($filters['city']) }}
                        <a href="{{ request()->fullUrlWithQuery(['city' => null]) }}" class="text-danger"><i class="bi bi-x"></i></a>
                    </span>
                @endif

                @if(!empty($filters['category']))
                    <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1 py-1 px-2">
                        Category: {{ $selectedCategory ? $selectedCategory->name : $filters['category'] }}
                        <a href="{{ request()->fullUrlWithQuery(['category' => null, 'subcategory' => null]) }}" class="text-danger"><i class="bi bi-x"></i></a>
                    </span>
                @endif

                @if(!empty($filters['subcategory']))
                    <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1 py-1 px-2">
                        Subcategory: {{ $filters['subcategory'] }}
                        <a href="{{ request()->fullUrlWithQuery(['subcategory' => null]) }}" class="text-danger"><i class="bi bi-x"></i></a>
                    </span>
                @endif

                @if(!empty($filters['type']))
                    <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1 py-1 px-2">
                        Type: {{ ucfirst($filters['type']) }}
                        <a href="{{ request()->fullUrlWithQuery(['type' => null]) }}" class="text-danger"><i class="bi bi-x"></i></a>
                    </span>
                @endif

                @if(!empty($filters['min_price']))
                    <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1 py-1 px-2">
                        Min: ₹{{ number_format($filters['min_price']) }}
                        <a href="{{ request()->fullUrlWithQuery(['min_price' => null]) }}" class="text-danger"><i class="bi bi-x"></i></a>
                    </span>
                @endif

                @if(!empty($filters['max_price']))
                    <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1 py-1 px-2">
                        Max: ₹{{ number_format($filters['max_price']) }}
                        <a href="{{ request()->fullUrlWithQuery(['max_price' => null]) }}" class="text-danger"><i class="bi bi-x"></i></a>
                    </span>
                @endif

                <a href="{{ route('browse') }}" class="small text-danger ms-2 text-decoration-none fw-semibold">
                    Clear All
                </a>
            </div>
        @endif

        <!-- Listings Grid -->
        @if($listings->isEmpty())
            <x-empty-state 
                title="No Listings Found"
                message="No active ads match your current search criteria or location filters. Try clearing some filters or searching for something else."
                :actionUrl="route('browse')"
                actionText="View All Listings"
            />
        @else
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3 mb-4">
                @foreach($listings as $listing)
                    <div class="col">
                        <x-listing-card :listing="$listing" />
                    </div>
                @endforeach
            </div>

            <!-- Preserved Filter Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $listings->links() }}
            </div>
        @endif

    </main>
</div>

<!-- Mobile Offcanvas Filter Drawer -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileFilterDrawer" aria-labelledby="mobileFilterDrawerLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold" id="mobileFilterDrawerLabel"><i class="bi bi-funnel me-1"></i> Filter Listings</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('browse') }}" method="GET">
            <div class="mb-3">
                <label class="form-label small fw-bold">Keywords</label>
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Search title or text..." value="{{ $filters['q'] ?? '' }}">
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">City</label>
                <select name="city" class="form-select form-select-sm">
                    <option value="">All India</option>
                    @foreach($cities as $c)
                        <option value="{{ $c->city }}" {{ (isset($filters['city']) && strtolower($filters['city']) === strtolower($c->city)) ? 'selected' : '' }}>
                            {{ $c->city }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Listing Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="product" {{ (isset($filters['type']) && $filters['type'] === 'product') ? 'selected' : '' }}>Products Only</option>
                    <option value="service" {{ (isset($filters['type']) && $filters['type'] === 'service') ? 'selected' : '' }}>Services Only</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Category</label>
                <select name="category" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ (isset($filters['category']) && $filters['category'] === $cat->slug) ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Price Range (₹)</label>
                <div class="row g-2">
                    <div class="col-6">
                        <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Min" value="{{ $filters['min_price'] ?? '' }}">
                    </div>
                    <div class="col-6">
                        <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Max" value="{{ $filters['max_price'] ?? '' }}">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Apply Filters</button>
            <a href="{{ route('browse') }}" class="btn btn-light w-100 mt-2 text-muted">Clear Filters</a>
        </form>
    </div>
</div>

@endsection
