@extends('layouts.app')
@section('title', 'Trang chủ')
@section('content')
<div class="row">
    <div class="col-lg-9">
        @if(request()->hasAny(['q','category','tag']))
            <p class="text-muted">
                Kết quả lọc:
                @if(request('q')) từ khóa <strong>{{ request('q') }}</strong> @endif
                @if(request('category')) danh mục <strong>{{ request('category') }}</strong> @endif
                @if(request('tag')) tag <strong>#{{ request('tag') }}</strong> @endif
                · <a href="{{ route('home') }}">Xóa bộ lọc</a>
            </p>
        @endif

        <div class="row g-3">
            @forelse($posts as $post)
                <div class="col-md-6 col-xl-4">@include('partials.post-card', ['post' => $post])</div>
            @empty
                <div class="col-12"><div class="alert alert-info">Chưa có bài viết nào phù hợp.</div></div>
            @endforelse
        </div>

        <div class="mt-4">{{ $posts->links() }}</div>
    </div>

    <aside class="col-lg-3 mt-4 mt-lg-0">
        <div class="card mb-3"><div class="card-header">Danh mục</div>
            <div class="list-group list-group-flush">
                @foreach($categories as $category)
                    <a class="list-group-item list-group-item-action {{ request('category') === $category->slug ? 'active' : '' }}"
                       href="{{ route('home', ['category' => $category->slug]) }}">{{ $category->name }}</a>
                @endforeach
            </div>
        </div>
        <div class="card"><div class="card-header">Tag</div>
            <div class="card-body">
                @foreach($tags as $tag)
                    <a class="badge text-bg-secondary text-decoration-none" href="{{ route('home', ['tag' => $tag->slug]) }}">#{{ $tag->name }}</a>
                @endforeach
            </div>
        </div>
    </aside>
</div>
@endsection
