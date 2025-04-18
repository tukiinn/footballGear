@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu')

@section('content')
<div class="login-page">
  <div class="login-card">
    <h1 class="text-center mb-4">Đặt lại mật khẩu</h1>
    <form method="POST" action="{{ route('password.update') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      <div class="mb-3">
        <label for="email" class="form-label">Địa chỉ Email:</label>
        <input id="email" type="email" class="form-control" name="email" value="{{ $email ?? old('email') }}" required autofocus>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Mật khẩu mới:</label>
        <input id="password" type="password" class="form-control" name="password" required>
      </div>
      <div class="mb-3">
        <label for="password-confirm" class="form-label">Xác nhận mật khẩu mới:</label>
        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
      </div>
      <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary">Đặt lại mật khẩu</button>
      </div>
    </form>
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
</style>
@endsection
