<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'userCount' => User::count(),
            'postCount' => Post::published()->count(),
            'commentCount' => Comment::count(),
            'topPosts' => Post::published()->withCount('comments')->orderByDesc('comments_count')->limit(5)->get(),
        ]);
    }
}
