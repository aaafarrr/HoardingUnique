<?php

namespace App\Http\Controllers;

use App\Models\UniqueItem;

class MapController extends Controller
{
    /**
     * Display the items map.
     */
    public function index()
    {
        $items = UniqueItem::with('user')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['id', 'title', 'image_path', 'tier', 'points', 'latitude', 'longitude', 'location_name', 'user_id']);

        return view('map.index', compact('items'));
    }
}
