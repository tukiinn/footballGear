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
                    <li class="breadcrumb-item active" aria-current="page">Danh sách danh mục</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row">
            @foreach ($categories as $category)
                <div class="col-md-4 mb-4">
                    <div class="card category-card border-light shadow-lg">
                        <!-- Bọc ảnh trong thẻ div có kích thước cố định -->
                        <div class="card-img-wrapper">
                            <img src="{{ $category->image ? asset($category->image) : 'https://via.placeholder.com/300' }}" 
                                 alt="{{ $category->category_name }}" class="card-img-top">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title text-center">{{ $category->category_name }}</h5>
                            <p class="card-text">{{ Str::limit($category->description, 150) }}</p>
                            <div class="d-flex justify-content-between">
                                <span class="badge badge-info">Trạng thái: {{ $category->status ? 'Hiển thị' : 'Ẩn' }}</span>
                                <a href="{{ route('categories.show', $category->id) }}" class="btn btn-white-hover btn-sm">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
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
        
 


        /* Card Styles */
        .category-card {
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .category-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        .card-img-wrapper {
            width: 100%;
            height: 250px;
            overflow: hidden;
        }
        .card-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .card-img-wrapper img:hover {
            transform: scale(1.05);
        }
        .card-body {
            padding: 15px;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        .card-text {
            font-size: 0.95rem;
            color: #555;
            margin-bottom: 15px;
        }
        .badge-info {
            font-size: 0.9rem;
        }
        
        /* Custom Button: White background, hover to #ff4081 */
        .btn-white-hover {
            background-color: #fff;
            color: #ff4081;
            border: 1px solid #ff4081;
            transition: background-color 0.3s ease, color 0.3s ease, transform 0.3s ease;
        }
        .btn-white-hover:hover {
            background-color: #ff4081;
            color: #fff;
            transform: translateY(-3px);
        }
        
        /* Responsive */
        @media (max-width: 767px) {
            .card-img-wrapper {
                height: 200px;
            }
        }
    </style>
@endsection
