<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Trang chủ') - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>.post-cover{height:180px;object-fit:cover}.post-body{white-space:normal;line-height:1.8}</style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">{{ config('app.name') }}</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="nav">
            <form class="d-flex me-auto my-2 my-lg-0" action="{{ route('home') }}" method="GET">
                <input class="form-control form-control-sm me-2" type="search" name="q" value="{{ request('q') }}" placeholder="Tìm bài viết...">
                <button class="btn btn-sm btn-outline-light">Tìm</button>
            </form>
            <ul class="navbar-nav align-items-lg-center gap-lg-2">
                @auth
                    <li class="nav-item"><a class="nav-link" href="{{ route('member.posts.index') }}">Bài của tôi</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('member.posts.create') }}">Viết bài</a></li>
                    @if(auth()->user()->is_admin)
                        <li class="nav-item"><a class="nav-link text-warning" href="{{ route('admin.dashboard') }}">Quản trị</a></li>
                    @endif
                    <li class="nav-item"><span class="navbar-text">{{ auth()->user()->name }}</span></li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">@csrf
                            <button class="btn btn-sm btn-outline-light">Đăng xuất</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Đăng nhập</a></li>
                    <li class="nav-item"><a class="btn btn-sm btn-primary" href="{{ route('register') }}">Đăng ký</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<div class="container pb-5">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
