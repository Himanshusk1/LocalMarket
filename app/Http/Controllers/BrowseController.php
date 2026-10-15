<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrowseController extends Controller
{
    /**
     * General browse & search with multi-faceted filters.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'q',
            'category',
            'subcategory',
            'type',
            'city',
            'state',
            'min_price',
            'max_price',
            'sort',
        ]);

        $query = Listing::with(['category', 'subcategory', 'images'])
            ->where('status', 'active')
            ->filter($filters);

        $listings = $query->paginate(12)->withQueryString();

        // Data for filter sidebar
        $categories = Category::withCount(['listings' => function ($q) {
            $q->where('status', 'active');
        }])->orderBy('sort_order')->get();

        $selectedCategory = null;
        $subcategories = collect();
        if (!empty($filters['category'])) {
            $selectedCategory = Category::where('slug', $filters['category'])
                ->orWhere('id', $filters['category'])
                ->first();

            if ($selectedCategory) {
                $subcategories = Subcategory::where('category_id', $selectedCategory->id)
                    ->withCount(['listings' => function ($q) {
                        $q->where('status', 'active');
                    }])
                    ->get();
            }
        }

        $cities = Listing::select('city', DB::raw('count(*) as count'))
            ->where('status', 'active')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->groupBy('city')
            ->orderBy('city')
            ->get();

        $pageTitle = 'Browse Classified Listings';
        if (!empty($filters['q'])) {
            $pageTitle = 'Search results for "' . e($filters['q']) . '"';
        }

        return view('browse.index', compact(
            'listings',
            'categories',
            'subcategories',
            'selectedCategory',
            'cities',
            'filters',
            'pageTitle'
        ));
    }

    /**
     * Category-wise listing page (/category/{category:slug}).
     */
    public function category(Category $category, Request $request)
    {
        $filters = $request->only([
            'q',
            'subcategory',
            'type',
            'city',
            'min_price',
            'max_price',
            'sort',
        ]);
        $filters['category'] = $category->slug;

        $query = Listing::with(['category', 'subcategory', 'images'])
            ->where('status', 'active')
            ->where('category_id', $category->id)
            ->filter($filters);

        $listings = $query->paginate(12)->withQueryString();

        $subcategories = Subcategory::where('category_id', $category->id)
            ->withCount(['listings' => function ($q) {
                $q->where('status', 'active');
            }])
            ->get();

        $categories = Category::withCount(['listings' => function ($q) {
            $q->where('status', 'active');
        }])->orderBy('sort_order')->get();

        $cities = Listing::select('city', DB::raw('count(*) as count'))
            ->where('status', 'active')
            ->where('category_id', $category->id)
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->groupBy('city')
            ->orderBy('city')
            ->get();

        $pageTitle = $category->name . ' - Classifieds';
        $selectedCategory = $category;

        return view('browse.index', compact(
            'listings',
            'categories',
            'subcategories',
            'selectedCategory',
            'cities',
            'filters',
            'pageTitle'
        ));
    }

    /**
     * City-wise listing page (/city/{city}).
     */
    public function city(string $city, Request $request)
    {
        $normalizedCity = trim(urldecode($city));

        $filters = $request->only([
            'q',
            'category',
            'subcategory',
            'type',
            'min_price',
            'max_price',
            'sort',
        ]);
        $filters['city'] = $normalizedCity;

        // Query by city applied in database
        $query = Listing::with(['category', 'subcategory', 'images'])
            ->where('status', 'active')
            ->whereRaw('LOWER(city) = ?', [strtolower($normalizedCity)])
            ->filter($filters);

        $listings = $query->paginate(12)->withQueryString();

        $categories = Category::withCount(['listings' => function ($q) use ($normalizedCity) {
            $q->where('status', 'active')
              ->whereRaw('LOWER(city) = ?', [strtolower($normalizedCity)]);
        }])->orderBy('sort_order')->get();

        $subcategories = collect();
        $selectedCategory = null;
        if (!empty($filters['category'])) {
            $selectedCategory = Category::where('slug', $filters['category'])
                ->orWhere('id', $filters['category'])
                ->first();
            if ($selectedCategory) {
                $subcategories = Subcategory::where('category_id', $selectedCategory->id)->get();
            }
        }

        $cities = Listing::select('city', DB::raw('count(*) as count'))
            ->where('status', 'active')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->groupBy('city')
            ->orderBy('city')
            ->get();

        $pageTitle = 'Classifieds in ' . ucfirst($normalizedCity);

        return view('browse.index', compact(
            'listings',
            'categories',
            'subcategories',
            'selectedCategory',
            'cities',
            'filters',
            'pageTitle',
            'normalizedCity'
        ));
    }

    /**
     * City + Category listing page (/city/{city}/{category:slug}).
     * Both conditions applied strictly in the database query.
     */
    public function cityCategory(string $city, Category $category, Request $request)
    {
        $normalizedCity = trim(urldecode($city));

        $filters = $request->only([
            'q',
            'subcategory',
            'type',
            'min_price',
            'max_price',
            'sort',
        ]);
        $filters['city'] = $normalizedCity;
        $filters['category'] = $category->slug;

        // Both conditions applied in database query
        $query = Listing::with(['category', 'subcategory', 'images'])
            ->where('status', 'active')
            ->whereRaw('LOWER(city) = ?', [strtolower($normalizedCity)])
            ->where('category_id', $category->id)
            ->filter($filters);

        $listings = $query->paginate(12)->withQueryString();

        $subcategories = Subcategory::where('category_id', $category->id)
            ->withCount(['listings' => function ($q) use ($normalizedCity) {
                $q->where('status', 'active')
                  ->whereRaw('LOWER(city) = ?', [strtolower($normalizedCity)]);
            }])
            ->get();

        $categories = Category::withCount(['listings' => function ($q) use ($normalizedCity) {
            $q->where('status', 'active')
              ->whereRaw('LOWER(city) = ?', [strtolower($normalizedCity)]);
        }])->orderBy('sort_order')->get();

        $cities = Listing::select('city', DB::raw('count(*) as count'))
            ->where('status', 'active')
            ->where('category_id', $category->id)
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->groupBy('city')
            ->orderBy('city')
            ->get();

        $pageTitle = $category->name . ' in ' . ucfirst($normalizedCity);
        $selectedCategory = $category;

        return view('browse.index', compact(
            'listings',
            'categories',
            'subcategories',
            'selectedCategory',
            'cities',
            'filters',
            'pageTitle',
            'normalizedCity'
        ));
    }
}
