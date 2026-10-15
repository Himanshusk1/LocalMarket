<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display user's account and listings dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->get('status', 'all');

        $query = Listing::with(['category', 'images'])
            ->where('user_id', $user->id);

        if ($status === 'active') {
            $query->where('status', 'active');
        } elseif ($status === 'sold') {
            $query->where('status', 'sold');
        } elseif ($status === 'inactive') {
            $query->where('status', 'inactive');
        }

        $listings = $query->latest()->paginate(10)->withQueryString();

        // Metrics
        $totalCount = Listing::where('user_id', $user->id)->count();
        $activeCount = Listing::where('user_id', $user->id)->where('status', 'active')->count();
        $soldCount = Listing::where('user_id', $user->id)->where('status', 'sold')->count();
        $viewsTotal = Listing::where('user_id', $user->id)->sum('views_count');

        return view('dashboard.index', compact(
            'user',
            'listings',
            'status',
            'totalCount',
            'activeCount',
            'soldCount',
            'viewsTotal'
        ));
    }

    /**
     * Toggle listing status between active and sold.
     */
    public function toggleStatus(Listing $listing)
    {
        if ($listing->user_id !== Auth::id()) {
            abort(403);
        }

        $newStatus = $listing->status === 'active' ? 'sold' : 'active';
        $listing->update(['status' => $newStatus]);

        $statusLabel = $newStatus === 'sold' ? 'marked as Sold' : 're-activated';

        return back()->with('success', "Listing '{$listing->title}' has been {$statusLabel}.");
    }
}
