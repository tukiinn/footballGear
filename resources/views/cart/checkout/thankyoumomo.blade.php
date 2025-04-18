@extends('layouts.app')

@section('content')

@php
    $statusMapping = [
        'pending'   => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'shipping'  => 'Đang giao hàng',
        'completed' => 'Hoàn thành',
        'canceled'  => 'Đã hủy',
    ];

    $paymentMapping = [
        'pending' => 'Chưa thanh toán',
        'paid'    => 'Đã thanh toán',
    ];
@endphp

<!-- Breadcrumb Wrapper -->
<div class="breadcrumb-wrapper">
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb custom-breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
        <li class="breadcrumb-item active" aria-current="page">Đơn hàng</li>
      </ol>
    </nav>
  </div>
</div>

<!-- Custom CSS cho giao diện dark & hiệu ứng viền mờ -->
<style>
  /* Breadcrumb Wrapper với gradient và viền mờ */
  .breadcrumb-wrapper {
    background: linear-gradient(90deg, #1e1e1e, #2c2c2c);
    padding: 15px 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.5);
  }
  .custom-breadcrumb {
    background: transparent;
    margin: 0;
    padding: 0;
    font-size: 1rem;
  }
  .custom-breadcrumb .breadcrumb-item a {
    color: #ff4081;
    text-decoration: none;
    transition: color 0.3s ease;
  }
  .custom-breadcrumb .breadcrumb-item a:hover {
    color: #fff;
  }
  .custom-breadcrumb .breadcrumb-item.active {
    color: #fff;
  }
  .custom-breadcrumb .breadcrumb-item + .breadcrumb-item::before {
    content: ">";
    color: #fff;
    margin: 0 8px;
  }

  /* Card dark theme với viền mờ */
  .card {
    background-color: #1e1e1e;
    border: 1px solid rgba(255,255,255,0.1);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
  }
  .card-header {
    background-color: #1e1e1e;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    color: #fff;
  }
  .card-body {
    background-color: #1e1e1e;
    color: #fff;
  }
  .table {
    background-color: #1e1e1e;
    color: #fff;
  }
  .table thead th {
    background-color: #2c2c2c;
    color: #fff;
  }
  .table-bordered td,
  .table-bordered th {
    border-left: 0 !important;
    border-right: 0 !important;
    border-color: rgba(255,255,255,0.1) !important;
  }
  .table-secondary {
    background-color: #2c2c2c !important;
    color: #fff;
  }
  /* Ghi đè dark theme cho bảng trong card */
  .card .table td,
  .card .table th {
    background-color: transparent;
    color: #fff;
    border-color: rgba(255,255,255,0.1) !important;
  }
  .card .table thead th {
    background-color: #2c2c2c;
    color: #fff;
  }
  .text-danger {
    color: #ff6b6b !important;
  }
  .btn-action:hover {
    opacity: 0.85;
  }

  /* Ticket (Thank You Section) Dark Style */
  .ticket {
    max-width: 800px;
    margin: 30px auto;
    background-color: #2c2c2c;
    border: 2px dashed rgba(255,255,255,0.3);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0,0,0,0.5);
  }
  .ticket-header {
    background: linear-gradient(90deg, #1e1e1e, #2c2c2c);
    padding: 20px;
    text-align: center;
  }
  .ticket-header h2 {
    color: #ff4081;
    margin: 0;
    font-size: 28px;
  }
  .ticket-body {
    padding: 20px;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    color: #fff;
  }
  .ticket-body .info {
    flex: 0 0 45%;
    margin-bottom: 20px;
  }
  .ticket-body .info strong {
    display: block;
    color: #ccc;
    margin-bottom: 5px;
  }
  .ticket-body .info span {
    font-size: 16px;
    color: #fff;
  }
  @media (max-width: 480px) {
    .ticket-body .info {
      flex: 0 0 100%;
    }
  }
</style>

<!-- Ticket xác nhận đơn hàng (Thank You Section) -->
<div class="container my-5">
  <div class="ticket">
    <div class="ticket-header">
      <h2>Cảm ơn bạn. Đơn hàng của bạn đã được nhận.</h2>
    </div>
    <div class="ticket-body">
      <div class="info">
        <strong><i class="bi bi-receipt"></i> Mã đơn hàng:</strong>
        <span>{{ $order->id }}</span>
      </div>
      <div class="info">
        <strong><i class="bi bi-calendar-event"></i> Ngày:</strong>
        <span>{{ \Carbon\Carbon::parse($order->created_at)->locale('vi')->translatedFormat('d F, Y') }}</span>
      </div>
      <div class="info">
        <strong><i class="bi bi-cash-stack"></i> Tổng:</strong>
        <span>{{ number_format($order->tong_tien, 0, ',', '.') }}₫</span>
      </div>
      <div class="info">
        <strong><i class="bi bi-credit-card"></i> Phương thức thanh toán:</strong>
        <span>{{ $order->phuong_thuc_thanh_toan }}</span>
      </div>
      <div class="info">
        <strong><i class="bi bi-person"></i> Tên khách hàng:</strong>
        <span>{{ $order->ten_khach_hang }}</span>
      </div>
      <div class="info">
        <strong><i class="bi bi-telephone"></i> Số điện thoại:</strong>
        <span>{{ $order->so_dien_thoai }}</span>
      </div>
      <div class="info">
        <strong><i class="bi bi-geo-alt"></i> Địa chỉ giao hàng:</strong>
        <span>{{ $order->dia_chi }}</span>
      </div>
      <div class="info">
        <strong><i class="bi bi-truck"></i> Trạng thái đơn:</strong>
        <span>{{ $statusMapping[$order->trang_thai] ?? $order->trang_thai }} ({{ $paymentMapping[$order->payment_status] ?? $order->payment_status }})</span>
      </div>
    </div>
  </div>
</div>

<!-- Chi tiết đơn hàng -->
<div class="container my-3">
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-8">
      <div class="card-header text-center">
        <h4 class="mb-0">Chi tiết đơn hàng</h4>
      </div>    
      <div class="card shadow my-3">
        <div class="card-body">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th class="text-end">Giá</th>
              </tr>
            </thead>
            <tbody>
              @php
                $totalSubtotal = $order->orderItems->sum(function($item) {
                  $productPrice = $item->gia;
                  return $productPrice * $item->so_luong;
                });
                $discountAmount = $totalSubtotal - $order->tong_tien;
              @endphp
              @foreach ($order->orderItems as $item)
                <tr>
                  <td>{{ $item->product->product_name }}</td>
                  <td>{{ $item->so_luong }}</td>
                  <td class="text-end">{{ number_format($item->gia, 0, ',', '.') }}₫</td>
                </tr>
              @endforeach
              <tr>
                <td colspan="2"><strong><i class="bi bi-cart"></i> Tổng số phụ:</strong></td>
                <td class="text-end">{{ number_format($totalSubtotal, 0, ',', '.') }}₫</td>
              </tr>
              @if($discountAmount > 0)
                <tr>
                  <td colspan="2"><strong><i class="bi bi-tag"></i> Giảm giá:</strong></td>
                  <td class="text-end text-success">- {{ number_format($discountAmount, 0, ',', '.') }}₫</td>
                </tr>
              @endif
              <tr>
                <td colspan="2"><strong><i class="bi bi-truck"></i> Giao nhận hàng:</strong></td>
                <td class="text-end">Giao hàng miễn phí</td>
              </tr>
              <tr class="table-secondary">
                <td colspan="2"><strong><i class="bi bi-cash"></i> Tổng cộng:</strong></td>
                <td class="text-danger text-end fw-bold">
                  {{ number_format($order->tong_tien, 0, ',', '.') }}₫
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
