@extends('layouts.app')

@section('content')
    <!-- Breadcrumb Container đơn giản, bên trái -->
    <div class="breadcrumb-container">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}">Trang chủ</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('categories.index') }}">Danh mục</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $category->category_name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Nội dung chi tiết danh mục -->
    <div class="container my-4">
        <div class="card category-detail-card shadow-sm">
            <div class="card-body">
                <h3 class="card-title text-center mb-4">{{ $category->category_name }}</h3>
                <div class="row">
                    <div class="col-md-4">
                        @if($category->image)
                            <img src="{{ asset($category->image) }}" alt="{{ $category->category_name }}" class="img-fluid rounded">
                        @else
                            <img src="https://via.placeholder.com/300" alt="No image" class="img-fluid rounded">
                        @endif
                    </div>
                    <div class="col-md-8">
                        <div class="detail-item">
                            <strong>Mô Tả:</strong>
                            <p>{{ $category->description ?: 'Không có mô tả' }}</p>
                        </div>
                        <div class="detail-item">
                            <strong>Slug:</strong>
                            <p>{{ $category->slug }}</p>
                        </div>
                        <div class="detail-item">
                            <strong>Trạng Thái:</strong>
                            <p>
                                @if($category->status)
                                    <span class="badge bg-success">Hoạt động</span>
                                @else
                                    <span class="badge bg-danger">Ngừng hoạt động</span>
                                @endif
                            </p>
                        </div>
                        <div class="detail-item">
                            <strong>Sắp Xếp:</strong>
                            <p>{{ $category->sort_order ?? 'Không có thông tin' }}</p>
                        </div>
                        <div class="detail-item">
                            <strong>Danh Mục Cha:</strong>
                            <p>{{ $category->parent ? $category->parent->category_name : 'Không có danh mục cha' }}</p>
                        </div>

                    </div>
                </div>
                <div class="mt-4 text-end">
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">Quay Lại</a>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Breadcrumb Container */
        .breadcrumb-container {
            background-color: #f8f9fa;
            padding: 10px 20px;
            border-bottom: 1px solid #e0e0e0;
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

        /* Card Chi tiết danh mục */
        .category-detail-card {
            border: none;
            border-radius: 8px;
            overflow: hidden;
        }
        .category-detail-card .card-body {
            padding: 20px;
        }
        .card-title {
            font-size: 1.75rem;
            font-weight: bold;
            color: #333;
        }
        .detail-item {
            margin-bottom: 15px;
        }
        .detail-item strong {
            display: block;
            font-size: 1rem;
            color: #333;
            margin-bottom: 5px;
        }
        .detail-item p {
            margin: 0;
            font-size: 0.95rem;
            color: #555;
        }
    </style>
@endsection
