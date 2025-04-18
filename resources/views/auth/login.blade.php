@extends('layouts.app')

@section('title', 'Đăng Nhập')

@section('content')
<div class="login-page">
  <div class="login-card">
    <h1 class="text-center mb-4">Đăng Nhập</h1>
    <form action="{{ route('login') }}" method="POST" id="loginForm">
      @csrf
      <div class="mb-3">
        <label for="userEmail" class="form-label">Địa chỉ Email:</label>
        <input type="email" name="email" id="userEmail" class="form-control" value="{{ old('email') }}" required>
      </div>
      <div class="mb-3">
        <label for="userPassword" class="form-label">Mật khẩu:</label>
        <input type="password" name="password" id="userPassword" class="form-control" required>
      </div>
      <!-- Thêm widget reCAPTCHA -->
      <div class="mb-3">
        {!! NoCaptcha::display() !!}
        @if ($errors->has('g-recaptcha-response'))
          <span class="text-danger">{{ $errors->first('g-recaptcha-response') }}</span>
        @endif
      </div>
      <div class="mb-3 form-check">
        <input type="checkbox" name="remember" id="rememberMe" class="form-check-input">
        <label for="rememberMe" class="form-check-label">Nhớ mật khẩu</label>
      </div>
      <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary" id="loginButton">Đăng Nhập</button>
      </div>
    </form>
    <div class="login-footer text-center">
      <a href="{{ url('/password/reset') }}" class="d-block mb-2">Quên mật khẩu?</a>
      <a href="{{ route('register') }}" class="d-block">Bạn chưa có tài khoản? Đăng ký</a>
    </div>
  </div>
</div>
<!-- Bao gồm JS của reCAPTCHA -->
{!! NoCaptcha::renderJs() !!}
<style>
  .login-page {
    min-height: 50vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }
  .login-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    padding: 30px;
    width: 100%;
    max-width: 500px;
    color: #333;
  }
  .login-card h1 {
    font-family: 'Montserrat', sans-serif;
    color: #333;
  }
  .login-card .form-label {
    color: #333;
  }
  .login-card .form-control {
    border-radius: 5px;
    border: 1px solid #ccc;
    color: #333;
  }
  .login-card .btn-primary {
    background: #ff4081;
    border: none;
    font-weight: bold;
    transition: background 0.3s ease;
  }
  .login-card .btn-primary:hover {
    background: #e91e63;
  }
  .login-footer a {
    color: #ff4081;
    text-decoration: none;
    transition: color 0.3s ease;
  }
  .login-footer a:hover {
    color: #e91e63;
  }
</style>
@endsection
