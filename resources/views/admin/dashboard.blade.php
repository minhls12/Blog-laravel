@extends('layouts.app')
@section('title', 'Quản trị - Thống kê')
@section('content')
@include('admin._nav')
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card card-body text-center"><div class="text-muted">Thành viên</div><div class="display-6">{{ $userCount }}</div></div></div>
    <div class="col-md-4"><div class="card card-body text-center"><div class="text-muted">Bài đã đăng</div><div class="display-6">{{ $postCount }}</div></div></div>
    <div class="col-md-4"><div class="card card-body text-center"><div class="text-muted">Bình luận</div><div class="display-6">{{ $commentCount }}</div></div></div>
</div>
<div class="card">
    <div class="card-header">5 bài nhiều bình luận nhất</div>
    <ul class="list-group list-group-flush">
        @forelse($topPosts as $post)
            <li class="list-group-item d-flex justify-content-between">
                <a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a>
                <span class="badge text-bg-primary">{{ $post->comments_count }} bình luận</span>
            </li>
        @empty
            <li class="list-group-item text-muted">Chưa có dữ liệu.</li>
        @endforelse
    </ul>
</div>
@endsection
