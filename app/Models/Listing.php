<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'subcategory_id',
        'listing_type',
        'title',
        'slug',
        'description',
        'price',
        'country',
        'state',
        'city',
        'area',
        'contact_phone',
        'status',
        'is_featured',
        'views_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
    ];

    /**
     * User who published the listing.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Category of the listing.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Subcategory of the listing.
     */
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    /**
     * Images attached to the listing.
     */
    public function images()
    {
        return $this->hasMany(ListingImage::class)->orderByDesc('is_primary')->orderBy('sort_order');
    }

    /**
     * Primary image relationship.
     */
    public function primaryImage()
    {
        return $this->hasOne(ListingImage::class)->where('is_primary', true);
    }

    /**
     * Helper to get primary image URL or fallback.
     */
    public function getImageUrlAttribute(): string
    {
        $primary = $this->images->firstWhere('is_primary', true) ?? $this->images->first();

        if ($primary && $primary->image_path) {
            // Check if full URL or stored asset
            if (Str::startsWith($primary->image_path, ['http://', 'https://'])) {
                return $primary->image_path;
            }
            return asset('storage/' . $primary->image_path);
        }

        return asset('images/placeholder.svg');
    }

    /**
     * Formatted price with Indian Rupee symbol and commas.
     */
    public function getFormattedPriceAttribute(): string
    {
        if ($this->price <= 0) {
            return 'Free / Negotiable';
        }

        // Format according to Indian number format or standard decimal
        $formatted = number_format($this->price, 0);
        return '₹ ' . $formatted;
    }

    /**
     * Full location string (Area, City, State).
     */
    public function getFullLocationAttribute(): string
    {
        $parts = array_filter([$this->area, $this->city, $this->state]);
        return implode(', ', $parts) ?: ($this->city ?? 'India');
    }

    /**
     * Scope for active listings.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for search and filters.
     */
    public function scopeFilter($query, array $filters)
    {
        // Keyword search (name or details)
        if (!empty($filters['q'])) {
            $keyword = trim($filters['q']);
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%")
                  ->orWhere('area', 'like', "%{$keyword}%");
            });
        }

        // Category filter (id or slug)
        if (!empty($filters['category'])) {
            $cat = $filters['category'];
            $query->whereHas('category', function ($q) use ($cat) {
                if (is_numeric($cat)) {
                    $q->where('id', $cat);
                } else {
                    $q->where('slug', $cat);
                }
            });
        }

        // Subcategory filter (id or slug)
        if (!empty($filters['subcategory'])) {
            $sub = $filters['subcategory'];
            $query->whereHas('subcategory', function ($q) use ($sub) {
                if (is_numeric($sub)) {
                    $q->where('id', $sub);
                } else {
                    $q->where('slug', $sub);
                }
            });
        }

        // Listing type filter (product or service)
        if (!empty($filters['type']) && in_array(strtolower($filters['type']), ['product', 'service'])) {
            $query->where('listing_type', strtolower($filters['type']));
        }

        // City filter
        if (!empty($filters['city'])) {
            $city = trim($filters['city']);
            $query->whereRaw('LOWER(city) = ?', [strtolower($city)]);
        }

        // State filter
        if (!empty($filters['state'])) {
            $state = trim($filters['state']);
            $query->whereRaw('LOWER(state) = ?', [strtolower($state)]);
        }

        // Price range
        if (isset($filters['min_price']) && is_numeric($filters['min_price']) && $filters['min_price'] >= 0) {
            $query->where('price', '>=', (float) $filters['min_price']);
        }
        if (isset($filters['max_price']) && is_numeric($filters['max_price']) && $filters['max_price'] > 0) {
            $query->where('price', '<=', (float) $filters['max_price']);
        }

        // Sorting
        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
                break;
        }

        return $query;
    }
}
