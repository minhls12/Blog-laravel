@extends('layouts.app')
@section('title', 'Bài viết của tôi')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Bài viết của tôi</h1>
    <div>
        <a href="{{ route('member.posts.trash') }}" class="btn btn-outline-secondary btn-sm">Thùng rác</a>
        <a href="{{ route('member.posts.create') }}" class="btn btn-primary btn-sm">+ Viết bài</a>
    </div>
</div>
<div class="table-responsive card">
<table class="table table-hover mb-0 align-middle">
    <thead><tr><th>Tiêu đề</th><th>Danh mục</th><th>Trạng thái</th><th>Ngày đăng</th><th></th></tr></thead>
    <tbody>
    @forelse($posts as $post)
        <tr>
            <td><a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a></td>
            <td>{{ $post->category?->name ?? '—' }}</td>
            <td><span class="badge text-bg-{{ $post->isPublished() ? 'success' : ($post->isScheduled() ? 'info' : 'secondary') }}">{{ $post->statusLabel() }}</span></td>
            <td>{{ $post->published_at?->format('d/m/Y H:i') ?? '—' }}</td>
            <td class="text-end text-nowrap">
                <a href="{{ route('member.posts.edit', $post->id) }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                <form method="POST" action="{{ route('member.posts.destroy', $post->id) }}" class="d-inline" onsubmit="return confirm('Chuyển bài vào thùng rác?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Xóa</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="5" class="text-center text-muted py-4">Bạn chưa có bài viết nào.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div class="mt-3">{{ $posts->links() }}</div>
@endsection
