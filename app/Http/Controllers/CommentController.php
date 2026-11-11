<?php

namespace App\Http\Controllers;

use App\Mail\NewCommentMail;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CommentController extends Controller
{
    public function store(Request $request, Post $post): RedirectResponse
    {
        abort_unless($post->isPublished(), 404);

        $data = $request->validate(
            ['body' => ['required', 'string', 'max:2000']],
            ['body.required' => 'Vui lòng nhập nội dung bình luận.', 'body.max' => 'Bình luận tối đa 2000 ký tự.']
        );

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        // Gửi email cho tác giả qua Queue (chạy: php artisan queue:work).
        Mail::to($post->user)->queue(new NewCommentMail($comment));

        Log::info('Bình luận mới', ['comment_id' => $comment->id, 'post_id' => $post->id, 'user_id' => $request->user()->id]);

        return redirect(route('posts.show', $post->slug).'#comments')->with('success', 'Đã gửi bình luận.');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        Gate::authorize('delete', $comment);

        $comment->delete();
        Log::info('Xóa bình luận', ['comment_id' => $comment->id, 'by_user_id' => auth()->id()]);

        return back()->with('success', 'Đã xóa bình luận.');
    }
}
