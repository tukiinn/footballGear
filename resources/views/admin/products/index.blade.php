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
            <li class="breadcrumb-item active" aria-current="page">Danh sách sản phẩm</li>
        </ol>
    </nav>
    <!-- Thanh công cụ -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.products.create') }}" class="btn btn-add-product mb-2">
            <i class="fas fa-plus"></i> Thêm sản phẩm mới
        </a>
        <form method="GET" action="{{ route('admin.products.index') }}" class="mb-2 search-form">
            <div class="input-group">
                <input type="text" name="search" class="form-control search-input" placeholder="Tìm kiếm sản phẩm..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-secondary search-button">
                    <i class="fas fa-search me-1"></i> Tìm kiếm
                </button>
            </div>
        </form>
    </div>

    <!-- Danh sách sản phẩm -->
    <div class="row">
        @forelse ($products as $product)
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card product-card h-100 shadow-sm">
                <!-- Ảnh sản phẩm và hover icons -->
                <div class="position-relative product-image-container">
                    <img src="{{ $product->image ? asset($product->image) : 'https://via.placeholder.com/250x250' }}" 
                         class="card-img-top product-image" 
                         alt="{{ $product->product_name }}">
                    <div class="hover-icons position-absolute top-50 start-50 translate-middle d-flex gap-2">
                        <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-light btn-icon" title="Xem chi tiết">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary btn-icon" title="Chỉnh sửa">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-info btn-icon btn-stock" title="Kho hàng"
                            data-bs-toggle="modal" data-bs-target="#stockModal"
                            data-product-id="{{ $product->id }}"
                            data-product-name="{{ $product->product_name }}"
                            data-sizes='@json($product->sizes)'>
                            <i class="fas fa-warehouse"></i>
                        </button>
                        <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-icon" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <!-- Nội dung sản phẩm -->
                <div class="card-body text-center d-flex flex-column">
                    <a href="{{ route('admin.products.show', $product->id) }}" class="text-decoration-none text-dark mb-2">
                        <h6 class="card-title text-truncate">{{ $product->product_name }}</h6>
                    </a>
                    @if($product->sizes && $product->sizes->count() > 0)
                        <small class="text-muted">
                            Sizes: 
                            @foreach($product->sizes as $size)
                                {{ $size->size }} ({{ $size->quantity }})@if(!$loop->last), @endif
                            @endforeach
                        </small>
                    @endif
                    <p class="card-text mt-auto fw-bold text-danger">
                        @if($product->discount_price)
                            <del class="text-muted">{{ number_format($product->price, 0, ',', '.') }} VND</del>
                            <span>{{ number_format($product->discount_price, 0, ',', '.') }} VND</span>
                        @else
                            <span>{{ number_format($product->price, 0, ',', '.') }} VND</span>
                        @endif                    
                    </p>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center">
            <p>Không có sản phẩm nào.</p>
        </div>
        @endforelse
    </div>

    <!-- Phân trang -->
    <div class="d-flex justify-content-center">
        {{ $products->links('pagination::bootstrap-4') }}
    </div>
</div>

