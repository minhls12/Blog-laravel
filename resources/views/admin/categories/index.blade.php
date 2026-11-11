@extends('layouts.app')
@section('title', 'Quản trị - Danh mục')
@section('content')
@include('admin._nav')
<form method="POST" action="{{ route('admin.categories.store') }}" class="row g-2 mb-3">
    @csrf
    <div class="col-md-6"><input type="text" name="name" class="form-control" placeholder="Tên danh mục mới" value="{{ old('name') }}" required></div>
    <div class="col-auto"><button class="btn btn-primary">Thêm danh mục</button></div>
</form>
<div class="card table-responsive">
<table class="table mb-0 align-middle">
    <thead><tr><th>Tên</th><th>Slug</th><th>Số bài</th><th></th></tr></thead>
    <tbody>
    @forelse($categories as $category)
        <tr>
            <td>{{ $category->name }}</td><td><code>{{ $category->slug }}</code></td><td>{{ $category->posts_count }}</td>
            <td class="text-end text-nowrap">
                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline" onsubmit="return confirm('Xóa danh mục này?')">
                    @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Xóa</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="4" class="text-center text-muted py-3">Chưa có danh mục.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
@endsection
