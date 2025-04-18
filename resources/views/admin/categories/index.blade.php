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
            <li class="breadcrumb-item active" aria-current="page">Danh sách danh mục</li>
        </ol>
    </nav>
    <!-- End Breadcrumb -->

    <!-- Header: Nút Thêm danh mục và Search -->
    <div class="header-section d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.categories.create') }}" class="btn btn-add-category mb-2">
            <i class="fas fa-plus"></i> Thêm danh mục
        </a>
        <form method="GET" action="{{ route('admin.categories.index') }}" class="mb-2 search-form">
            <div class="input-group">
                <input type="text" name="search" class="form-control search-input" placeholder="Tìm kiếm danh mục..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-secondary search-button">
                    <i class="fas fa-search me-1"></i> Tìm kiếm
                </button>
            </div>
        </form>
    </div>

    <!-- Bảng danh sách danh mục -->
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Hình ảnh</th>
                    <th>Tên danh mục</th>
                    <th>Mô tả</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <!-- Thẻ bao cho ảnh với kích thước cố định -->
                        <div class="table-img-wrapper">
                            <img src="{{ $category->image ? asset($category->image) : 'https://via.placeholder.com/100' }}" 
                                 alt="{{ $category->category_name }}">
                        </div>
                    </td>
                    <td>{{ $category->category_name }}</td>
                    <td>{{ $category->description }}</td>
                    <td>{{ $category->status ? 'Hiển thị' : 'Ẩn' }}</td>
                    <td>
                        <div class="action-icons">
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="action-btn btn btn-warning btn-sm" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn btn btn-danger btn-sm" title="Xóa"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này không?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- CSS tùy chỉnh cho trang admin -->
<style>
    /* Container chung */
    .container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }
    
    /* Breadcrumb */
    .breadcrumb {
        background-color: transparent;
        padding: 0;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }
    .breadcrumb-item a {
        color: #ff4081;
        text-decoration: none;
    }
    .breadcrumb-item a:hover {
        text-decoration: underline;
    }
    .breadcrumb-item.active {
        color: #6c757d;
    }
    
    /* Header: Nút Thêm danh mục và Search */
    .header-section {
        margin-bottom: 20px;
    }
    .btn-add-category {
        background: linear-gradient(45deg, #ff6ec4, #ff4dab);
        color: #fff;
        border: none;
        border-radius: 50px;
        padding: 10px 20px;
        font-size: 1rem;
        transition: background 0.3s ease, transform 0.2s ease;
        display: inline-flex;
        align-items: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .btn-add-category i {
        margin-right: 8px;
        font-size: 1.2rem;
    }
    .btn-add-category:hover {
        background: linear-gradient(45deg, #ff4dab, #ff6ec4);
        transform: translateY(-2px);
        text-decoration: none;
    }
    /* CSS cho tìm kiếm */
    .search-form .input-group {
        border: 1px solid #ff4dab;
        border-radius: 50px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        max-width: 400px;
    }
    .search-form .search-input {
        border: none;
        padding: 12px 15px;
    }
    .search-form .search-input:focus {
        box-shadow: none;
        outline: none;
    }
    .search-form .search-button {
        background-color: #ff4dab;
        border: none;
        color: #fff;
        padding: 12px 20px;
        transition: background 0.3s ease;
    }
    .search-form .search-button:hover {
        background-color: #0056b3;
    }
    
    /* Thẻ bao ảnh với kích thước cố định */
    .table-img-wrapper {
        width: 80px;
        height: 80px;
        overflow: hidden;
        border-radius: 4px;
        margin: auto;
    }
    .table-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .table-img-wrapper img:hover {
        transform: scale(1.1);
    }
    
    /* Cải thiện giao diện bảng */
    table.table-bordered {
        border: 1px solid #dee2e6;
    }
    table.table-bordered th,
    table.table-bordered td {
        border: 1px solid #dee2e6;
        vertical-align: middle;
        text-align: center;
    }
    table.table-bordered tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    /* Action icons */
    .action-icons {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }
    .action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        font-size: 1rem;
        padding: 0;
    }
    .action-btn i {
        margin: 0;
    }
    /* Hiệu ứng hover cho nút */
    .action-btn:hover {
        transform: translateY(-2px);
    }
</style>
@endsection
