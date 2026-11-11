@extends('layouts.app')
@section('title', 'Quản trị - Bài viết')
@section('content')
@include('admin._nav')
<div class="card table-responsive">
<table class="table mb-0 align-middle">
    <thead><tr><th>Tiêu đề</th><th>Tác giả</th><th>Danh mục</th><th>Trạng thái</th><th></th></tr></thead>
    <tbody>
    @foreach($posts as $post)
        <tr>
            <td>
                @if($post->trashed()) {{ $post->title }} @else <a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a> @endif
            </td>
            <td>{{ $post->user->name }}</td>
            <td>{{ $post->category?->name ?? '—' }}</td>
            <td>
                {{ $post->statusLabel() }}
                @if($post->trashed()) <span class="badge text-bg-danger">Trong thùng rác</span> @endif
            </td>
            <td class="text-end">
                <form method="POST" action="{{ route('admin.posts.destroy', $post->id) }}" onsubmit="return confirm('Xóa vĩnh viễn bài viết này?')">
                    @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Xóa</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
<div class="mt-3">{{ $posts->links() }}</div>
@endsection