<!-- Modal Kho Hàng -->
<div class="modal fade" id="stockModal" tabindex="-1" aria-labelledby="stockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="stockModalLabel">Kho hàng cho sản phẩm: <span id="modalProductName"></span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
        </div>
        <div class="modal-body">
          <!-- Bảng danh sách size -->
          <div class="table-responsive">
              <table class="table table-striped" id="modalSizesTable">
                  <thead>
                      <tr>
                          <th>Size</th>
                          <th>Số lượng</th>
                          <th>Hành động</th>
                      </tr>
                  </thead>
                  <tbody>
                      <!-- Dữ liệu size sẽ được chèn vào đây qua JavaScript -->
                  </tbody>
              </table>
          </div>
          <!-- Nút Thêm size -->
          <button type="button" class="btn btn-primary" id="addSizeBtn">
            <i class="fas fa-plus"></i> Thêm size
          </button>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- JavaScript: Xử lý Modal Kho Hàng và AJAX cho các chức năng Size -->
  <script>
    let currentProductId = null; // Lưu productId của sản phẩm hiện tại
  
    // Khi modal được hiển thị, lấy thông tin sản phẩm và các size
    var stockModal = document.getElementById('stockModal');
    stockModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      currentProductId = button.getAttribute('data-product-id');
      var productName = button.getAttribute('data-product-name');
      // Sử dụng toArray() khi truyền dữ liệu nếu cần, sau đó parse JSON
      var sizesData = button.getAttribute('data-sizes');
      var sizes = [];
      try {
        sizes = JSON.parse(sizesData);
        console.log("Sizes:", sizes); // Kiểm tra xem có thuộc tính product_size_id hay không
      } catch (e) {
        console.error("Lỗi parse JSON cho sizes:", e);
      }
      
      // Cập nhật tiêu đề modal
      document.getElementById('modalProductName').textContent = productName;
      
      // Xây dựng bảng size, sử dụng size.product_size_id thay vì size.id
      var tbody = document.getElementById('modalSizesTable').getElementsByTagName('tbody')[0];
      tbody.innerHTML = "";
      if (sizes.length > 0) {
        sizes.forEach(function(size) {
          var tr = document.createElement('tr');
          tr.setAttribute('data-size-id', size.product_size_id); // Sử dụng product_size_id
          tr.innerHTML = `
            <td>${size.size}</td>
            <td class="size-quantity">${size.quantity}</td>
            <td>
              <button type="button" class="btn btn-sm btn-warning btn-edit-size" title="Sửa">
                <i class="fas fa-edit"></i>
              </button>
              <button type="button" class="btn btn-sm btn-danger btn-delete-size" title="Xóa">
                <i class="fas fa-trash-alt"></i>
              </button>
            </td>
          `;
          tbody.appendChild(tr);
        });
      } else {
        tbody.innerHTML = `<tr><td colspan="3" class="text-center">Không có size nào.</td></tr>`;
      }
    });
  
    // Sự kiện cho nút "Thêm size"
    document.getElementById('addSizeBtn').addEventListener('click', function() {
      let newSize = prompt("Nhập kích cỡ mới:");
      if (!newSize) return;
      let newQuantity = prompt("Nhập số lượng cho kích cỡ " + newSize + ":");
      if (newQuantity === null) return;
      newQuantity = parseInt(newQuantity);
      if (isNaN(newQuantity) || newQuantity < 0) {
        alert("Số lượng không hợp lệ.");
        return;
      }
      // Gửi request POST để thêm size mới
      fetch(`/admin/products/${currentProductId}/sizes`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          size: newSize,
          quantity: newQuantity
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert(data.message);
          var tbody = document.getElementById('modalSizesTable').getElementsByTagName('tbody')[0];
          // Nếu có dòng "Không có size nào" thì xoá nó
          if (tbody.querySelector('tr td[colspan="3"]')) {
            tbody.innerHTML = "";
          }
          var tr = document.createElement('tr');
          tr.setAttribute('data-size-id', data.data.product_size_id); // Dùng product_size_id từ server
          tr.innerHTML = `
            <td>${data.data.size}</td>
            <td class="size-quantity">${data.data.quantity}</td>
            <td>
              <button type="button" class="btn btn-sm btn-warning btn-edit-size" title="Sửa">
                <i class="fas fa-edit"></i>
              </button>
              <button type="button" class="btn btn-sm btn-danger btn-delete-size" title="Xóa">
                <i class="fas fa-trash-alt"></i>
              </button>
            </td>
          `;
          tbody.appendChild(tr);
        } else {
          alert(data.message || "Có lỗi xảy ra khi thêm size.");
        }
      })
      .catch(error => {
        console.error("Error:", error);
        alert("Có lỗi xảy ra khi thêm size.");
      });
    });
  
    // Xử lý sự kiện cho nút Sửa và Xóa size bằng delegation
    document.addEventListener('click', function(event) {
      // Sửa size
      if (event.target.closest('.btn-edit-size')) {
        let btn = event.target.closest('.btn-edit-size');
        let tr = btn.closest('tr');
        let sizeId = tr.getAttribute('data-size-id');
        let currentQuantity = tr.querySelector('.size-quantity').textContent;
        let newQuantity = prompt("Nhập số lượng mới:", currentQuantity);
        if (newQuantity === null) return;
        newQuantity = parseInt(newQuantity);
        if (isNaN(newQuantity) || newQuantity < 0) {
            alert("Số lượng không hợp lệ.");
            return;
        }
        fetch(`/admin/products/${currentProductId}/sizes/${sizeId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                quantity: newQuantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                tr.querySelector('.size-quantity').textContent = newQuantity;
            } else {
                alert(data.message || "Có lỗi xảy ra khi cập nhật số lượng.");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Có lỗi xảy ra khi cập nhật số lượng.");
        });
      }
  
      // Xóa size
      if (event.target.closest('.btn-delete-size')) {
        let btn = event.target.closest('.btn-delete-size');
        let tr = btn.closest('tr');
        let sizeId = tr.getAttribute('data-size-id');
        if (confirm("Bạn có chắc chắn muốn xóa size này không?")) {
            fetch(`/admin/products/${currentProductId}/sizes/${sizeId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    tr.remove();
                    var tbody = document.getElementById('modalSizesTable').getElementsByTagName('tbody')[0];
                    if (!tbody.querySelector('tr')) {
                        tbody.innerHTML = `<tr><td colspan="3" class="text-center">Không có size nào.</td></tr>`;
                    }
                } else {
                    alert(data.message || "Có lỗi xảy ra khi xóa size.");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Có lỗi xảy ra khi xóa size.");
            });
        }
      }
    });
  </script>
  
  <!-- CSS Tùy chỉnh cho Modal Kho Hàng -->
  <style>
    /* Modal Table */
    #modalSizesTable th, #modalSizesTable td {
      text-align: center;
      vertical-align: middle;
      padding: 10px;
    }
    /* Nút Icon cho Edit & Delete trong modal */
    .btn-edit-size, .btn-delete-size {
      width: 30px;
      height: 30px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border: none;
      border-radius: 50%;
      font-size: 0.9rem;
      transition: transform 0.2s ease;
      color: #fff;
    }
    .btn-edit-size {
      background-color: #007bff;
    }
    .btn-edit-size:hover {
      background-color: #0056b3;
      transform: scale(1.1);
    }
    .btn-delete-size {
      background-color: #dc3545;
    }
    .btn-delete-size:hover {
      background-color: #c82333;
      transform: scale(1.1);
    }
  </style>
  

