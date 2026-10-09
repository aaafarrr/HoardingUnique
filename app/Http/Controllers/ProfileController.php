<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show user profile.
     */
    public function show(User $user = null)
    {
        // Default to authenticated user if no user is specified
        if (!$user) {
            if (!Auth::check()) {
                return redirect()->route('login');
            }
            $user = Auth::user();
        }

        // Eager load items and reposts
        $user->load([
            'uniqueItems' => function ($q) {
                $q->latest()->withCount(['likes', 'comments', 'reposts']);
            },
            'reposts.uniqueItem.user'
        ]);

        $user->loadCount(['uniqueItems', 'comments', 'likes', 'reposts']);

        // Calculate total likes received on all their uploaded items
        $likesReceived = $user->uniqueItems->sum('likes_count');

        // Determine highest tier earned
        $highestTier = $this->determineHighestTier($user->uniqueItems->pluck('tier')->toArray());

        return view('profile.index', compact('user', 'likesReceived', 'highestTier'));
    }

    /**
     * Update user profile.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $validated = $request->validated();

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->name = $validated['name'];
        $user->bio = $validated['bio'] ?? null;
        $user->save();

        return back()->with('success', 'Profil Anda berhasil diperbarui!');
    }

    /**
     * Helper to determine highest tier from array of tiers.
     */
    protected function determineHighestTier(array $tiers): string
    {
        if (empty($tiers)) {
            return '-';
        }

        // Tiers order from highest to lowest
        $order = [
            'SSS+' => 14, 'SSS' => 13, 'SS+' => 12, 'SS' => 11,
            'S+' => 10, 'S' => 9, 'A+' => 8, 'A' => 7,
            'B+' => 6, 'B' => 5, 'C+' => 4, 'C' => 3,
            'D+' => 2, 'D' => 1
        ];

        $highestScore = 0;
        $highestTier = '-';

        foreach ($tiers as $tier) {
            $score = $order[$tier] ?? 0;
            if ($score > $highestScore) {
                $highestScore = $score;
                $highestTier = $tier;
            }
        }

        return $highestTier;
    }
}
