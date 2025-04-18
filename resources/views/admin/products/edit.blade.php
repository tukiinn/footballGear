@extends('layouts.admin')

@section('content')
<div class="container p-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i></a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.products.index') }}">Sản Phẩm</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Chỉnh Sửa Sản Phẩm</li>
        </ol>
    </nav>

    <!-- Tiêu đề trang -->
    <h2 class="mb-4 text-center">Chỉnh Sửa Sản Phẩm</h2>

    <!-- Form Chỉnh Sửa Sản Phẩm -->
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Tên Sản Phẩm -->
        <div class="mb-3">
            <label for="product_name" class="form-label">Tên Sản Phẩm</label>
            <input type="text" class="form-control @error('product_name') is-invalid @enderror" id="product_name" name="product_name" value="{{ old('product_name', $product->product_name) }}" required>
            @error('product_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Mô Tả -->
        <div class="mb-3">
            <label for="description" class="form-label">Mô Tả</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description', $product->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="detail" class="form-label">Chi tiết sản phẩm</label>
            <textarea class="form-control" name="detail" id="detail" rows="5" placeholder="Soạn nội dung bài viết...">{{ old('detail',$product->detail) }}</textarea>
        </div>
        <!-- Ảnh -->
        <div class="mb-3">
            <label for="image" class="form-label">Ảnh</label>
            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
            @if($product->image)
                <img src="{{ asset($product->image) }}" alt="{{ $product->product_name }}" class="img-thumbnail mt-2" style="max-width: 150px;">
            @endif
            @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Giá -->
        <div class="mb-3">
            <label for="price" class="form-label">Giá</label>
            <input type="number" step="1" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', intval($product->price)) }}" required>
            @error('price')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Giá Giảm Giá -->
        <div class="mb-3">
            <label for="discount_price" class="form-label">Giá Giảm Giá</label>
            <input type="number" step="1" class="form-control @error('discount_price') is-invalid @enderror" id="discount_price" name="discount_price" value="{{ old('discount_price', intval($product->discount_price)) }}">
            @error('discount_price')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Danh Mục -->
        <div class="mb-3">
            <label for="category_id" class="form-label">Danh Mục</label>
            <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                <option value="">Chọn danh mục</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->category_name }}</option>
                @endforeach
            </select>
            @error('category_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Slug -->
        <div class="mb-3">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $product->slug) }}" required>
            @error('slug')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Trạng Thái -->
        <div class="mb-3">
            <label for="status" class="form-label">Trạng Thái</label>
            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                <option value="1" {{ old('status', $product->status) == '1' ? 'selected' : '' }}>Hoạt động</option>
                <option value="0" {{ old('status', $product->status) == '0' ? 'selected' : '' }}>Ngừng hoạt động</option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Nổi Bật -->
        <div class="mb-3">
            <label for="featured" class="form-label">Nổi Bật</label>
            <select class="form-control @error('featured') is-invalid @enderror" id="featured" name="featured">
                <option value="0" {{ old('featured', $product->featured) == '0' ? 'selected' : '' }}>Không</option>
                <option value="1" {{ old('featured', $product->featured) == '1' ? 'selected' : '' }}>Có</option>
            </select>
            @error('featured')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Kích Cỡ & Số Lượng (Quản lý qua product_sizes) -->
        <div class="mb-3">
            <label class="form-label">Kích Cỡ &amp; Số Lượng</label>
            <table class="table table-bordered" id="sizes-table">
                <thead>
                    <tr>
                        <th>Kích cỡ</th>
                        <th>Số lượng</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @if(old('sizes'))
                        @foreach(old('sizes') as $index => $sizeData)
                        <tr>
                            <td>
                                <input type="text" name="sizes[{{ $index }}][size]" class="form-control" value="{{ $sizeData['size'] }}" required>
                            </td>
                            <td>
                                <input type="number" name="sizes[{{ $index }}][quantity]" class="form-control" value="{{ $sizeData['quantity'] }}" required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-size">Xóa</button>
                            </td>
                        </tr>
                        @endforeach
                    @elseif($product->sizes && $product->sizes->count() > 0)
                        @foreach($product->sizes as $index => $size)
                        <tr>
                            <td>
                                <input type="text" name="sizes[{{ $index }}][size]" class="form-control" value="{{ $size->size }}" required>
                            </td>
                            <td>
                                <input type="number" name="sizes[{{ $index }}][quantity]" class="form-control" value="{{ $size->quantity }}" required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-size">Xóa</button>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>
                                <input type="text" name="sizes[0][size]" class="form-control" required>
                            </td>
                            <td>
                                <input type="number" name="sizes[0][quantity]" class="form-control" required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-size">Xóa</button>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <button type="button" class="btn btn-primary btn-sm" id="add-size">Thêm kích cỡ</button>
        </div>

        <!-- Nút Submit -->
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Cập Nhật Sản Phẩm</button>
        </div>
    </form>
</div>
<script src="https://cdn.ckeditor.com/4.22.0/full/ckeditor.js"></script>
<script>
    CKEDITOR.replace('detail', {
        // Nếu bạn muốn hỗ trợ upload ảnh, cấu hình thêm filebrowserUploadUrl:
        filebrowserUploadUrl: "{{ route('upload', ['_token' => csrf_token()]) }}",
        filebrowserUploadMethod: 'form'
    });
</script>
<!-- CSS Tùy Chỉnh -->
<style>
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
    h2 {
        font-size: 1.75rem;
        font-weight: 600;
        color: #343a40;
    }
    /* Form label */
    .form-label {
        font-weight: 500;
    }
    /* Nút Submit */
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }
    .btn-primary:hover {
        background-color: #0069d9;
        border-color: #0062cc;
    }
</style>

<!-- JS xử lý thêm/xóa dòng kích cỡ -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    let sizesTableBody = document.querySelector('#sizes-table tbody');
    let addSizeButton = document.getElementById('add-size');
    
    addSizeButton.addEventListener('click', function () {
        let index = sizesTableBody.querySelectorAll('tr').length;
        let newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <input type="text" name="sizes[${index}][size]" class="form-control" required>
            </td>
            <td>
                <input type="number" name="sizes[${index}][quantity]" class="form-control" required>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-size">Xóa</button>
            </td>
        `;
        sizesTableBody.appendChild(newRow);
    });

    document.addEventListener('click', function(e) {
        if(e.target && e.target.classList.contains('remove-size')) {
            let row = e.target.closest('tr');
            row.remove();
        }
    });
});
</script>
@endsection
