@extends('layouts.app')
@section('title', 'Thùng rác')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Thùng rác</h1>
    <a href="{{ route('member.posts.index') }}" class="btn btn-outline-secondary btn-sm">← Bài viết của tôi</a>
</div>
<div class="table-responsive card">
<table class="table mb-0 align-middle">
    <thead><tr><th>Tiêu đề</th><th>Ngày xóa</th><th></th></tr></thead>
    <tbody>
    @forelse($posts as $post)
        <tr>
            <td>{{ $post->title }}</td>
            <td>{{ $post->deleted_at->format('d/m/Y H:i') }}</td>
            <td class="text-end text-nowrap">
                <form method="POST" action="{{ route('member.posts.restore', $post->id) }}" class="d-inline">
                    @csrf @method('PATCH')
                    <button class="btn btn-sm btn-outline-success">Khôi phục</button>
                </form>
                <form method="POST" action="{{ route('member.posts.force', $post->id) }}" class="d-inline" onsubmit="return confirm('Xóa vĩnh viễn? Không thể hoàn tác!')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Xóa vĩnh viễn</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="3" class="text-center text-muted py-4">Thùng rác trống.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div class="mt-3">{{ $posts->links() }}</div>
@endsection
