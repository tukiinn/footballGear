<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


    <style>
/* Nếu form và kết quả live search được bọc trong container có position: relative */
#admin-live-search-results {
  position: absolute;       /* Hiển thị "đè lên" nội dung bên dưới */
  top: calc(100% + 5px);     /* Cách form 5px, điều chỉnh tùy nhu cầu */
  left: 30%;
  width: 600px;
  background-color: #fff;
  border: 1px solid #ddd;
  border-top: none;
  z-index: 1000;
  max-height: 300px;
  overflow-y: auto;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
  padding: 10px;
  display: none;            /* Ẩn mặc định */
}
#admin-live-search-results.show {
      display: block;
    }
    .admin-live-search-item {
      padding: 5px;
      border-bottom: 1px solid #eee;
      display: flex;
      align-items: center;
    }
    .admin-live-search-item:last-child {
      border-bottom: none;
    }
    .admin-live-search-item a {
      color: #333;
      text-decoration: none;
      display: flex;
      width: 100%;
    }
    .live-search-thumb {
      width: 60px;
      height: 60px;
      object-fit: cover;
      margin-right: 10px;
      border-radius: 4px;
    }
    .group-title {
      margin: 5px 0;
      font-size: 14px;
      color: #007bff;
      border-bottom: 1px solid #ddd;
      padding-bottom: 3px;
    }
    .product-info, .order-info {
      font-size: 14px;
      line-height: 1.4;
    }
/* ==== Navbar ==== */
.navbar-brand img { 
    height: 70px;
    width: auto; /* Giữ tỷ lệ logo */
}

/* CSS override cho logo */
.sidebar a.logo:hover { 
    background-color: transparent !important;
}

.navbar-first { 
    background-color: #212529; 
    padding: 30px 40px; /* Tăng padding để làm thanh navbar lớn hơn */
}

.navbar .btn-outline-light:hover {
    background-color: #f8f9fa;
    color: #212529;
}

/* ==== Sidebar ==== */
/* Trạng thái ban đầu: sidebar thu gọn chỉ hiển thị icon */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 60px; /* Chỉ hiển thị icon */
    background-color: #212529;
    padding-top: 20px;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
    transition: width 0.3s ease, background-color 0.3s ease; /* Thêm hiệu ứng mượt mà */
    overflow-y: auto;
}

/* Mở rộng sidebar khi di chuột vào */
.sidebar:hover {
    width: 260px;

}

/* Định dạng cho các link của sidebar */
.sidebar a {
    color: #ffffff;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    font-size: 16px;
    text-decoration: none;
    transition: background 0.3s, padding-left 0.3s, color 0.3s; /* Thêm hiệu ứng mượt mà cho màu chữ */
}

/* Khoảng cách giữa icon và text */
.sidebar a i {
    margin-right: 12px;
}

/* Hiệu ứng hover cho các mục */
.sidebar a:hover {
    background-color: #495057;
    padding-left: 25px;
    color: #ffc107; /* Thêm hiệu ứng mượt mà cho màu chữ */
}

/* Ẩn text khi sidebar thu gọn */
.sidebar a .menu-text {
    display: none;
    opacity: 0;
    transition: opacity 0.3s ease, visibility 0.3s ease; /* Thêm hiệu ứng mượt mà */
    white-space: nowrap; /* Ngăn chặn văn bản xuống dòng */
}

/* Hiển thị text khi sidebar mở rộng */
.sidebar:hover a .menu-text {
    display: inline;
    opacity: 1;
    margin-left: 10px;
}



/* ==== Nội dung chính ==== */
/* Ban đầu, nội dung được đẩy theo chiều rộng của sidebar thu gọn */
.content {
    margin-left: 60px;
    padding: 20px;
    width: calc(100% - 60px);
    transition: margin-left 0.3s ease, width 0.3s ease;
}

