@extends('layouts.app')

@section('content')
<!-- Custom CSS cho trang giỏ hàng -->
<style>
    /* Bỏ đường viền dọc cho bảng giỏ hàng */
    .table-bordered td,
    .table-bordered th {
        border-left: 0 !important;
        border-right: 0 !important;
    }
    /* Nút "Tiếp tục mua sắm" */
    .ttmua {
        background: none !important;  /* Loại bỏ nền */
        border: none !important;       /* Loại bỏ viền */
        box-shadow: none !important;   /* Loại bỏ đổ bóng */
        color: inherit;                /* Dùng màu chữ của phần tử cha */
        font-size: 15px;
        font-weight: bold;
    }
    /* Định dạng chung cho thông tin sản phẩm */
    .product-cell a {
        text-decoration: none;
        color: inherit;
    }
    .product-cell img {
        max-width: 80px;
    }
    /* Thêm hiệu ứng cho các nút hành động */
    .btn-action:hover {
        opacity: 0.85;
    }
    .breadcrumb-item + .breadcrumb-item::before {
    content: "/";
    color: #fff; /* Màu trắng cho dấu "/" */
    padding: 0 0.5rem; /* Khoảng cách xung quanh dấu "/" */
}

      /* Breadcrumb Container */
      .breadcrumb-container {
            background-color: #1e1e1e;
            padding: 10px 20px;
            border-bottom: 1px solid #444;
        }
        .breadcrumb {
            margin: 0;
            font-size: 0.9rem;
        }
        .breadcrumb-item a {
            color: #ff4081;
            text-decoration: none;
            transition: text-decoration 0.3s ease;
        }
        .breadcrumb-item a:hover {
            text-decoration: underline;
        }
        .breadcrumb-item.active {
            color: #6c757d;
        }
</style>
<div class="breadcrumb-container">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Trang chủ</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
            </ol>
        </nav>
    </div>
</div>
<div class="container my-5">
    @if($cartItems && count($cartItems) > 0)
        <div class="row">
            <!-- Cột bên trái: Danh sách sản phẩm trong giỏ hàng -->
            <div class="col-md-8">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-end">Giá</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Tổng phụ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $grandTotal = 0; @endphp
                            @foreach($cartItems as $cartKey => $item)
                                @php
                                    // Sử dụng discount_price nếu có, ngược lại dùng price
                                    $basePrice = $item['discount_price'] ?? $item['price'];
                                    
                                    // Xác định hệ số cân nặng nếu sản phẩm bán theo kg
                                    $weightFactor = 1;
                                    if(isset($item['unit']) && $item['unit'] == 'kg' && isset($item['size'])) {
                                        if($item['size'] == '500g'){
                                            $weightFactor = 0.5;
                                        } elseif($item['size'] == '250g'){
                                            $weightFactor = 0.25;
                                        }
                                    }
                                    
                                    // Tính giá đơn vị hiệu chỉnh theo kích thước
                                    $effectivePrice = $basePrice * $weightFactor;
                                    
                                    // Tính thành tiền của dòng sản phẩm
                                    $lineTotal = $effectivePrice * $item['so_luong'];
                                    $grandTotal += $lineTotal;
                                @endphp
                                <tr>
                                    <td class="product-cell bg-dark">
                                        <div class="d-flex align-items-center">
                                            <!-- Form xóa sản phẩm khỏi giỏ hàng -->
                                            <form action="{{ route('cart.remove', $item['product_id']) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');" class="me-2">
                                                @csrf
                                                <input type="hidden" name="size" value="{{ $item['size'] ?? '' }}">
                                                <button type="submit" class="btn btn-lg border-0 btn-action" style="background: none; font-size: 20px; color: #fff;">&times;</button>
                                            </form>
                                            <!-- Hiển thị thông tin sản phẩm -->
                                            <a href="{{ route('products.show', $item['product_id']) }}" class="d-flex align-items-center text-decoration-none text-white">
                                                <img src="{{ asset($item['image'] ?? 'https://via.placeholder.com/100') }}" alt="{{ $item['name'] }}" class="img-fluid">
                                                <div class="ms-3">
                                                    <div>{{ $item['name'] }}</div>
                                                    @if(isset($item['size']) && $item['size'])
                                                        <small class="text-secondary">Size: {{ $item['size'] }}</small>
                                                    @endif
                                                </div>
                                            </a>
                                        </div>
                                    </td>
                                    <td class="text-end bg-dark text-white">{{ number_format($effectivePrice, 0, ',', '.') }} VND</td>
                                    <td class="text-center bg-dark text-white">{{ $item['so_luong'] }}</td>
                                    <td class="text-end bg-dark text-white">{{ number_format($lineTotal, 0, ',', '.') }} VND</td>                                    
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Nút "Tiếp tục mua sắm" -->
                <a href="{{ route('products.index') }}" class="btn ttmua btn-action text-white">
                    <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                </a>
            </div>
            <!-- Cột bên phải: Bảng tóm tắt giỏ hàng -->
            <div class="col-md-4">
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th colspan="2" class="text-center">Cộng giỏ hàng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Tạm tính:</td>
                            <td class="text-end">{{ number_format($grandTotal, 0, ',', '.') }} VND</td>
                        </tr>
                        <tr>
                            <td>Giao hàng:</td>
                            <td class="text-end">Giao hàng miễn phí</td>
                        </tr>
                        <tr>
                          
                        </tr>
                        <tr>
                            <td>Tính phí giao hàng:</td>
                            <td class="text-end">0 VND</td>
                        </tr>
                        <tr class="table-secondary">
                            <td class="fw-bold">Tổng:</td>
                            <td class="fw-bold text-end">{{ number_format($grandTotal, 0, ',', '.') }} VND</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-end">
                                @if(auth()->check())
                                    <a href="{{ route('cart.checkout') }}" class="btn btn-warning btn-lg btn-action">
                                        <i class="fa-solid fa-basket-shopping"></i>
                                        Tiến hành thanh toán
                                    </a>
                                @else
                                    <button type="button" class="btn btn-warning btn-lg btn-action" onclick="requireLogin()">
                                        <i class="fa-solid fa-basket-shopping"></i>
                                        Tiến hành thanh toán
                                    </button>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="empty-cart-message mt-3">Giỏ hàng của bạn trống!</div>
    @endif
    <style>
        .empty-cart-message {
            background-color: #f8d7da;  /* Nền hồng nhạt */
            color: #721c24;             /* Màu chữ đỏ đậm */
            border: 1px solid #f5c6cb;  /* Viền nhẹ */
            padding: 15px;
            border-radius: 4px;
            text-align: center;
            font-size: 16px;
            font-weight: 500;
        }
        </style>
</div>

<script>
    function requireLogin() {
        Swal.fire({
            icon: 'warning',
            title: 'Bạn chưa đăng nhập!',
            text: 'Vui lòng đăng nhập để tiếp tục thanh toán.',
            showCancelButton: true,
            confirmButtonText: 'Đăng nhập ngay',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('login') }}";
            }
        });
    }
</script>
@endsection
