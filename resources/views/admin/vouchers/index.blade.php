@extends('layouts.admin')

@section('content')
    <div class="container p-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-home"></i>
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Danh sách Voucher</li>
            </ol>
        </nav>
        <!-- End Breadcrumb -->

        <!-- Thanh công cụ: Nút Tạo Voucher và Form tìm kiếm -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <a href="{{ route('admin.vouchers.create') }}" class="btn btn-add-voucher">Tạo Voucher mới</a>
            <form method="GET" action="{{ route('admin.vouchers.index') }}" class="mb-2">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm voucher..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                </div>
            </form>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Mã Voucher</th>
                    <th>Giảm giá</th>
                    <th>Loại</th>
                    <th>Số lần sử dụng tối đa</th>
                    <th>Số lần đã sử dụng</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày kết thúc</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vouchers as $voucher)
                    <tr>
                        <td>{{ $voucher->code }}</td>
                        <td>{{ $voucher->discount }} {{ $voucher->type == 'percentage' ? '%' : '₫' }}</td>
                        <td>{{ ucfirst($voucher->type) }}</td>
                        <td>{{ $voucher->max_usage }}</td>
                        <td>{{ $voucher->used }}</td>
                        <td>{{ $voucher->start_date ? \Carbon\Carbon::parse($voucher->start_date)->format('d/m/Y') : 'Không xác định' }}</td>
                        <td>{{ $voucher->end_date ? \Carbon\Carbon::parse($voucher->end_date)->format('d/m/Y') : 'Không xác định' }}</td>
                        <td>
                            <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="btn btn-icon btn-edit" title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-delete" title="Xóa">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
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
    
        /* Thanh công cụ */
        .d-flex.justify-content-between {
            margin-bottom: 20px;
        }
    
        /* Nút Tạo Voucher */
        .btn-add-voucher {
            background: linear-gradient(45deg, #ff6ec4, #ff4dab);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 12px 25px;
            font-size: 1rem;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .btn-add-voucher:hover {
            background: linear-gradient(45deg, #ff4dab, #ff6ec4);
            transform: translateY(-2px);
            text-decoration: none;
        }
    
        /* Input Group Tìm kiếm */
        .input-group {
            max-width: 400px;
            border: 1px solid #ff4dab;
            border-radius: 50px;
            overflow: hidden;
        }
        .input-group .form-control {
            border: none;
            padding: 12px 15px;
        }
        .input-group .form-control:focus {
            box-shadow: none;
            outline: none;
        }
        .input-group .btn {
            padding: 12px 20px;
            background-color: #ff4dab;
            border: none;
            color: #fff;
            transition: background 0.3s ease;
        }
        .input-group .btn:hover {
            background-color: #e635a7;
        }
    
        /* Bảng */
        .table {
            margin-bottom: 0;
        }

        /* Nút Icon cho Edit & Delete */
        .btn-icon {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 50%;
            font-size: 1rem;
            transition: transform 0.2s ease;
            color: #fff;
        }
        .btn-edit {
            background-color: #007bff;
        }
        .btn-edit:hover {
            background-color: #0056b3;
            transform: scale(1.1);
        }
        .btn-delete {
            background-color: #dc3545;
        }
        .btn-delete:hover {
            background-color: #c82333;
            transform: scale(1.1);
        }
    </style>
@endsection
