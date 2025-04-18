@extends('layouts.admin')

@section('content')
<div class="container p-4 modern-container" id="printableArea">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb modern-breadcrumb no-print">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-home"></i>
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.orders.index') }}">Đơn hàng</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Chi tiết đơn hàng #{{ $order->id }}</li>
        </ol>
    </nav>
    <!-- Nút in đơn hàng (không in các thành phần khác) -->
    <div class="text-center mt-4 no-print d-flex justify-content-end ">
        <button class="btn btn-secondary" onclick="window.print()">
            <i class="fas fa-print"></i> In đơn hàng
        </button>
    </div>
    <!-- Tiêu đề trang -->
    <h2 class="modern-title text-center mb-4">Chi tiết đơn hàng #{{ $order->id }}</h2>
    
    @php
        // Mapping trạng thái đơn hàng (key là tiếng Anh)
        $statusMapping = [
            'pending'   => ['label' => 'Chờ xác nhận', 'icon' => 'fas fa-hourglass-half', 'class' => 'badge bg-warning'],
            'confirmed' => ['label' => 'Đã xác nhận', 'icon' => 'fas fa-check-circle', 'class' => 'badge bg-primary'],
            'shipping'  => ['label' => 'Đang giao hàng', 'icon' => 'fas fa-truck', 'class' => 'badge bg-info'],
            'completed' => ['label' => 'Hoàn thành', 'icon' => 'fas fa-check-double', 'class' => 'badge bg-success'],
            'cancelled' => ['label' => 'Đã hủy', 'icon' => 'fas fa-times-circle', 'class' => 'badge bg-danger'],
        ];
    @endphp

    <!-- Thông tin đơn hàng -->
    <div class="card order-details-card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong><i class="fas fa-user"></i> Khách hàng:</strong> {{ $order->ten_khach_hang }}</p>
                    <p><strong><i class="fas fa-phone"></i> Số điện thoại:</strong> {{ $order->so_dien_thoai }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong><i class="fas fa-map-marker-alt"></i> Địa chỉ:</strong> {{ $order->dia_chi }}</p>
                    <p><strong><i class="fas fa-money-bill-wave"></i> Tổng tiền:</strong> {{ number_format($order->tong_tien, 0) }} VND</p>
                </div>
                <div class="col-md-4">
                    <p><strong><i class="fas fa-credit-card"></i> Phương thức thanh toán:</strong> {{ $order->phuong_thuc_thanh_toan }}</p>
                    <p>
                        <strong><i class="fas fa-info-circle"></i> Trạng thái:</strong>
                        @if(isset($statusMapping[$order->trang_thai]))
                            <span class="{{ $statusMapping[$order->trang_thai]['class'] }}">
                                <i class="{{ $statusMapping[$order->trang_thai]['icon'] }}"></i>
                                {{ $statusMapping[$order->trang_thai]['label'] }}
                            </span>
                        @else
                            <span class="badge bg-secondary">{{ $order->trang_thai }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách sản phẩm trong đơn hàng -->
    <h4 class="modern-subtitle mb-3">Sản phẩm trong đơn hàng:</h4>
    <div class="table-responsive modern-table-wrapper">
        <table class="table table-striped table-hover modern-table">
            <thead class="thead-dark">
                <tr>
                    <th>Ảnh</th>
                    <th>Mã sản phẩm</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orderItems as $item)
                    <tr>
                        <td>
                            <img src="{{ asset($item->product->image ?? 'https://via.placeholder.com/100') }}" 
                                 alt="Ảnh sản phẩm" 
                                 class="modern-product-img">
                        </td>
                        <td>#{{ $item->product->id }}</td>
                        <td>{{ $item->product->product_name }}</td>
                        <td>{{ number_format($item->gia, 0) }} VND</td>
                        <td>{{ $item->so_luong }}</td>
                        <td>{{ number_format($item->thanh_tien, 0) }} VND</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Nút xác nhận đơn hàng (nếu cần) -->
    @if ($order->trang_thai === 'pending')
        <form action="{{ route('order.payCOD', $order->id) }}" method="POST" class="text-center mt-4">
            @csrf
            <button type="submit" class="btn btn-success btn-lg modern-confirm-btn">
                <i class="fas fa-check-circle"></i> Xác nhận đơn hàng
            </button>
        </form>
    @endif
</div>

<!-- CSS Tùy chỉnh -->
<style>
    /* Modern Container */
    .modern-container {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding: 30px;
    }
    
    /* Modern Breadcrumb */
    .modern-breadcrumb {
        font-size: 0.95rem;
        background-color: transparent;
        margin-bottom: 25px;
    }
    .modern-breadcrumb .breadcrumb-item a {
        color: #ff4081;
        text-decoration: none;
    }
    .modern-breadcrumb .breadcrumb-item a:hover {
        text-decoration: underline;
    }
    .modern-breadcrumb .breadcrumb-item.active {
        color: #6c757d;
    }
    
    /* Modern Title & Subtitle */
    .modern-title {
        font-size: 2rem;
        font-weight: 600;
        color: #333;
    }
    .modern-subtitle {
        font-size: 1.5rem;
        font-weight: 500;
        color: #444;
    }
    
    /* Order Details Card */
    .order-details-card {
        border: none;
        border-radius: 10px;
        background: #f9f9f9;
    }
    .order-details-card .card-body {
        padding: 20px;
        font-size: 1rem;
        color: #555;
    }
    .order-details-card p {
        margin-bottom: 10px;
    }
    
    /* Modern Table */
    .modern-table-wrapper {
        margin-top: 20px;
    }
    .modern-table {
        border-collapse: separate;
        border-spacing: 0;
    }
    .modern-table th, .modern-table td {
        vertical-align: middle;
        text-align: center;
        padding: 15px;
    }
    .thead-dark {
        background-color: #343a40;
        color: #fff;
    }
    .modern-product-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        transition: transform 0.3s ease;
    }
    .modern-product-img:hover {
        transform: scale(1.05);
    }
    
    /* Modern Confirm Button */
    .modern-confirm-btn {
        background-color: #28a745;
        border: none;
        font-weight: bold;
        padding: 15px 40px;
        border-radius: 8px;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    .modern-confirm-btn:hover {
        background-color: #218838;
        transform: scale(1.05);
    }
    .modern-confirm-btn i {
        margin-right: 10px;
    }
    
    /* Khi in chỉ hiển thị phần container với id printableArea và thu nhỏ kích thước */
    @media print {
        .no-print {
        display: none !important;
    }
        body * {
            visibility: hidden;
        }
        #printableArea, #printableArea * {
            visibility: visible;
        }
        #printableArea {
            position: absolute;
            top: 0;
            left: 20px;
            width: 100%;
            transform-origin: top left;
        }
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .order-details-card .row > div {
            margin-bottom: 15px;
        }
    }
</style>
@endsection
