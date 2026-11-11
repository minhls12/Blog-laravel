@extends('layouts.app')
@section('title', $post->exists ? 'Sửa bài viết' : 'Viết bài mới')
@section('content')
@php($selectedTags = old('tags', $post->tags->pluck('id')->all()))
<h1 class="h4 mb-3">{{ $post->exists ? 'Sửa bài viết' : 'Viết bài mới' }}</h1>
<form method="POST" enctype="multipart/form-data" class="card card-body"
      action="{{ $post->exists ? route('member.posts.update', $post->id) : route('member.posts.store') }}">
    @csrf
    @if($post->exists) @method('PUT') @endif

    <div class="mb-3"><label class="form-label">Tiêu đề</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $post->title) }}" required></div>

    <div class="mb-3"><label class="form-label">Nội dung</label>
        <textarea name="body" rows="10" class="form-control" required>{{ old('body', $post->body) }}</textarea></div>

    <div class="row">
        <div class="col-md-6 mb-3"><label class="form-label">Danh mục</label>
            <select name="category_id" class="form-select" required>
                <option value="">-- Chọn danh mục --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected((int) old('category_id', $post->category_id) === $category->id)>{{ $category->name }}</option>
                @endforeach
            </select></div>

        <div class="col-md-6 mb-3"><label class="form-label">Ảnh bìa</label>
            <input type="file" name="image" accept="image/*" class="form-control">
            @if($post->image)<img src="{{ asset('storage/'.$post->image) }}" class="mt-2 rounded" height="70" alt="">@endif</div>
    </div>

    <div class="mb-3"><label class="form-label d-block">Tag</label>
        @foreach($tags as $tag)
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="tags[]" id="tag{{ $tag->id }}" value="{{ $tag->id }}" @checked(in_array($tag->id, $selectedTags))>
                <label class="form-check-label" for="tag{{ $tag->id }}">{{ $tag->name }}</label>
            </div>
        @endforeach</div>

    <div class="row">
        <div class="col-md-6 mb-3"><label class="form-label">Trạng thái</label>
            <select name="status" id="status" class="form-select">
                @foreach(\App\Models\Post::STATUSES as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $post->status) === $value)>{{ $label }}</option>
                @endforeach
            </select></div>
        <div class="col-md-6 mb-3" id="schedule-box"><label class="form-label">Thời gian hẹn đăng</label>
            <input type="datetime-local" name="published_at" class="form-control"
                   value="{{ old('published_at', $post->isScheduled() ? $post->published_at?->format('Y-m-d\TH:i') : '') }}"></div>
    </div>

    <div><button class="btn btn-primary">Lưu bài viết</button>
        <a href="{{ route('member.posts.index') }}" class="btn btn-link">Hủy</a></div>
</form>
@endsection
@push('scripts')
<script>
    const status = document.getElementById('status'), box = document.getElementById('schedule-box');
    const toggle = () => box.style.display = status.value === 'scheduled' ? '' : 'none';
    status.addEventListener('change', toggle); toggle();
</script>
@endpush
