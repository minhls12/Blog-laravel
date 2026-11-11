@extends('layouts.app')
@section('title', 'Đăng nhập')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card card-body">
            <h1 class="h4 mb-3">Đăng nhập</h1>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3"><label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus></div>
                <div class="mb-3"><label class="form-label">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" required></div>
                <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label></div>
                <button class="btn btn-primary w-100">Đăng nhập</button>
            </form>
            <p class="mt-3 mb-0 small">Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký</a></p>
        </div>
    </div>
</div>
@endsection
