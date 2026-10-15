@extends('layouts.app')

@section('title', 'Edit Ad — ' . $listing->title . ' — LocalMarket')

@section('content')

<div class="row justify-content-center mb-5">
    <div class="col-lg-9">
        
        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Edit Listing</h1>
                <p class="text-muted small mb-0">Update listing details, manage photos, or update status</p>
            </div>
            <a href="{{ route('listings.show', $listing) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Ad
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 p-md-5">
            <form action="{{ route('listings.update', $listing) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Section 1: Classification & Status -->
                <div class="mb-4 pb-4 border-bottom">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Ad Classification <span class="text-danger">*</span></label>
                            <select name="listing_type" class="form-select @error('listing_type') is-invalid @enderror" required>
                                <option value="product" {{ old('listing_type', $listing->listing_type) === 'product' ? 'selected' : '' }}>Product (Physical Item)</option>
                                <option value="service" {{ old('listing_type', $listing->listing_type) === 'service' ? 'selected' : '' }}>Service (Skill / Offering)</option>
                            </select>
                            @error('listing_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Listing Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', $listing->status) === 'active' ? 'selected' : '' }}>Active (Visible on public search)</option>
                                <option value="sold" {{ old('status', $listing->status) === 'sold' ? 'selected' : '' }}>Sold (Marked as sold out)</option>
                                <option value="inactive" {{ old('status', $listing->status) === 'inactive' ? 'selected' : '' }}>Inactive (Hidden)</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Category & Subcategory -->
                <div class="mb-4 pb-4 border-bottom">
                    <h5 class="fw-bold text-dark mb-3">Category & Subcategory</h5>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="category_id" class="form-label small fw-bold">Category <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required onchange="updateSubcategories()">
                                <option value="">-- Choose Category --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (string)old('category_id', $listing->category_id) === (string)$cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="subcategory_id" class="form-label small fw-bold">Subcategory</label>
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
                    <h5 class="fw-bold text-dark mb-3">Ad Details</h5>

                    <div class="mb-3">
                        <label for="title" class="form-label small fw-bold">Ad Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $listing->title) }}" required maxlength="150">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label small fw-bold">Description / Details <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror" required minlength="20" maxlength="5000">{{ old('description', $listing->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="price" class="form-label small fw-bold">Price / Rate (₹) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">₹</span>
                                <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', (int)$listing->price) }}" required min="0">
                            </div>
                            @error('price')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="contact_phone" class="form-label small fw-bold">Contact Phone Number</label>
                            <input type="text" name="contact_phone" id="contact_phone" class="form-control @error('contact_phone') is-invalid @enderror" value="{{ old('contact_phone', $listing->contact_phone) }}">
                            @error('contact_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 4: Location -->
                <div class="mb-4 pb-4 border-bottom">
                    <h5 class="fw-bold text-dark mb-3">Location</h5>

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="country" class="form-label small fw-bold">Country <span class="text-danger">*</span></label>
                            <input type="text" name="country" id="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country', $listing->country) }}" required>
                        </div>

                        <div class="col-md-3">
                            <label for="state" class="form-label small fw-bold">State <span class="text-danger">*</span></label>
                            <input type="text" name="state" id="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state', $listing->state) }}" required>
                        </div>

                        <div class="col-md-3">
                            <label for="city" class="form-label small fw-bold">City <span class="text-danger">*</span></label>
                            <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $listing->city) }}" required>
                        </div>

                        <div class="col-md-3">
                            <label for="area" class="form-label small fw-bold">Locality / Area</label>
                            <input type="text" name="area" id="area" class="form-control @error('area') is-invalid @enderror" value="{{ old('area', $listing->area) }}">
                        </div>
                    </div>
                </div>

                <!-- Section 5: Current & New Photos -->
                <div class="mb-4 pb-4 border-bottom">
                    <h5 class="fw-bold text-dark mb-2">Manage Photos</h5>

                    @if($listing->images->isNotEmpty())
                        <div class="mb-3">
                            <label class="form-label small text-muted">Current Photos:</label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach($listing->images as $img)
                                    <div class="position-relative border rounded p-1 bg-light text-center" style="width: 110px;">
                                        <img src="{{ $img->url }}" alt="Ad photo" class="rounded mb-1" style="width: 100%; height: 75px; object-fit: cover;">
                                        @if($img->is_primary)
                                            <span class="badge bg-primary position-absolute top-0 start-0 m-1" style="font-size: 0.6rem;">Cover</span>
                                        @endif
                                        <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2 mt-1 w-100" style="font-size: 0.72rem;" onclick="deleteImage('{{ route('listings.images.destroy', ['listing' => $listing->id, 'image' => $img->id]) }}', this)">
                                            <i class="bi bi-trash"></i> Remove
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="newImagesInput" class="form-label small fw-bold">Add More Photos (Optional)</label>
                        <input type="file" name="images[]" id="newImagesInput" class="form-control" multiple accept="image/jpeg,image/png,image/jpg,image/webp">
                    </div>
                </div>

                <!-- Submit Action -->
                <div class="d-flex align-items-center justify-content-between pt-2">
                    <a href="{{ route('listings.show', $listing) }}" class="btn btn-link text-muted text-decoration-none">Cancel</a>
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold fs-6 shadow-sm">
                        <i class="bi bi-check-circle me-1"></i> Update Listing
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
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
        const currentSubId = "{{ old('subcategory_id', $listing->subcategory_id) }}";

        subSelect.innerHTML = '<option value="">-- Select Subcategory --</option>';

        if (selectedId && categoryData[selectedId]) {
            categoryData[selectedId].forEach(sub => {
                const opt = document.createElement('option');
                opt.value = sub.id;
                opt.textContent = sub.name;
                if (String(sub.id) === currentSubId) {
                    opt.selected = true;
                }
                subSelect.appendChild(opt);
            });
        }
    }

    function deleteImage(url, button) {
        if (!confirm('Are you sure you want to remove this image?')) return;

        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                button.closest('.position-relative').remove();
            }
        })
        .catch(err => {
            // fallback reload
            location.reload();
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateSubcategories();
    });
</script>
@endpush