<!-- CSS Tùy chỉnh -->
<style>
    .container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    /* Breadcrumb */
    .breadcrumb {
        background-color: transparent;
    }
    .breadcrumb-item a {
        color: #ff4081;
        text-decoration: none;
    }
    /* Nút Thêm sản phẩm mới - màu hồng */
    .btn-add-product {
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
    .btn-add-product i {
        margin-right: 8px;
        font-size: 1.2rem;
    }
    .btn-add-product:hover {
        background: linear-gradient(45deg, #ff4dab, #ff6ec4);
        transform: translateY(-2px);
        text-decoration: none;
    }
    /* CSS cho tìm kiếm */
    .search-form .input-group {
        border: 1px solid #e0e0e0;
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
        background: linear-gradient(45deg, #ff4dab, #ff6ec4);
        border: none;
        color: #fff;
        padding: 12px 20px;
        transition: background 0.3s ease;
    }
    .search-form .search-button:hover {
        background-color: #0056b3;
    }
    /* Card sản phẩm */
    .product-card {
        border: 0px solid #e0e0e0;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
    }
    .product-card:hover {
        border-color: #ff4dab;
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
    .product-image-container {
        position: relative;
        height: 220px;
        overflow: hidden;
    }
    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .product-image-container:hover .product-image {
        transform: scale(1.1);
    }
    .hover-icons {
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .product-image-container:hover .hover-icons {
        opacity: 1;
    }
    .btn-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
</style>
@endsection
