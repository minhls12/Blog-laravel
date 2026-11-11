<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::withTrashed()->with(['user', 'category'])->latest()->paginate(15);

        return view('admin.posts.index', compact('posts'));
    }

    // Xóa bài vi phạm: xóa vĩnh viễn để tác giả không khôi phục lại được.
    public function destroy(Post $post): RedirectResponse
    {
        $post->forceDelete();
        Log::warning('Admin xóa bài vi phạm', ['post_id' => $post->id, 'admin_id' => auth()->id()]);

        return redirect()->route('admin.posts.index')->with('success', 'Đã xóa bài viết.');
    }
}
