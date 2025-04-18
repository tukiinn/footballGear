@extends('layouts.app')

@section('title', 'Quên mật khẩu')

@section('content')
<div class="login-page">
  <div class="login-card">
    <h1 class="text-center mb-4">Quên mật khẩu</h1>

    <!-- Hiển thị thông báo thành công -->
    @if (session('status'))
      <div class="alert alert-success">
        {{ session('status') }}
      </div>
    @endif

    <!-- Hiển thị thông báo lỗi -->
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <div class="mb-3">
        <label for="email" class="form-label">Địa chỉ Email:</label>
        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
      </div>
      <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary">Gửi liên kết đặt lại mật khẩu</button>
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
  .alert {
    padding: 10px 15px;
    border-radius: 5px;
    margin-bottom: 15px;
  }
  .alert-success {
    background-color: #d4edda;
    color: #155724;
  }
  .alert-danger {
    background-color: #f8d7da;
    color: #721c24;
  }
</style>
@endsection
