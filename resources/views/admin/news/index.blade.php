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
                <li class="breadcrumb-item active" aria-current="page">Tin Tức</li>
            </ol>
        </nav>
        <!-- End Breadcrumb -->

        <!-- Header: Nút Thêm bài viết và Form tìm kiếm -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <a href="{{ route('admin.news.create') }}" class="btn btn-add-news">
                <i class="fas fa-plus"></i> Thêm bài viết
            </a>
            <form method="GET" action="{{ route('admin.news.index') }}" class="mb-2">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm bài viết..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-search">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                </div>
            </form>
        </div>

        <!-- Thông báo thành công -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Bảng danh sách tin tức -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>STT</th>
                        <th>Tiêu đề</th>
                        <th>Slug</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($news as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->title }}</td>
                            <td>{{ $item->slug }}</td>
                            <td>
                                <a href="{{ route('admin.news.edit', $item->id) }}" class="btn btn-icon btn-edit" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.news.destroy', $item->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
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

        <!-- Phân trang -->
        <div class="d-flex justify-content-center mt-3">
            {{ $news->links() }}
        </div>
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

        /* Header: Nút thêm và tìm kiếm */
        .d-flex.justify-content-between {
            margin-bottom: 20px;
        }
        .btn-add-news {
            background: linear-gradient(45deg, #ff6ec4, #ff4dab);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 12px 25px;
            font-size: 1rem;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .btn-add-news:hover {
            background: linear-gradient(45deg, #ff4dab, #ff6ec4);
            transform: translateY(-2px);
            text-decoration: none;
        }

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
        .btn-search {
            padding: 12px 20px;
            background-color: #ff4dab;
            border: none;
            color: #fff;
            transition: background 0.3s ease;
        }
        .btn-search:hover {
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
