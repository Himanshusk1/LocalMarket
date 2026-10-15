<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\Subcategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ListingController extends Controller
{
    use AuthorizesRequests;

    /**
     * Show the form to create a new listing.
     */
    public function create()
    {
        $categories = Category::with('subcategories')->orderBy('sort_order')->get();
        return view('listings.create', compact('categories'));
    }

    /**
     * Store a newly created listing in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'listing_type' => ['required', 'in:product,service'],
            'title' => ['required', 'string', 'min:5', 'max:150'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'category_id' => ['required', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:subcategories,id'],
            'country' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:150'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'images' => ['nullable', 'array', 'max:8'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ], [
            'listing_type.required' => 'Please select whether this is a Product or a Service.',
            'title.required' => 'Ad title is required.',
            'description.min' => 'Please provide at least 20 characters describing your item or service.',
            'category_id.required' => 'Please select a category.',
            'city.required' => 'Please enter your city.',
            'price.required' => 'Please specify a valid price (or 0 for negotiable/free).',
            'images.*.image' => 'Each uploaded file must be a valid image (JPEG, PNG, JPG, or WEBP).',
            'images.*.max' => 'Each image must not exceed 4MB.',
        ]);

        // Verify subcategory belongs to category if provided
        if (!empty($validated['subcategory_id'])) {
            $validSub = Subcategory::where('id', $validated['subcategory_id'])
                ->where('category_id', $validated['category_id'])
                ->exists();
            if (!$validSub) {
                $validated['subcategory_id'] = null;
            }
        }

        $user = Auth::user();

        $listing = DB::transaction(function () use ($validated, $user, $request) {
            $baseSlug = Str::slug($validated['title']);
            $uniqueSlug = $baseSlug . '-' . Str::lower(Str::random(6));

            $listing = Listing::create([
                'user_id' => $user->id,
                'category_id' => $validated['category_id'],
                'subcategory_id' => $validated['subcategory_id'] ?? null,
                'listing_type' => $validated['listing_type'],
                'title' => $validated['title'],
                'slug' => $uniqueSlug,
                'description' => $validated['description'],
                'price' => $validated['price'],
                'country' => $validated['country'],
                'state' => $validated['state'],
                'city' => $validated['city'],
                'area' => $validated['area'] ?? null,
                'contact_phone' => $validated['contact_phone'] ?? $user->phone,
                'status' => 'active',
                'is_featured' => false,
                'views_count' => 0,
            ]);

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $file) {
                    $path = $file->store('listings', 'public');
                    ListingImage::create([
                        'listing_id' => $listing->id,
                        'image_path' => $path,
                        'is_primary' => $index === 0,
                        'sort_order' => $index,
                    ]);
                }
            }

            return $listing;
        });

        return redirect()->route('listings.show', $listing)
            ->with('success', 'Your ad has been published successfully!');
    }

    /**
     * Display the specified listing details.
     */
    public function show(Listing $listing)
    {
        // Check active or owner
        if ($listing->status !== 'active') {
            if (!Auth::check() || Auth::id() !== $listing->user_id) {
                abort(404, 'This listing is no longer available.');
            }
        }

        // Increment view count (simple count or session checked)
        $sessionKey = 'viewed_listing_' . $listing->id;
        if (!session()->has($sessionKey)) {
            $listing->increment('views_count');
            session()->put($sessionKey, true);
        }

        $listing->load(['user', 'category', 'subcategory', 'images']);

        // Related listings in the same category or city
        $relatedListings = Listing::with(['category', 'images'])
            ->where('status', 'active')
            ->where('id', '!=', $listing->id)
            ->where(function ($q) use ($listing) {
                $q->where('category_id', $listing->category_id)
                  ->orWhere('city', $listing->city);
            })
            ->latest()
            ->take(4)
            ->get();

        return view('listings.show', compact('listing', 'relatedListings'));
    }

    /**
     * Show the form for editing an existing listing.
     */
    public function edit(Listing $listing)
    {
        $this->authorize('update', $listing);

        $categories = Category::with('subcategories')->orderBy('sort_order')->get();
        $listing->load(['category', 'subcategory', 'images']);

        return view('listings.edit', compact('listing', 'categories'));
    }

    /**
     * Update the specified listing in storage.
     */
    public function update(Request $request, Listing $listing)
    {
        $this->authorize('update', $listing);

        $validated = $request->validate([
            'listing_type' => ['required', 'in:product,service'],
            'title' => ['required', 'string', 'min:5', 'max:150'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'category_id' => ['required', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:subcategories,id'],
            'country' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:150'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'status' => ['required', 'in:active,sold,inactive'],
            'images' => ['nullable', 'array', 'max:8'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ]);

        if (!empty($validated['subcategory_id'])) {
            $validSub = Subcategory::where('id', $validated['subcategory_id'])
                ->where('category_id', $validated['category_id'])
                ->exists();
            if (!$validSub) {
                $validated['subcategory_id'] = null;
            }
        }

        DB::transaction(function () use ($validated, $listing, $request) {
            $listing->update([
                'category_id' => $validated['category_id'],
                'subcategory_id' => $validated['subcategory_id'] ?? null,
                'listing_type' => $validated['listing_type'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'country' => $validated['country'],
                'state' => $validated['state'],
                'city' => $validated['city'],
                'area' => $validated['area'] ?? null,
                'contact_phone' => $validated['contact_phone'] ?? $listing->contact_phone,
                'status' => $validated['status'],
            ]);

            // Handle new images
            if ($request->hasFile('images')) {
                $hasExistingPrimary = $listing->images()->where('is_primary', true)->exists();
                $existingCount = $listing->images()->count();

                foreach ($request->file('images') as $i => $file) {
                    $path = $file->store('listings', 'public');
                    ListingImage::create([
                        'listing_id' => $listing->id,
                        'image_path' => $path,
                        'is_primary' => !$hasExistingPrimary && $i === 0,
                        'sort_order' => $existingCount + $i,
                    ]);
                }
            }
        });

        return redirect()->route('listings.show', $listing)
            ->with('success', 'Your listing has been updated successfully.');
    }

    /**
     * Remove the specified listing from storage.
     */
    public function destroy(Listing $listing)
    {
        $this->authorize('delete', $listing);

        // Remove images from storage
        foreach ($listing->images as $img) {
            if ($img->image_path && !Str::startsWith($img->image_path, ['samples/', 'http://', 'https://'])) {
                Storage::disk('public')->delete($img->image_path);
            }
        }

        $listing->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Listing deleted successfully.');
    }

    /**
     * Delete an individual image from a listing.
     */
    public function deleteImage(Listing $listing, ListingImage $image)
    {
        $this->authorize('update', $listing);

        if ($image->listing_id !== $listing->id) {
            abort(403);
        }

        // Delete from disk if not sample
        if ($image->image_path && !Str::startsWith($image->image_path, ['samples/', 'http://', 'https://'])) {
            Storage::disk('public')->delete($image->image_path);
        }

        $wasPrimary = $image->is_primary;
        $image->delete();

        // If primary was deleted, set next available image as primary
        if ($wasPrimary) {
            $next = $listing->images()->first();
            if ($next) {
                $next->update(['is_primary' => true]);
            }
        }

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Image removed.');
    }

    /**
     * Return subcategories for category as JSON (for AJAX dynamic forms).
     */
    public function getSubcategories(Category $category)
    {
        $subcategories = $category->subcategories()->select('id', 'name', 'slug')->get();
        return response()->json($subcategories);
    }
}
