@extends('layouts.app')
@section('title', 'Đăng ký')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card card-body">
            <h1 class="h4 mb-3">Đăng ký tài khoản</h1>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-3"><label class="form-label">Họ tên</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Nhập lại mật khẩu</label>
                    <input type="password" name="password_confirmation" class="form-control" required></div>
                <button class="btn btn-primary w-100">Đăng ký</button>
            </form>
            <p class="mt-3 mb-0 small">Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a></p>
        </div>
    </div>
</div>
@endsection
