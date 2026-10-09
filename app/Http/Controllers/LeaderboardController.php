<?php

namespace App\Http\Controllers;

use App\Services\LeaderboardService;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function __construct(
        protected LeaderboardService $leaderboardService
    ) {}

    /**
     * Display leaderboard for all categories.
     */
    public function index(Request $request)
    {
        $category = $request->input('category', 'points');
        $limit = 15;

        switch ($category) {
            case 'uploads':
                $users = $this->leaderboardService->getTopUploads($limit);
                $metricLabel = 'Unggahan';
                $metricField = 'unique_items_count';
                break;
            case 'comments':
                $users = $this->leaderboardService->getTopComments($limit);
                $metricLabel = 'Komentar';
                $metricField = 'comments_count';
                break;
            case 'likes':
                $users = $this->leaderboardService->getTopLikes($limit);
                $metricLabel = 'Suka Diterima';
                $metricField = 'likes_received_count';
                break;
            case 'logins':
                $users = $this->leaderboardService->getTopLogins($limit);
                $metricLabel = 'Total Login';
                $metricField = 'login_count';
                break;
            case 'active':
                $users = $this->leaderboardService->getTopActive($limit);
                $metricLabel = 'Skor Aktivitas';
                $metricField = 'activity_score';
                break;
            case 'points':
            default:
                $category = 'points';
                $users = $this->leaderboardService->getTopPoints($limit);
                $metricLabel = 'Total Poin';
                $metricField = 'points';
                break;
        }

        return view('leaderboard.index', compact('users', 'category', 'metricLabel', 'metricField'));
    }
}
