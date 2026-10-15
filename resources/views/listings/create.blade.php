@extends('layouts.app')

@section('title', 'Post a Free Ad — LocalMarket India')
@section('meta_description', 'Post your product or service listing on LocalMarket. Reach thousands of local buyers quickly and easily.')

@section('content')

<div class="row justify-content-center mb-5">
    <div class="col-lg-9">
        
        <!-- Page Header -->
        <div class="text-center mb-4">
            <h1 class="h3 fw-bold text-dark mb-1">POST YOUR AD</h1>
            <p class="text-muted small">Fill in the details below to publish your product or service</p>
        </div>

        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 p-md-5">
            <form action="{{ route('listings.store') }}" method="POST" enctype="multipart/form-data" id="createListingForm">
                @csrf

                <!-- Section 1: Listing Type -->
                <div class="mb-4 pb-4 border-bottom">
                    <h5 class="fw-bold text-dark mb-2">1. Ad Classification</h5>
                    <p class="text-muted small mb-3">Choose whether you are selling a physical product or offering a service</p>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="card p-3 border rounded-3 cursor-pointer d-flex flex-row align-items-center gap-3 listing-type-card h-100 {{ old('listing_type', 'product') === 'product' ? 'border-primary bg-light' : '' }}" for="type_prod">
                                <input class="form-check-input mt-0 fs-5" type="radio" name="listing_type" id="type_prod" value="product" {{ old('listing_type', 'product') === 'product' ? 'checked' : '' }} required onchange="highlightType(this)">
                                <div>
                                    <div class="fw-bold text-dark"><i class="bi bi-box-seam me-1 text-primary"></i> Product / Physical Item</div>
                                    <div class="small text-muted">Mobiles, Cars, Electronics, Furniture, Real Estate, Fashion</div>
                                </div>
                            </label>
                        </div>
                        <div class="col-sm-6">
                            <label class="card p-3 border rounded-3 cursor-pointer d-flex flex-row align-items-center gap-3 listing-type-card h-100 {{ old('listing_type') === 'service' ? 'border-primary bg-light' : '' }}" for="type_serv">
                                <input class="form-check-input mt-0 fs-5" type="radio" name="listing_type" id="type_serv" value="service" {{ old('listing_type') === 'service' ? 'checked' : '' }} onchange="highlightType(this)">
                                <div>
                                    <div class="fw-bold text-dark"><i class="bi bi-tools me-1 text-info"></i> Service / Skill Offering</div>
                                    <div class="small text-muted">Home Tutors, Electricians, AC Repair, CA & Legal, Developers</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    @error('listing_type')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Section 2: Category & Subcategory -->
                <div class="mb-4 pb-4 border-bottom">
                    <h5 class="fw-bold text-dark mb-3">2. Category & Subcategory</h5>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="category_id" class="form-label small fw-bold">Select Category <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required onchange="updateSubcategories()">
                                <option value="">-- Choose Category --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (string)old('category_id') === (string)$cat->id ? 'selected' : '' }} data-slug="{{ $cat->slug }}">
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="subcategory_id" class="form-label small fw-bold">Subcategory <span class="text-muted">(Optional)</span></label>
                            <select name="subcategory_id" id="subcategory_id" class="form-select @error('subcategory_id') is-invalid @enderror">
                                <option value="">-- Select Subcategory --</option>
                            </select>
                            @error('subcategory_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 3: Ad Details & Pricing -->
                <div class="mb-4 pb-4 border-bottom">
                    <h5 class="fw-bold text-dark mb-3">3. Ad Details</h5>

                    <!-- Ad Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label small fw-bold">Ad Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" placeholder="Mention key features (e.g. Apple MacBook Air M2 8GB/256GB Space Grey)" value="{{ old('title') }}" required maxlength="150">
                        <div class="form-text small">Sensible, descriptive title helps buyers find your listing quickly. (5-150 characters)</div>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label small fw-bold">Description / Details <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror" placeholder="Describe condition, reason for selling, inclusions, warranty, age of item, or service scope..." required minlength="20" maxlength="5000">{{ old('description') }}</textarea>
                        <div class="form-text small">Include details such as condition, bill/box status, service charges, or working hours.</div>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="price" class="form-label small fw-bold">Price / Rate (₹) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">₹</span>
                                <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" placeholder="e.g. 25000 (enter 0 for free/negotiable)" value="{{ old('price') }}" required min="0" step="1">
                            </div>
                            <div class="form-text small">Set a realistic non-negative price in Indian Rupees.</div>
                            @error('price')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="contact_phone" class="form-label small fw-bold">Contact Phone Number <span class="text-muted">(Optional)</span></label>
                            <input type="text" name="contact_phone" id="contact_phone" class="form-control @error('contact_phone') is-invalid @enderror" placeholder="+91 98260 12345" value="{{ old('contact_phone', Auth::user()->phone) }}">
                            <div class="form-text small">Shown to interested buyers in the contact modal.</div>
                            @error('contact_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 4: Location Details -->
                <div class="mb-4 pb-4 border-bottom">
                    <h5 class="fw-bold text-dark mb-3">4. Location</h5>

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="country" class="form-label small fw-bold">Country <span class="text-danger">*</span></label>
                            <input type="text" name="country" id="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country', 'India') }}" required>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="state" class="form-label small fw-bold">State <span class="text-danger">*</span></label>
                            <input type="text" name="state" id="state" class="form-control @error('state') is-invalid @enderror" placeholder="e.g. Madhya Pradesh" value="{{ old('state', 'Madhya Pradesh') }}" required>
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="city" class="form-label small fw-bold">City <span class="text-danger">*</span></label>
                            <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" placeholder="e.g. Gwalior" value="{{ old('city', Auth::user()->city ?? 'Gwalior') }}" required>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="area" class="form-label small fw-bold">Locality / Area <span class="text-muted">(Optional)</span></label>
                            <input type="text" name="area" id="area" class="form-control @error('area') is-invalid @enderror" placeholder="e.g. City Center" value="{{ old('area') }}">
                            @error('area')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 5: Image Uploads -->
                <div class="mb-4 pb-4 border-bottom">
                    <h5 class="fw-bold text-dark mb-2">5. Upload Photos <span class="text-muted small fw-normal">(Up to 8 images)</span></h5>
                    <p class="text-muted small mb-3">Ads with clear photos get up to 10x more inquiries. Accepted formats: JPG, PNG, WEBP (Max 4MB each).</p>

                    <div class="mb-3">
                        <input type="file" name="images[]" id="imagesInput" class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror" multiple accept="image/jpeg,image/png,image/jpg,image/webp" onchange="previewImages(event)">
                        @error('images')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        @error('images.*')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Client-side Preview Container -->
                    <div id="imagePreviewContainer" class="d-flex flex-wrap gap-2 pt-2"></div>
                </div>

                <!-- Submit Action -->
                <div class="d-flex align-items-center justify-content-between pt-2">
                    <a href="{{ route('home') }}" class="btn btn-link text-muted text-decoration-none">Cancel</a>
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold fs-6 shadow-sm" id="submitListingBtn">
                        <i class="bi bi-check-circle me-1"></i> Post Ad Now
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    // Embedded category subcategories data for instant client-side update
    const categoryData = {
        @foreach($categories as $c)
            "{{ $c->id }}": [
                @foreach($c->subcategories as $s)
                    { id: {{ $s->id }}, name: "{{ addslashes($s->name) }}" },
                @endforeach
            ],
        @endforeach
    };

    function updateSubcategories() {
        const catSelect = document.getElementById('category_id');
        const subSelect = document.getElementById('subcategory_id');
        const selectedId = catSelect.value;

        // Clear current options
        subSelect.innerHTML = '<option value="">-- Select Subcategory --</option>';

        if (selectedId && categoryData[selectedId]) {
            categoryData[selectedId].forEach(sub => {
                const opt = document.createElement('option');
                opt.value = sub.id;
                opt.textContent = sub.name;
                @if(old('subcategory_id'))
                    if (String(sub.id) === "{{ old('subcategory_id') }}") {
                        opt.selected = true;
                    }
                @endif
                subSelect.appendChild(opt);
            });
        }
    }

    function highlightType(radio) {
        document.querySelectorAll('.listing-type-card').forEach(c => {
            c.classList.remove('border-primary', 'bg-light');
        });
        radio.closest('.listing-type-card').classList.add('border-primary', 'bg-light');
    }

    function previewImages(event) {
        const container = document.getElementById('imagePreviewContainer');
        container.innerHTML = '';
        const files = event.target.files;

        if (files) {
            Array.from(files).forEach((file, idx) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const wrapper = document.createElement('div');
                        wrapper.className = 'position-relative rounded border p-1 bg-light';
                        wrapper.style.width = '100px';
                        wrapper.style.height = '80px';
                        
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.width = '100%';
                        img.style.height = '100%';
                        img.style.objectFit = 'cover';
                        img.className = 'rounded';

                        if (idx === 0) {
                            const badge = document.createElement('span');
                            badge.className = 'badge bg-primary position-absolute top-0 start-0 m-1';
                            badge.style.fontSize = '0.6rem';
                            badge.innerText = 'Cover';
                            wrapper.appendChild(badge);
                        }

                        wrapper.appendChild(img);
                        container.appendChild(wrapper);
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    }

    // Initialize subcategories on page load (for preserving old input)
    document.addEventListener('DOMContentLoaded', function() {
        updateSubcategories();
    });
</script>
@endpush
