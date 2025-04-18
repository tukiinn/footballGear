@extends('layouts.admin')

@section('content')
<div class="container p-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">     <i class="fas fa-home"></i></a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.vouchers.index') }}">Voucher</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Thêm Voucher Mới</li>
        </ol>
    </nav>

    <!-- Tiêu đề trang -->
    <h1 class="mb-4 text-center">Thêm Bài Voucher</h1>


        <form action="{{ route('admin.vouchers.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="code" class="form-label">Mã Voucher</label>
                <input type="text" name="code" id="code" class="form-control" value="{{ old('code') }}" required>
            </div>
            <div class="mb-3">
                <label for="discount" class="form-label">Giảm giá</label>
                <input type="number" name="discount" id="discount" class="form-control" value="{{ old('discount') }}" required>
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Loại giảm giá</label>
                <select name="type" id="type" class="form-select" required>
                    <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Phần trăm</option>
                    <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Số tiền cố định</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="max_usage" class="form-label">Số lần sử dụng tối đa</label>
                <input type="number" name="max_usage" id="max_usage" class="form-control" value="{{ old('max_usage') }}" required>
            </div>
            <div class="mb-3">
                <label for="start_date" class="form-label">Ngày bắt đầu</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date') }}">
            </div>
            <div class="mb-3">
                <label for="end_date" class="form-label">Ngày kết thúc</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}">
            </div>
            <button type="submit" class="btn btn-success">Tạo Voucher</button>
        </form>
    </div>
    <style>
        /* Container */
        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
    
        /* Breadcrumb */
        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .breadcrumb-item a {
            text-decoration: none;
            color: #ff4081;
        }
        .breadcrumb-item a:hover {
            text-decoration: underline;
        }
        .breadcrumb-item.active {
            color: #6c757d;
        }
    
        /* Bảng */
        .table {
            margin-bottom: 0;
        }
    </style>
@endsection
