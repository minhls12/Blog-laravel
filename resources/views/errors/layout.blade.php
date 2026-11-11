<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center min-vh-100">
<div class="container text-center">
    <div class="display-1 fw-bold text-primary">@yield('code')</div>
    <h1 class="h3">@yield('title')</h1>
    <p class="text-muted">@yield('message')</p>
    <a href="/" class="btn btn-primary">Về trang chủ</a>
</div>
</body>
</html>
