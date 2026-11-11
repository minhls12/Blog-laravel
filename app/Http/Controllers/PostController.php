<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::published()
            ->with(['user', 'category', 'tags'])
            ->search($request->query('q'))
            ->when($request->query('category'), fn ($q, $slug) => $q->whereHas('category', fn ($c) => $c->where('slug', $slug)))
            ->when($request->query('tag'), fn ($q, $slug) => $q->whereHas('tags', fn ($t) => $t->where('slug', $slug)))
            ->orderByDesc('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('posts.index', [
            'posts' => $posts,
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    public function show(Request $request, Post $post)
    {
        // Bài chưa đăng chỉ tác giả được xem trước; người khác nhận 404.
        if (! $post->isPublished() || $post->published_at?->isFuture()) {
            abort_unless($request->user()?->id === $post->user_id, 404);
        }

        $post->load(['user', 'category', 'tags', 'comments.user']);

        return view('posts.show', compact('post'));
    }
}
