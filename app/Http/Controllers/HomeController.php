<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Display the marketplace homepage.
     */
    public function index(Request $request)
    {
        // 1. All categories with count of active listings
        $categories = Category::withCount(['listings' => function ($q) {
            $q->where('status', 'active');
        }])->orderBy('sort_order')->get();

        // 2. Featured listings
        $featuredListings = Listing::with(['category', 'images'])
            ->where('status', 'active')
            ->where('is_featured', true)
            ->latest()
            ->take(8)
            ->get();

        // 3. Recently published listings (Fresh Recommendations)
        $recentListings = Listing::with(['category', 'images'])
            ->where('status', 'active')
            ->latest()
            ->take(12)
            ->get();

        // 4. Popular cities with active listing counts
        $popularCities = Listing::select('city', DB::raw('count(*) as count'))
            ->where('status', 'active')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->groupBy('city')
            ->orderByDesc('count')
            ->take(8)
            ->get();

        // Top services
        $serviceListings = Listing::with(['category', 'images'])
            ->where('status', 'active')
            ->where('listing_type', 'service')
            ->latest()
            ->take(4)
            ->get();

        return view('home', compact(
            'categories',
            'featuredListings',
            'recentListings',
            'popularCities',
            'serviceListings'
        ));
    }
}
