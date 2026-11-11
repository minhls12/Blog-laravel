@extends('layouts.app')
@section('title', $post->title)
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        @unless($post->isPublished())
            <div class="alert alert-warning">Bài viết ở trạng thái <strong>{{ $post->statusLabel() }}</strong> — chỉ bạn (tác giả) xem được.</div>
        @endunless

        <article class="card card-body mb-4">
            <h1 class="h2">{{ $post->title }}</h1>
            <p class="text-muted">
                Bởi <strong>{{ $post->user->name }}</strong>
                @if($post->published_at) · {{ $post->published_at->format('d/m/Y H:i') }} @endif
                @if($post->category) · <a href="{{ route('home', ['category' => $post->category->slug]) }}">{{ $post->category->name }}</a> @endif
            </p>
            @if($post->image)
                <img src="{{ asset('storage/'.$post->image) }}" class="img-fluid rounded mb-3" alt="{{ $post->title }}">
            @endif
            <div class="post-body">{!! nl2br(e($post->body)) !!}</div>
            <div class="mt-3">
                @foreach($post->tags as $tag)
                    <a href="{{ route('home', ['tag' => $tag->slug]) }}" class="badge text-bg-light border text-decoration-none">#{{ $tag->name }}</a>
                @endforeach
            </div>
        </article>

        <section id="comments">
            <h2 class="h5">Bình luận ({{ $post->comments->count() }})</h2>

            @auth
                @if($post->isPublished())
                    <form method="POST" action="{{ route('comments.store', $post->slug) }}" class="mb-4">
                        @csrf
                        <textarea name="body" rows="3" class="form-control mb-2" placeholder="Viết bình luận..." required>{{ old('body') }}</textarea>
                        <button class="btn btn-primary btn-sm">Gửi bình luận</button>
                    </form>
                @endif
            @else
                <p><a href="{{ route('login') }}">Đăng nhập</a> để bình luận.</p>
            @endauth

            @forelse($post->comments as $comment)
                <div class="card card-body mb-2">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $comment->user->name }}</strong>
                        <small class="text-muted">{{ $comment->created_at->format('d/m/Y H:i') }}</small>
                    </div>
                    <div>{!! nl2br(e($comment->body)) !!}</div>
                    @can('delete', $comment)
                        <form method="POST" action="{{ route('comments.destroy', $comment->id) }}" class="mt-2" onsubmit="return confirm('Xóa bình luận này?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Xóa</button>
                        </form>
                    @endcan
                </div>
            @empty
                <p class="text-muted">Chưa có bình luận nào.</p>
            @endforelse
        </section>
    </div>
</div>
@endsection
