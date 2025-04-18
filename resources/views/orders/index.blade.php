@extends('layouts.app')

@section('content')

  <!-- Breadcrumb -->
  <div class="breadcrumb-container">
    <div class="container">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
          <li class="breadcrumb-item active" aria-current="page">Danh sách đơn hàng</li>
        </ol>
      </nav>
    </div>
  </div>



<<div class="container my-4">
    @php
    $statusMapping = [
        'pending'           => 'Chờ xác nhận',
        'confirmed'         => 'Đã xác nhận',
        'shipping'          => 'Đang giao hàng',
        'completed'         => 'Hoàn thành',
        'canceled'          => 'Đã hủy',
    ];

$paymentMapping = [
    'pending' => 'Chưa thanh toán',
    'paid'    => 'Đã thanh toán',
];
@endphp
    @if($orders->count() > 0)
        <div class="row">
            @foreach($orders as $order)
                <div class="col-md-6 col-lg-4">
                    <div class="card order-card mb-4 shadow">
                        <div class="card-header bg-secondary text-white text-center">
                            Đơn hàng #{{ $order->id }}
                        </div>
                        <div class="card-body bg-dark">
                            <p class="text-white"><i class="fas fa-user"></i> <strong>Khách hàng:</strong> {{ $order->ten_khach_hang }}</p>
                            <p class="text-white"><i class="fas fa-phone"></i> <strong>Số điện thoại:</strong> {{ $order->so_dien_thoai }}</p>
                            <p class="text-white"><i class="fas fa-map-marker-alt"></i> <strong>Địa chỉ:</strong> {{ $order->dia_chi }}</p>
                            <p class="text-white">
                                <i class="fas fa-money-bill-wave"></i> <strong>Tổng tiền:</strong>
                                <span class="price-highlight">{{ number_format($order->tong_tien, 0) }} VND</span>
                            </p>
                            <p >
                                @php
                                $statusMapping = [
                                    'pending'   => 'Chờ xác nhận',
                                    'confirmed' => 'Đã xác nhận',
                                    'shipping'  => 'Đang giao hàng',
                                    'completed' => 'Hoàn thành',
                                    'cancelled'  => 'Đã hủy',
                                ];

                                // Mapping cho CSS class của trạng thái đơn hàng
                                $statusBadgeClassMapping = [
                                    'pending'   => 'badge bg-secondary',
                                    'confirmed' => 'badge bg-primary',
                                    'shipping'  => 'badge bg-info',
                                    'completed' => 'badge bg-success',
                                    'cancelled'  => 'badge bg-danger',
                                ];

                                $paymentMapping = [
                                    'pending' => 'Chưa thanh toán',
                                    'paid'    => 'Đã thanh toán',
                                ];

                                // Mapping cho CSS class của trạng thái thanh toán
                                $paymentBadgeClassMapping = [
                                    'pending' => 'badge bg-secondary',
                                    'paid'    => 'badge bg-success',
                                ];
                            @endphp
                                <p class="text-white">
                                <i class="fas fa-info-circle"></i> <strong>Trạng thái:</strong>
                                <span class="{{ $statusBadgeClassMapping[$order->trang_thai] ?? 'badge bg-secondary' }}">
                                    {{ $statusMapping[$order->trang_thai] ?? $order->trang_thai }}
                                </span>
                                
                                <span class="{{ $paymentBadgeClassMapping[$order->payment_status] ?? 'badge bg-secondary' }}">
                                    {{ $paymentMapping[$order->payment_status] ?? $order->payment_status }}
                                </span>
                                
                            </p>

                            <p class="text-white"><i class="fas fa-calendar-alt"></i> <strong>Ngày tạo:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-warning btn-block mt-3">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
               <!-- Phân trang -->
               <div class="d-flex justify-content-center">
                {{ $orders->links('pagination::bootstrap-4') }}
            </div>
    @else
        <p class="mt-3 text-white">Không có đơn hàng nào</p>
    @endif
</div>
<!-- CSS cho giao diện với chủ đề nông sản (xanh lá) -->
<style>

/* Order Card */
.order-card {
    border: none;
    border-radius: 10px;
    overflow: hidden;
    background-color: #fff;
    transition: transform 0.3s;
}

.order-card:hover {
    transform: translateY(-5px);
}

/* Card header */
.order-card .card-header {
    font-size: 18px;
    font-weight: bold;
    text-align: center;
}

/* Card body */
.order-card .card-body p {
    font-size: 15px;
    margin-bottom: 0.75rem;
}

.order-card .card-body i {
    margin-right: 6px;
    color: #af9046; /* sắc xanh lá tối */
}

/* Nổi bật giá với màu xanh lá đậm */
.price-highlight {
    color: red !important;
    font-weight: bold;
    font-size: 16px;
}

/* Nút "Xem chi tiết" */
.btn-block {
    display: block;
    width: 100%;
}


/* Responsive adjustments */
@media (max-width: 576px) {
    .order-card .card-body p {
        font-size: 14px;
    }
}
 /* Breadcrumb */
 .breadcrumb-item + .breadcrumb-item::before {
      content: "/";
      color: #fff;
      padding: 0 0.5rem;
    }
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
      transition: color 0.3s ease;
    }
    .breadcrumb-item a:hover {
      color: #d81b60;
    }
    .breadcrumb-item.active {
      color: #6c757d;
    }
/* Container phân trang hiện đại */
.pagination {
    display: inline-flex;
    background-color: #1e1e1e;
    padding: 0.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
}

/* Link của trang */
.pagination .page-item .page-link {
    color: #ddd;
    background-color: transparent;
    border: none;
    margin: 0 4px;
    padding: 0.5rem 0.75rem;
    transition: all 0.3s ease;
    font-weight: 500;
}

/* Hover và focus: hiệu ứng nổi nhẹ, chuyển màu */
.pagination .page-item .page-link:hover,
.pagination .page-item .page-link:focus {
    background-color: #333;
    border-radius: 4px;
    transform: translateY(-2px);
    color: #fff;
}

/* Trang đang được chọn */
.pagination .page-item.active .page-link {
    background-color: #ff4081;
    color: #fff;
    border-radius: 4px;
    transform: translateY(-2px);
}

/* Các trang không khả dụng */
.pagination .page-item.disabled .page-link {
    opacity: 0.5;
    pointer-events: none;
}

</style>
@endsection
