<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class LeaderboardService
{
    /**
     * Get top users by points.
     */
    public function getTopPoints(int $limit = 10): Collection
    {
        return User::select('id', 'uuid', 'name', 'avatar', 'points', 'bio')
            ->orderBy('points', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top users by number of unique item uploads.
     */
    public function getTopUploads(int $limit = 10): Collection
    {
        return User::select('id', 'uuid', 'name', 'avatar', 'points', 'bio')
            ->withCount('uniqueItems')
            ->orderBy('unique_items_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top users by number of comments written.
     */
    public function getTopComments(int $limit = 10): Collection
    {
        return User::select('id', 'uuid', 'name', 'avatar', 'points', 'bio')
            ->withCount('comments')
            ->orderBy('comments_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top users by number of likes received on their items.
     */
    public function getTopLikes(int $limit = 10): Collection
    {
        return User::select('users.id', 'users.uuid', 'users.name', 'users.avatar', 'users.points', 'users.bio')
            ->leftJoin('unique_items', 'users.id', '=', 'unique_items.user_id')
            ->leftJoin('likes', 'unique_items.id', '=', 'likes.unique_item_id')
            ->selectRaw('count(likes.id) as likes_received_count')
            ->groupBy('users.id', 'users.uuid', 'users.name', 'users.avatar', 'users.points', 'users.bio')
            ->orderBy('likes_received_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top users by login count.
     */
    public function getTopLogins(int $limit = 10): Collection
    {
        return User::select('id', 'uuid', 'name', 'avatar', 'points', 'login_count', 'bio')
            ->orderBy('login_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top active users.
     * Activity Score = (uploads * 10) + likes_given + comments_written + (login_count * 2)
     */
    public function getTopActive(int $limit = 10): Collection
    {
        return User::select('users.id', 'users.uuid', 'users.name', 'users.avatar', 'users.points', 'users.login_count', 'users.bio')
            ->leftJoin('unique_items', 'users.id', '=', 'unique_items.user_id')
            ->leftJoin('comments', 'users.id', '=', 'comments.user_id')
            ->leftJoin('likes', 'users.id', '=', 'likes.user_id')
            ->selectRaw('
                (
                    (count(distinct unique_items.id) * 10) + 
                    count(distinct likes.id) + 
                    count(distinct comments.id) + 
                    (users.login_count * 2)
                ) as activity_score
            ')
            ->groupBy('users.id', 'users.uuid', 'users.name', 'users.avatar', 'users.points', 'users.login_count', 'users.bio')
            ->orderBy('activity_score', 'desc')
            ->limit($limit)
            ->get();
    }
}
