<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = auth()->user()->posts()->with('category')->latest()->paginate(10);

        return view('member.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('member.posts.form', $this->formData(new Post(['status' => Post::DRAFT])));
    }

    public function store(PostRequest $request): RedirectResponse
    {
        $post = $request->user()->posts()->create($this->payload($request));
        $post->tags()->sync($request->input('tags', []));

        Log::info('Tạo bài viết', ['post_id' => $post->id, 'user_id' => $request->user()->id, 'status' => $post->status]);

        return redirect()->route('member.posts.index')->with('success', 'Đã tạo bài viết.');
    }

    public function edit(Post $post)
    {
        Gate::authorize('update', $post);

        return view('member.posts.form', $this->formData($post));
    }

    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        Gate::authorize('update', $post);

        $post->update($this->payload($request, $post));
        $post->tags()->sync($request->input('tags', []));

        Log::info('Cập nhật bài viết', ['post_id' => $post->id, 'user_id' => $request->user()->id]);

        return redirect()->route('member.posts.index')->with('success', 'Đã cập nhật bài viết.');
    }

    // Xóa mềm: đưa vào thùng rác.
    public function destroy(Post $post): RedirectResponse
    {
        Gate::authorize('delete', $post);

        $post->delete();
        Log::info('Chuyển bài viết vào thùng rác', ['post_id' => $post->id, 'user_id' => auth()->id()]);

        return redirect()->route('member.posts.index')->with('success', 'Đã chuyển bài viết vào thùng rác.');
    }

    public function trash()
    {
        $posts = auth()->user()->posts()->onlyTrashed()->latest('deleted_at')->paginate(10);

        return view('member.posts.trash', compact('posts'));
    }

    public function restore(Post $post): RedirectResponse
    {
        Gate::authorize('restore', $post);

        $post->restore();
        Log::info('Khôi phục bài viết', ['post_id' => $post->id, 'user_id' => auth()->id()]);

        return redirect()->route('member.posts.trash')->with('success', 'Đã khôi phục bài viết.');
    }

    public function forceDelete(Post $post): RedirectResponse
    {
        Gate::authorize('forceDelete', $post);

        $post->forceDelete();
        Log::warning('Xóa vĩnh viễn bài viết', ['post_id' => $post->id, 'user_id' => auth()->id()]);

        return redirect()->route('member.posts.trash')->with('success', 'Đã xóa vĩnh viễn bài viết.');
    }

    private function formData(Post $post): array
    {
        return [
            'post' => $post->loadMissing('tags'),
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ];
    }

    private function payload(PostRequest $request, ?Post $post = null): array
    {
        $data = $request->safe()->only(['title', 'body', 'category_id', 'status', 'published_at']);

        if ($data['status'] === Post::PUBLISHED) {
            // Giữ nguyên ngày đăng cũ nếu bài đã đăng từ trước.
            $data['published_at'] = ($post && $post->isPublished() && $post->published_at) ? $post->published_at : now();
        } elseif ($data['status'] === Post::DRAFT) {
            $data['published_at'] = null;
        }

        if ($request->hasFile('image')) {
            if ($post?->image) {
                Storage::disk('public')->delete($post->image);
            }
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        return $data;
    }
}
