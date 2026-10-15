@extends('layouts.app')

@section('title', 'My Account & Dashboard — LocalMarket')

@section('content')

<div class="row g-4 mb-5">
    
    <!-- Left Column: User Profile Summary -->
    <div class="col-lg-4">
        
        <!-- Profile Card -->
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
            <div class="text-center pb-3 border-bottom mb-3">
                <div class="mx-auto mb-3 rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold fs-2" style="width: 72px; height: 72px;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h4 class="fw-bold text-dark mb-0">{{ $user->name }}</h4>
                <div class="text-muted small">{{ $user->email }}</div>
                @if($user->phone)
                    <div class="text-muted small mt-1"><i class="bi bi-telephone me-1"></i> {{ $user->phone }}</div>
                @endif
                @if($user->city)
                    <div class="badge bg-light text-dark border mt-2">
                        <i class="bi bi-geo-alt me-1 text-danger"></i> {{ $user->city }}
                    </div>
                @endif
            </div>

            <div class="d-flex justify-content-between text-muted small mb-2">
                <span>Member Since:</span>
                <span class="fw-semibold text-dark">{{ $user->created_at->format('M d, Y') }}</span>
            </div>
            <div class="d-flex justify-content-between text-muted small mb-3">
                <span>Total Ad Views:</span>
                <span class="fw-semibold text-dark">{{ $viewsTotal }}</span>
            </div>

            <a href="{{ route('listings.create') }}" class="btn btn-warning w-100 fw-bold py-2 shadow-sm text-dark">
                <i class="bi bi-plus-circle-fill me-1"></i> Post New Listing
            </a>
        </div>

        <!-- Metric Counter Cards -->
        <div class="row g-2 mb-4">
            <div class="col-4">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3 text-center">
                    <div class="fs-4 fw-extrabold text-primary">{{ $totalCount }}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">Total Ads</div>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3 text-center">
                    <div class="fs-4 fw-extrabold text-success">{{ $activeCount }}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">Active</div>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3 text-center">
                    <div class="fs-4 fw-extrabold text-secondary">{{ $soldCount }}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">Sold</div>
                </div>
            </div>
        </div>

    </div>

    <!-- Right Column: My Listings -->
    <div class="col-lg-8">
        
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
            
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 pb-3 border-bottom mb-4">
                <div>
                    <h3 class="h5 fw-bold text-dark mb-1">My Published Ads</h3>
                    <div class="text-muted small">Manage, update, or remove your advertisements</div>
                </div>

                <!-- Status Filter Tabs -->
                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('dashboard', ['status' => 'all']) }}" class="btn {{ $status === 'all' ? 'btn-dark' : 'btn-outline-secondary' }}">
                        All ({{ $totalCount }})
                    </a>
                    <a href="{{ route('dashboard', ['status' => 'active']) }}" class="btn {{ $status === 'active' ? 'btn-dark' : 'btn-outline-secondary' }}">
                        Active ({{ $activeCount }})
                    </a>
                    <a href="{{ route('dashboard', ['status' => 'sold']) }}" class="btn {{ $status === 'sold' ? 'btn-dark' : 'btn-outline-secondary' }}">
                        Sold ({{ $soldCount }})
                    </a>
                </div>
            </div>

            @if($listings->isEmpty())
                <div class="text-center py-5">
                    <div class="mx-auto mb-3 text-muted fs-1"><i class="bi bi-inbox"></i></div>
                    <h5 class="fw-bold text-dark mb-1">No listings in this tab</h5>
                    <p class="text-muted small mb-3">You don't have any {{ $status !== 'all' ? $status : '' }} listings right now.</p>
                    <a href="{{ route('listings.create') }}" class="btn btn-primary fw-bold px-4">
                        <i class="bi bi-plus-lg me-1"></i> Post Your First Ad
                    </a>
                </div>
            @else
                <div class="d-flex flex-column gap-3">
                    @foreach($listings as $listing)
                        <div class="card border rounded-3 p-3 position-relative hover-shadow" id="user-listing-{{ $listing->id }}">
                            <div class="row align-items-center g-3">
                                
                                <!-- Image -->
                                <div class="col-auto">
                                    <img src="{{ $listing->image_url }}" alt="{{ $listing->title }}" class="rounded" style="width: 90px; height: 75px; object-fit: cover;" onerror="this.src='{{ asset('images/placeholder.svg') }}'">
                                </div>

                                <!-- Info -->
                                <div class="col">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge {{ $listing->status === 'active' ? 'bg-success' : ($listing->status === 'sold' ? 'bg-danger' : 'bg-secondary') }} text-uppercase" style="font-size: 0.65rem;">
                                            {{ $listing->status }}
                                        </span>
                                        <span class="badge bg-light text-dark border" style="font-size: 0.65rem;">
                                            {{ ucfirst($listing->listing_type) }}
                                        </span>
                                        <span class="small text-muted ms-auto">
                                            <i class="bi bi-eye"></i> {{ $listing->views_count }} views
                                        </span>
                                    </div>

                                    <h6 class="fw-bold text-dark mb-1">
                                        <a href="{{ route('listings.show', $listing) }}" class="text-decoration-none text-dark">
                                            {{ $listing->title }}
                                        </a>
                                    </h6>

                                    <div class="text-primary fw-bold">{{ $listing->formatted_price }}</div>
                                    <div class="small text-muted">
                                        <i class="bi bi-geo-alt"></i> {{ $listing->city }} &bull; Posted {{ $listing->created_at->diffForHumans() }}
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="col-12 col-md-auto d-flex flex-wrap gap-1 border-top pt-2 pt-md-0 border-md-0">
                                    
                                    <!-- View -->
                                    <a href="{{ route('listings.show', $listing) }}" class="btn btn-sm btn-outline-secondary" title="View Ad">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('listings.edit', $listing) }}" class="btn btn-sm btn-outline-primary" title="Edit Ad">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>

                                    <!-- Toggle Sold/Active -->
                                    <form action="{{ route('dashboard.toggle-status', $listing) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $listing->status === 'active' ? 'btn-outline-warning text-dark' : 'btn-outline-success' }}" title="Toggle Status">
                                            {{ $listing->status === 'active' ? 'Mark Sold' : 'Reactivate' }}
                                        </button>
                                    </form>

                                    <!-- Delete -->
                                    <form action="{{ route('listings.destroy', $listing) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete this ad?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Ad">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>

                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $listings->links() }}
                </div>
            @endif

        </div>

    </div>
</div>

@endsection
