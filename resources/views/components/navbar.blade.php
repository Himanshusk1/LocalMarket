@php
    $categoriesList = \App\Models\Category::orderBy('sort_order')->get();
    $currentCity = request('city', '');
    $popularCitiesList = ['Gwalior', 'Delhi', 'Indore', 'Bhopal', 'Mumbai', 'Bengaluru', 'Jaipur', 'Pune'];
@endphp

<header class="lm-header">
    <div class="container py-2">
        <div class="d-flex align-items-center justify-content-between gap-3">
            
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="lm-brand me-1">
                <i class="bi bi-shop text-primary"></i>
                <span>Local<span style="color: var(--lm-accent);">Market</span></span>
                <span class="lm-brand-badge">India</span>
            </a>

            <!-- City / Location Selector -->
            <div class="dropdown d-none d-md-block">
                <button class="lm-location-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="locationDropdownBtn">
                    <i class="bi bi-geo-alt-fill text-danger"></i>
                    <span id="selectedCityLabel">{{ $currentCity ? ucfirst($currentCity) : 'All India' }}</span>
                </button>
                <ul class="dropdown-menu shadow border-0" aria-labelledby="locationDropdownBtn" style="min-width: 220px;">
                    <li class="px-3 py-1 small text-muted text-uppercase fw-bold">Select City</li>
                    <li>
                        <a class="dropdown-item {{ empty($currentCity) ? 'active' : '' }}" href="{{ route('browse') }}">
                            <i class="bi bi-compass me-2"></i> All India
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    @foreach($popularCitiesList as $cityItem)
                        <li>
                            <a class="dropdown-item {{ strtolower($currentCity) === strtolower($cityItem) ? 'active' : '' }}" 
                               href="{{ route('city.show', ['city' => strtolower($cityItem)]) }}">
                                <i class="bi bi-geo-alt me-2 text-secondary"></i> {{ $cityItem }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Global Search Bar -->
            <form action="{{ route('browse') }}" method="GET" class="lm-search-bar d-none d-lg-flex" role="search" id="headerSearchForm">
                @if($currentCity)
                    <input type="hidden" name="city" value="{{ $currentCity }}">
                @endif
                <input type="text" 
                       name="q" 
                       class="form-control" 
                       placeholder="Find Cars, Mobile Phones, Laptops, Services, Flats..." 
                       value="{{ request('q') }}"
                       aria-label="Search listings">
                <button type="submit" class="lm-search-btn" aria-label="Search">
                    <i class="bi bi-search"></i>
                </button>
            </form>

            <!-- User Auth & Sell Action -->
            <div class="d-flex align-items-center gap-2 ms-auto ms-lg-0">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-link text-decoration-none text-dark fw-bold px-2 py-1">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-dark fw-semibold d-none d-sm-inline-block px-3 py-1">
                        Register
                    </a>
                @else
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 py-1 px-2 border" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="userMenuBtn">
                            <span class="rounded-circle bg-dark text-white d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.8rem; font-weight: 700;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span class="fw-semibold small d-none d-md-inline">{{ Auth::user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userMenuBtn" style="min-width: 200px;">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold">{{ Auth::user()->name }}</div>
                                <div class="small text-muted text-truncate">{{ Auth::user()->email }}</div>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('dashboard') }}">
                                    <i class="bi bi-speedometer2 me-2 text-primary"></i> My Ads & Dashboard
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('listings.create') }}">
                                    <i class="bi bi-plus-circle me-2 text-success"></i> Post New Listing
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger py-2">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endguest

                <!-- Sell Button -->
                <a href="{{ route('listings.create') }}" class="lm-sell-button" id="headerSellBtn">
                    <i class="bi bi-plus-lg fs-6"></i>
                    <span>SELL</span>
                </a>
            </div>
        </div>

        <!-- Mobile Search Bar -->
        <div class="mt-2 d-lg-none">
            <form action="{{ route('browse') }}" method="GET" class="lm-search-bar" role="search">
                @if($currentCity)
                    <input type="hidden" name="city" value="{{ $currentCity }}">
                @endif
                <input type="text" name="q" class="form-control" placeholder="Search products, services, locations..." value="{{ request('q') }}">
                <button type="submit" class="lm-search-btn">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Secondary Category Bar -->
    <nav class="lm-category-strip" aria-label="Category Navigation">
        <div class="container d-flex align-items-center gap-1">
            <div class="dropdown me-2">
                <button class="btn btn-sm fw-bold text-dark dropdown-toggle px-2 py-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-grid-fill me-1"></i> ALL CATEGORIES
                </button>
                <ul class="dropdown-menu shadow border-0" style="max-height: 400px; overflow-y: auto;">
                    @foreach($categoriesList as $cat)
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center" href="{{ route('category.show', $cat) }}">
                                <i class="bi {{ $cat->icon ?? 'bi-tag' }} me-2 text-secondary"></i>
                                {{ $cat->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="d-flex align-items-center gap-1 overflow-auto">
                <a href="{{ route('browse') }}" class="lm-cat-link {{ request()->routeIs('browse') && !request('category') ? 'active' : '' }}">
                    All Items
                </a>
                @foreach($categoriesList->take(7) as $cat)
                    <a href="{{ route('category.show', $cat) }}" 
                       class="lm-cat-link {{ (isset($selectedCategory) && $selectedCategory->id === $cat->id) || request('category') === $cat->slug ? 'active' : '' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </nav>
</header>
