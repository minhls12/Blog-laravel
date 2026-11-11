<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;

class CommentController extends Controller
{
    // Việc xóa dùng chung route comments.destroy (CommentPolicy cho phép admin).
    public function index()
    {
        $comments = Comment::with(['user', 'post'])->latest()->paginate(20);

        return view('admin.comments.index', compact('comments'));
    }
}
