@extends('layouts.app')
@section('title', 'Quản trị - Bình luận')
@section('content')
@include('admin._nav')
<div class="card table-responsive">
<table class="table mb-0 align-middle">
    <thead><tr><th>Người viết</th><th>Nội dung</th><th>Bài viết</th><th>Thời gian</th><th></th></tr></thead>
    <tbody>
    @forelse($comments as $comment)
        <tr>
            <td>{{ $comment->user->name }}</td>
            <td>{{ \Illuminate\Support\Str::limit($comment->body, 80) }}</td>
            <td>{{ \Illuminate\Support\Str::limit($comment->post?->title, 40) }}</td>
            <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
            <td class="text-end">
                <form method="POST" action="{{ route('comments.destroy', $comment->id) }}" onsubmit="return confirm('Xóa bình luận này?')">
                    @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Xóa</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="5" class="text-center text-muted py-3">Chưa có bình luận.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div class="mt-3">{{ $comments->links() }}</div>
@endsection