/* Điều chỉnh nội dung khi sidebar mở rộng */
/* Lưu ý: Điều này áp dụng khi sidebar và content là các phần tử anh em trong cùng 1 container */
.sidebar:hover ~ .content {
    margin-left: 260px;
    width: calc(100% - 260px);
}

/* Nội dung full-width khi sidebar ẩn */
.content-full {
    margin-left: 0;
    width: 100%;
}

/* ==== Responsive ==== */
@media (max-width: 768px) {
    .sidebar {
        width: 60px;
    }
    .sidebar:hover {
        width: 260px;
    }
    .sidebar a {
        text-align: center;
        padding-left: 0;
    }
    .sidebar a i {
        margin-right: 0;
    }
    .content {
        margin-left: 60px;
        width: calc(100% - 60px);
    }
    .sidebar:hover ~ .content {
        margin-left: 260px;
        width: calc(100% - 260px);
    }
    .content-full {
        margin-left: 0;
        width: 100%;
    }
}

    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-first">
        <div class="container-fluid d-flex align-items-center">
 
<!-- Form tìm kiếm -->
<div class="d-flex flex-grow-1 justify-content-center mx-3 search-container">
    <select id="admin-data-type-select" class="form-select me-2" style="max-width: 150px;">
        <option value="all">Tất cả</option>
        <option value="categories">Danh mục</option>
        <option value="products">Sản phẩm</option>
        <option value="orders">Đơn hàng</option>
        <option value="vouchers">Voucher</option>
        <option value="news">Bài viết</option>
      </select>
      <input type="search" id="admin-live-search-input" class="form-control" placeholder="Nhập từ khóa tìm kiếm..." aria-label="Search" style="max-width: 400px;">
    <!-- Nút submit vẫn có thể dùng nhưng live search sẽ tự động gọi -->
    <button class="btn btn-outline-light ms-2" type="submit">Tìm kiếm</button>
  </div>
  
  <div id="admin-live-search-results"></div>
     
            <div class="d-flex align-items-center">
                @if(auth()->check())
                    <span class="text-white me-3">Xin chào, {{ auth()->user()->name }}!</span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary">Đăng xuất</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary me-2" title="Đăng ký/Đăng nhập">
                        <i class="fa-solid fa-user"></i>
                    </a>
                @endif
            </div>
        </div>
    </nav>

   <!-- Sidebar -->
<div class="d-flex">
    <div class="sidebar" id="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="navbar-brand logo ms-5">
            <img src="/images/logo/hieustorelogo.png" alt="Logo">
        </a>

        <div class="sidebar-content">
            <a href="{{ route('admin.dashboard') }}">
                <i class="fa-solid fa-tachometer-alt"></i>
                <span class="menu-text">Dashboard</span>
            </a>
            <a href="{{ route('admin.categories.index') }}">
                <i class="fa-solid fa-list"></i>
                <span class="menu-text">Danh mục sản phẩm</span>
            </a>
            <a href="{{ route('admin.products.index') }}">
                <i class="fa-solid fa-box"></i>
                <span class="menu-text">Sản phẩm</span>
            </a>
            <a href="{{ route('admin.orders.index') }}">
                <i class="fa-solid fa-shopping-cart"></i>
                <span class="menu-text">Đơn hàng</span>
            </a>
            <a href="{{ route('admin.news.index') }}">
                <i class="fa-solid fa-newspaper"></i>
                <span class="menu-text">Tin tức</span>
            </a>
            <a href="{{ route('admin.vouchers.index') }}">
                <i class="fas fa-ticket-alt"></i>
                <span class="menu-text">Voucher</span>
            </a>

 
        </div>
    </div>

    <!-- Main content -->
    <div class="content" id="mainContent">
        <main class="py-4">
            @yield('content')
        </main>
    </div>
</div>


