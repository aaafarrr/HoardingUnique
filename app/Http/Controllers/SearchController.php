<?php

namespace App\Http\Controllers;

use App\Models\UniqueItem;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Display global search results for items and users.
     */
    public function index(Request $request)
    {
        $query = $request->input('q');
        
        $items = collect();
        $users = collect();

        if ($query) {
            // Search items (optimized)
            $items = UniqueItem::with(['user'])
                ->withCount(['likes', 'comments'])
                ->where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('location_name', 'like', "%{$query}%")
                ->latest()
                ->limit(20)
                ->get();

            // Search users (optimized selection)
            $users = User::select('id', 'uuid', 'name', 'avatar', 'bio', 'points')
                ->where('name', 'like', "%{$query}%")
                ->orWhere('bio', 'like', "%{$query}%")
                ->orderBy('points', 'desc')
                ->limit(20)
                ->get();
        }

        return view('search.index', compact('items', 'users', 'query'));
    }
}
