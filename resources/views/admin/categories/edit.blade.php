@extends('layouts.app')
@section('title', 'Sửa danh mục')
@section('content')
@include('admin._nav')
<form method="POST" action="{{ route('admin.categories.update', $category) }}" class="card card-body col-md-6">
    @csrf @method('PUT')
    <label class="form-label">Tên danh mục</label>
    <input type="text" name="name" class="form-control mb-3" value="{{ old('name', $category->name) }}" required>
    <div><button class="btn btn-primary">Lưu</button> <a href="{{ route('admin.categories.index') }}" class="btn btn-link">Hủy</a></div>
</form>
@endsection