@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show alert-modern" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show alert-modern" role="alert">
        {!! session('error') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<style>
  /* Flash message modern style - hiển thị ở đầu trang */
  .alert-modern {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    width: auto;
    max-width: 90%;
    z-index: 1050;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    font-weight: 500;
    /* Tăng padding bên phải để dành chỗ cho nút close */
    padding: 15px 50px 15px 20px;
    margin: 0;
    border: none;
    animation: slideDown 0.5s ease-out;
  }
  @keyframes slideDown {
    from {
      transform: translate(-50%, -100%);
      opacity: 0;
    }
    to {
      transform: translate(-50%, 0);
      opacity: 1;
    }
  }
  .alert-modern.alert-success {
    background: linear-gradient(90deg, #28a745, #218838);
    color: #fff;
  }
  .alert-modern.alert-danger {
    background: linear-gradient(90deg, #dc3545, #c82333);
    color: #fff;
  }
  /* Định vị lại nút close để không đè lên chữ */
  .alert-modern .btn-close {
    position: absolute;
    top: 50%;
    right: 15px;
    transform: translateY(-50%);
    filter: invert(1);
    opacity: 0.8;
    transition: opacity 0.3s ease;
  }
  .alert-modern .btn-close:hover {
    opacity: 1;
  }
</style>

    
    <!-- Bootstrap JS -->

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Hàm debounce để hạn chế gửi quá nhiều request khi gõ liên tục
    function debounce(func, delay) {
      let timer;
      return function(...args) {
        clearTimeout(timer);
        timer = setTimeout(() => func.apply(this, args), delay);
      };
    }
  
    document.addEventListener("DOMContentLoaded", function() {
      const searchInput = document.getElementById("admin-live-search-input");
      const resultsContainer = document.getElementById("admin-live-search-results");
      const dataTypeSelect = document.getElementById("admin-data-type-select");
  
      if (!searchInput || !resultsContainer) {
        console.error("Các phần tử tìm kiếm không tồn tại trên trang này.");
        return;
      }
  
      // URL endpoint cho live search admin (ở đây dùng GET)
      const routeUrl = "/admin/search";
  
      // Hàm xử lý chuyển đổi đường dẫn ảnh thành dạng tuyệt đối nếu cần
      function getAbsoluteImageUrl(url) {
        if (!url) return "";
        // Nếu URL đã bắt đầu bằng "/" hoặc "http", trả về nguyên URL
        if (url.startsWith("/") || url.startsWith("http")) {
          return url;
        }
        // Nếu không, thêm dấu "/" ở đầu
        return "/" + url;
      }
  
      searchInput.addEventListener("input", debounce(function() {
        const query = this.value.trim();
        const dataType = dataTypeSelect ? dataTypeSelect.value : "all";
  
        if (query.length < 2) {
          resultsContainer.classList.remove("show");
          setTimeout(() => {
            resultsContainer.innerHTML = "";
          }, 300);
          return;
        }
  
        // Gửi request GET với query và dataType
        fetch(routeUrl + "?query=" + encodeURIComponent(query) + "&dataType=" + dataType)
          .then(response => response.json())
          .then(data => {
            resultsContainer.innerHTML = "";
            let hasResults = false;
  
            for (const group in data) {
              if (data.hasOwnProperty(group) && data[group].length > 0) {
                hasResults = true;
                resultsContainer.innerHTML += `<h5 class="group-title">${group.toUpperCase()}</h5>`;
                data[group].forEach(item => {
                  let html = "";
                  if (group === "categories") {
                    // Hiển thị danh mục với ảnh (nếu có)
                    html = `
                      <div class="admin-live-search-item">
                        <a href="/admin/categories/${item.id}">
                          ${item.image ? `<img src="${getAbsoluteImageUrl(item.image)}" alt="${item.category_name}" class="live-search-thumb">` : ""}
                          <div class="product-info">
                            <strong>${item.category_name}</strong>
                            ${item.description ? " - " + item.description : ""}
                          </div>
                        </a>
                      </div>`;
                  } else if (group === "products") {
                    // Hiển thị sản phẩm với ảnh, tên, giá thường và giá giảm (nếu có)
                    let normalPrice = parseInt(item.price).toLocaleString('vi-VN');
                    if (item.discount_price) {
                      let discountPrice = parseInt(item.discount_price).toLocaleString('vi-VN');
                      html = `
                        <div class="admin-live-search-item">
                          <a href="/admin/products/${item.id}">
                            ${item.image ? `<img src="${getAbsoluteImageUrl(item.image)}" alt="${item.product_name}" class="live-search-thumb">` : ""}
                            <div class="product-info">
                              <strong>${item.product_name}</strong><br>
                              <span><del>${normalPrice} VND</del></span>
            <span class="discount-price" style="color: red;">${discountPrice} VND</span>
                            </div>
                          </a>
                        </div>`;
                    } else {
                      html = `
                        <div class="admin-live-search-item">
                          <a href="/admin/products/${item.id}">
                            ${item.image ? `<img src="${getAbsoluteImageUrl(item.image)}" alt="${item.product_name}" class="live-search-thumb">` : ""}
                            <div class="product-info">
                              <strong>${item.product_name}</strong><br>
                            Giá: <span class="normal-price" style="color: red;">${normalPrice} VND</span>
                            </div>
                          </a>
                        </div>`;
                    }
                  }else if (group === "orders") {
  // Định dạng ngày đặt
  const createdDate = new Date(item.created_at).toLocaleDateString('vi-VN');
  // Định dạng tổng tiền: loại bỏ phần thập phân và thêm phân cách hàng nghìn
  const total = parseInt(item.tong_tien).toLocaleString('vi-VN');
  html = `
    <div class="admin-live-search-item">
      <a href="/admin/orders/${item.id}">
        <div class="order-info">
          <strong>Mã đơn hàng: ${item.order_number || item.id}</strong><br>
          Khách hàng: ${item.ten_khach_hang}<br>
          Ngày đặt: ${createdDate}<br>
          Tổng tiền: ${total} VND
        </div>
      </a>
    </div>`;
}
 else if (group === "vouchers") {
                    // Xử lý thông tin giảm giá
                    let discountInfo = "";
                    if (item.type === 'fixed') {
                      discountInfo = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(item.discount);
                    } else if (item.type === 'percentage') {
                      discountInfo = item.discount + '%';
                    }
                    html = `
                      <div class="admin-live-search-item">
                        <a href="/admin/vouchers/${item.id}">
                          <div class="product-info">
                            <strong>${item.code}</strong>
                            ${discountInfo ? " (Giảm: " + discountInfo + ")" : ""}
                          </div>
                        </a>
                      </div>`;
                  } else if (group === "news") {
                    // Hiển thị bài viết với ảnh (nếu có)
                    html = `
                      <div class="admin-live-search-item">
                        <a href="/admin/news/${item.slug}">
                          ${item.image ? `<img src="${getAbsoluteImageUrl(item.image)}" alt="${item.title}" class="live-search-thumb">` : ""}
                          <div class="product-info">
                            <strong>${item.title}</strong>
                          </div>
                        </a>
                      </div>`;
                  }
                  resultsContainer.innerHTML += html;
                });
              }
            }
  
            if (!hasResults) {
              resultsContainer.innerHTML = `<p class="text-center p-2">Không có kết quả nào!</p>`;
            }
            resultsContainer.classList.add("show");
          })
          .catch(error => console.error("Error:", error));
      }, 300));
  
      document.addEventListener("click", function(event) {
        if (!searchInput.contains(event.target) && !resultsContainer.contains(event.target)) {
          resultsContainer.classList.remove("show");
        }
      });
    });
  </script>
  

<script>
    // Tự động đóng alert sau 5 giây
    setTimeout(function() {
      document.querySelectorAll('.alert').forEach(function(alertEl) {
        var alertInstance = new bootstrap.Alert(alertEl);
        alertInstance.close();
      });
    }, 5000);
  </script>
</body>
</html>
