@extends('layouts.app')

@section('title', 'Đăng Ký')

@section('content')
<div class="login-page">
  <div class="login-card">
    <h1 class="text-center mb-4">Đăng Ký</h1>

    @if ($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
    @endif

    <form id="registerForm" action="{{ route('register') }}" method="POST">
      @csrf
      <div class="mb-3">
        <label for="name" class="form-label">Họ và Tên:</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Địa chỉ Email:</label>
        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Mật khẩu:</label>
        <input type="password" name="password" id="password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="password_confirmation" class="form-label">Xác nhận Mật khẩu:</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
      </div>
      
      <!-- Thêm reCAPTCHA -->
      <div class="mb-3">
        {!! NoCaptcha::display() !!}
      </div>

      <div class="d-grid mb-3">
        <button type="submit" id="buttonRegister" class="btn btn-primary">Đăng Ký</button>
      </div>
    </form>

    <div class="login-footer text-center">
      <a href="{{ route('login') }}" class="d-block">Bạn đã có tài khoản? Đăng Nhập</a>
    </div>
  </div>
</div>

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

{!! NoCaptcha::renderJs() !!}
@endsection
