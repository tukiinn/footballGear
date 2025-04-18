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
                <a href="{{ route('admin.products.index') }}">Sản phẩm</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Chi Tiết Sản Phẩm</li>
        </ol>
    </nav>

    <!-- Tiêu đề trang -->
    <h1 class="mb-4 text-center">Chi Tiết Sản Phẩm</h1>

    <!-- Thẻ card chi tiết sản phẩm -->
    <div class="card mb-4 shadow-sm">
        <div class="row g-0">
            <div class="col-md-4">
                <img src="{{ asset($product->image) }}" class="img-fluid rounded-start" alt="{{ $product->product_name }}">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h5 class="card-title">{{ $product->product_name }}</h5>
                    
                    <p class="card-text">
                        <strong>Mô tả:</strong> 
                        {{ $product->description ?? 'Không có mô tả.' }}
                    </p>
                    
                    <p class="card-text">
                        <strong>Giá:</strong> 
                        {{ number_format($product->price, 0, ',', '.') }} VND
                        @if($product->discount_price)
                            <span class="text-danger">(Giảm: {{ number_format($product->discount_price, 0, ',', '.') }} VND)</span>
                        @endif
                    </p>
                    
                    @if($product->sizes && $product->sizes->count() > 0)
                        <p class="card-text">
                            <strong>Kích cỡ &amp; Số lượng:</strong>
                            <ul class="list-unstyled mb-0">
                                @foreach($product->sizes as $size)
                                    <li>
                                        <small>
                                            Kích cỡ: <strong>{{ $size->size }}</strong> - Số lượng: <strong>{{ $size->quantity }}</strong>
                                        </small>
                                    </li>
                                @endforeach
                            </ul>
                        </p>
                    @endif
                    
                    <p class="card-text">
                        <strong>Danh mục:</strong> {{ $product->category->category_name ?? 'Không xác định' }}
                    </p>
                    
                    <p class="card-text">
                        <strong>Trạng thái:</strong> 
                        @if($product->status)
                            <span class="badge bg-success">Hoạt động</span>
                        @else
                            <span class="badge bg-danger">Ngừng hoạt động</span>
                        @endif
                    </p>
                    
                    <p class="card-text">
                        <strong>Nổi bật:</strong>
                        @if($product->featured)
                            <span class="badge bg-info">Có</span>
                        @else
                            <span class="badge bg-secondary">Không</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Các nút thao tác -->
    <div class="d-flex justify-content-between">
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Quay lại</a>
        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary">Chỉnh sửa</a>
    </div>
</div>

<!-- CSS tùy chỉnh -->
<style>
    /* Container chính */
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

    /* Tiêu đề */
    h1 {
        font-size: 2rem;
        font-weight: bold;
        color: #343a40;
    }

    /* Card sản phẩm */
    .card {
        border: 1px solid #e0e0e0;
    }
    .card-body {
        padding: 20px;
    }
    .card-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 15px;
    }
    .card-text strong {
        color: #343a40;
    }
</style>
@endsection
