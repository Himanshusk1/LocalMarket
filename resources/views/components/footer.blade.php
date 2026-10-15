<footer class="lm-footer">
    <div class="container py-5">
        <div class="row g-4">
            
            <!-- Popular Locations -->
            <div class="col-6 col-md-3">
                <h6 class="fw-bold text-uppercase text-dark mb-3 small" style="letter-spacing: 0.5px;">Popular Locations</h6>
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2"><a href="{{ route('city.show', 'gwalior') }}" class="text-decoration-none text-muted">Classifieds in Gwalior</a></li>
                    <li class="mb-2"><a href="{{ route('city.show', 'delhi') }}" class="text-decoration-none text-muted">Classifieds in Delhi</a></li>
                    <li class="mb-2"><a href="{{ route('city.show', 'indore') }}" class="text-decoration-none text-muted">Classifieds in Indore</a></li>
                    <li class="mb-2"><a href="{{ route('city.show', 'bhopal') }}" class="text-decoration-none text-muted">Classifieds in Bhopal</a></li>
                    <li class="mb-2"><a href="{{ route('city.show', 'mumbai') }}" class="text-decoration-none text-muted">Classifieds in Mumbai</a></li>
                    <li class="mb-2"><a href="{{ route('city.show', 'bengaluru') }}" class="text-decoration-none text-muted">Classifieds in Bengaluru</a></li>
                </ul>
            </div>

            <!-- Trending Categories -->
            <div class="col-6 col-md-3">
                <h6 class="fw-bold text-uppercase text-dark mb-3 small" style="letter-spacing: 0.5px;">Trending Categories</h6>
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2"><a href="{{ route('category.show', 'electronics-appliances') }}" class="text-decoration-none text-muted">Electronics & Appliances</a></li>
                    <li class="mb-2"><a href="{{ route('category.show', 'mobiles-tablets') }}" class="text-decoration-none text-muted">Mobiles & Tablets</a></li>
                    <li class="mb-2"><a href="{{ route('category.show', 'vehicles') }}" class="text-decoration-none text-muted">Cars & Bikes</a></li>
                    <li class="mb-2"><a href="{{ route('category.show', 'real-estate') }}" class="text-decoration-none text-muted">Houses & Flats for Sale</a></li>
                    <li class="mb-2"><a href="{{ route('category.show', 'education-tutoring') }}" class="text-decoration-none text-muted">Home Tutors & Classes</a></li>
                    <li class="mb-2"><a href="{{ route('category.show', 'other-services') }}" class="text-decoration-none text-muted">Repair & Home Services</a></li>
                </ul>
            </div>

            <!-- About Platform -->
            <div class="col-6 col-md-3">
                <h6 class="fw-bold text-uppercase text-dark mb-3 small" style="letter-spacing: 0.5px;">About LocalMarket</h6>
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2"><a href="{{ route('browse') }}" class="text-decoration-none text-muted">All Listings</a></li>
                    <li class="mb-2"><a href="{{ route('browse', ['type' => 'product']) }}" class="text-decoration-none text-muted">Buy Products</a></li>
                    <li class="mb-2"><a href="{{ route('browse', ['type' => 'service']) }}" class="text-decoration-none text-muted">Hire Services</a></li>
                    <li class="mb-2"><a href="{{ route('listings.create') }}" class="text-decoration-none text-muted">Post a Free Ad</a></li>
                    <li class="mb-2"><span class="text-muted">Honeybee Digital Internship Project</span></li>
                </ul>
            </div>

            <!-- Safety & Help -->
            <div class="col-6 col-md-3">
                <h6 class="fw-bold text-uppercase text-dark mb-3 small" style="letter-spacing: 0.5px;">Buyer Safety & Support</h6>
                <div class="bg-white p-3 rounded border border-light-subtle shadow-sm small text-muted">
                    <div class="d-flex align-items-center gap-2 mb-2 text-dark fw-bold">
                        <i class="bi bi-shield-check text-success fs-5"></i>
                        <span>Safe Trading Tips</span>
                    </div>
                    <ul class="ps-3 mb-0" style="font-size: 0.775rem;">
                        <li class="mb-1">Meet sellers in well-lit public places.</li>
                        <li class="mb-1">Inspect items thoroughly before paying.</li>
                        <li>Never send advance deposits to strangers.</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

    <!-- Bottom Copyright Bar -->
    <div class="lm-footer-bottom">
        <div class="container d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2">
            <div>
                <strong>LocalMarket</strong> — Free Classifieds in India. Built with Laravel 12 & MySQL.
            </div>
            <div class="small text-white-50">
                &copy; {{ date('Y') }} LocalMarket. Developed for Honeybee Digital Assessment.
            </div>
        </div>
    </div>
</footer>
