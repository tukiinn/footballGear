@extends('layouts.app')

@section('content')
<div class="breadcrumb-container">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Trang chủ</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Danh sách Sản phẩm</li>
            </ol>
        </nav>
    </div>
</div>
  <div class="container my-4">
      <div class="row">
     <!-- LEFT: Bộ lọc (Filter Sidebar) -->
<style>
    /* Container filter sidebar */
    .shop-filter-container {
      background: #2c2f33; /* Nền tối, gần với xám đen */
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      margin-bottom: 1.5rem;
      overflow: hidden;
    }
    
    /* Header của filter */
    .shop-filter-header {
      background: #23272a;
      color: #fff;
      padding: 12px 20px;
      border-bottom: 1px solid #444;
      font-size: 1.1rem;
      font-weight: 600;
    }
    
    /* Body của filter */
    .shop-filter-body {
      background: #2c2f33;
      color: #fff;
      padding: 20px;
    }
    
    /* Label */
    .filter-label {
      display: block;
      margin-bottom: 5px;
      font-size: 0.95rem;
      color: #ccc;
    }
    
    /* Select box */
    .filter-select {
      width: 100%;
      background: #40444b;
      color: #fff;
      border: none;
      padding: 8px 10px;
      border-radius: 4px;
      margin-bottom: 15px;
      transition: background 0.3s ease;
    }
    .filter-select:focus {
      outline: none;
      background: #4e545b;
    }
    
    /* Price slider container */
    #price_slider {
      margin-bottom: 15px;
    }
    
    /* Button lọc sản phẩm */
    .btn-primary {
      background: #7289da;
      border: none;
      border-radius: 4px;
      padding: 10px 15px;
      font-weight: bold;
      transition: background 0.3s ease;
    }
    .btn-primary:hover {
      background: #5b6eae;
    }
    .category-filter {
  margin-bottom: 1rem;
}

.category-filter h3 {
  color: var(--text-color);
  margin-bottom: 0.5rem;
  font-size: 1.2rem;
}

.category-list {
  list-style: none;
  padding: 0;
  
  margin: 0;
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.category-list li {
  margin: 0;
}

.category-list a {
  color: var(--text-color);
  text-decoration: none;
  padding: 5px 10px;
  border: 1px solid rgba(255, 255, 255, 0.3); /* Viền trắng mờ */
  border-radius: 4px;
  transition: all 0.3s ease;
}

.category-list a:hover,
.category-list a.active {
  color: var(--primary-color);
  border-color: var(--primary-color);
}

  </style>
  
  <div class="col-md-3">
    <div class="shop-filter-container mt-2">
      <div class="shop-filter-header">
        Bộ lọc
      </div>
      <div class="shop-filter-body">
        <form method="GET" action="{{ route('products.index') }}" id="filterForm">
          <!-- Lọc theo danh mục -->
          <div class="category-filter">
            <h3 class="mb-3">Danh mục</h3>
            <!-- Input ẩn để lưu giá trị danh mục -->
            <input type="hidden" name="category" id="category_input" value="{{ request('category') }}">
            <ul class="category-list">
              <li>
                <a href="#" class="category-link {{ request('category') == '' ? 'active' : '' }}" data-id="">
                  Tất cả
                </a>
              </li>
              @foreach($categories->sortBy('sort_order') as $category)
                <li>
                  <a href="#" class="category-link {{ request('category') == $category->id ? 'active' : '' }}" data-id="{{ $category->id }}">
                    {{ $category->category_name }} ({{ $category->total_products }})
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
  
          <!-- Lọc theo khoảng giá -->
          <div class="mb-3">
            <label for="price_range" class="filter-label">Chọn khoảng giá:</label>
            <div id="price_slider"></div>
            <!-- Các input ẩn để gửi giá trị -->
            <input type="hidden" name="min_price" id="min_price" value="{{ request('min_price', 0) }}">
            <input type="hidden" name="max_price" id="max_price" value="{{ request('max_price', 5000000) }}">
            <p class="mt-2">
              <span id="price_range_display"></span>
            </p>
          </div>
  
          <!-- Lọc theo tình trạng -->
          <div class="mb-3">
            <label for="condition" class="filter-label">Tình trạng</label>
            <select name="condition" id="condition" class="filter-select">
              <option value="">Tất cả</option>
              <option value="sale" {{ request('condition') == 'sale' ? 'selected' : '' }}>Sale</option>
              <option value="featured" {{ request('condition') == 'featured' ? 'selected' : '' }}>Trending</option>
              <option value="in-stock" {{ request('condition') == 'in-stock' ? 'selected' : '' }}>Còn hàng</option>
            </select>
          </div>
          <!-- Bỏ nút submit vì form sẽ tự gửi khi thay đổi -->
        </form>
      </div>
    </div>
  </div>
  
  <!-- JavaScript tự động submit khi thay đổi điều kiện lọc -->
  <script>
    $(function() {
      // Khởi tạo giá trị cho slider
      var minPrice = parseFloat($("#min_price").val()) || 0;
      var maxPrice = parseFloat($("#max_price").val()) || 5000000;
  
      $("#price_slider").slider({
        range: true,
        min: 0,
        max: 5000000,
        values: [minPrice, maxPrice],
        slide: function(event, ui) {
          $("#price_range_display").text(ui.values[0].toLocaleString('vi-VN') + "₫ - " + ui.values[1].toLocaleString('vi-VN') + "₫");
        },
        stop: function(event, ui) {
          $("#min_price").val(ui.values[0]);
          $("#max_price").val(ui.values[1]);
          $("#filterForm").submit();
        }
      });
  
      $("#price_range_display").text(minPrice.toLocaleString('vi-VN') + "₫ - " + maxPrice.toLocaleString('vi-VN') + "₫");
  
      // Auto-submit khi thay đổi tình trạng
      $("#condition").on("change", function() {
        $("#filterForm").submit();
      });
  
      // Auto-submit khi chọn danh mục
      $(".category-link").on("click", function(e) {
        e.preventDefault();
        // Xóa class active của tất cả các link danh mục
        $(".category-link").removeClass("active");
        // Thêm class active cho link được click
        $(this).addClass("active");
        // Cập nhật giá trị vào input ẩn
        $("#category_input").val($(this).data("id"));
        // Gửi form
        $("#filterForm").submit();
      });
    });
  </script>
  
  

          <!-- RIGHT: Danh sách sản phẩm và sắp xếp -->
          <div class="col-md-9">
              <!-- Sort Dropdown -->
              <style>/* Button Sort Dropdown */
                .shop-sort-dropdown .btn {
                    background-color: #1e1e1e; /* Nền tối */
                    color: #fff;             /* Chữ trắng */
                    border: 1px solid #444;  /* Viền mờ */
                    border-radius: 4px;
                    padding: 0.5rem 1rem;
                    font-weight: 500;
                    transition: background-color 0.3s ease, transform 0.2s ease;
                }
                
                .shop-sort-dropdown .btn:hover {
                    background-color: #333;  /* Sáng hơn khi hover */
                    transform: translateY(-1px); /* Hiệu ứng nhô nhẹ */
                }
                
                /* Dropdown Menu */
                .shop-sort-dropdown .dropdown-menu {
                    background-color: #1e1e1e;
                    border: 1px solid #444;
                    border-radius: 4px;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                }
                
                /* Các item trong Dropdown */
                .shop-sort-dropdown .dropdown-menu li a.dropdown-item {
                    color: #ccc;  /* Màu chữ sáng nhưng không quá nổi */
                    padding: 0.5rem 1rem;
                    transition: background-color 0.3s ease, color 0.3s ease;
                }
                
                .shop-sort-dropdown .dropdown-menu li a.dropdown-item:hover,
                .shop-sort-dropdown .dropdown-menu li a.dropdown-item.active {
                    background-color: #ff4081; /* Màu điểm nổi bật */
                    color: #fff;
                }
                </style>
              <div class="d-flex justify-content-end align-items-center mb-3">
                  @php
                      $currentSort = request('sort') ?? '';
                      switch ($currentSort) {
                          case 'featured':
                              $sortText = 'Phổ biến';
                              break;
                          case 'newest':
                              $sortText = 'Mới nhất';
                              break;
                          case 'price_asc':
                              $sortText = 'Giá: Tăng dần';
                              break;
                          case 'price_desc':
                              $sortText = 'Giá: Giảm dần';
                              break;
                          default:
                              $sortText = 'Mặc định';
                              break;
                      }
                  @endphp
                  <div class="dropdown shop-sort-dropdown">
                      <button class="btn btn-secondary dropdown-toggle" type="button" id="sortDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
                          Sắp xếp: {{ $sortText }}
                      </button>
                      <ul class="dropdown-menu" aria-labelledby="sortDropdownButton">
                          <li>
                              <a class="dropdown-item {{ $currentSort == '' ? 'active' : '' }}" 
                                 href="{{ route('products.index', array_merge(request()->all(), ['sort' => ''])) }}">
                                  Mặc định
                              </a>
                          </li>
                          <li>
                              <a class="dropdown-item {{ $currentSort == 'featured' ? 'active' : '' }}" 
                                 href="{{ route('products.index', array_merge(request()->all(), ['sort' => 'featured'])) }}">
                                  Phổ biến
                              </a>
                          </li>
                          <li>
                              <a class="dropdown-item {{ $currentSort == 'newest' ? 'active' : '' }}" 
                                 href="{{ route('products.index', array_merge(request()->all(), ['sort' => 'newest'])) }}">
                                  Mới nhất
                              </a>
                          </li>
                          <li>
                              <a class="dropdown-item {{ $currentSort == 'price_asc' ? 'active' : '' }}" 
                                 href="{{ route('products.index', array_merge(request()->all(), ['sort' => 'price_asc'])) }}">
                                  Giá: Tăng dần
                              </a>
                          </li>
                          <li>
                              <a class="dropdown-item {{ $currentSort == 'price_desc' ? 'active' : '' }}" 
                                 href="{{ route('products.index', array_merge(request()->all(), ['sort' => 'price_desc'])) }}">
                                  Giá: Giảm dần
                              </a>
                          </li>
                      </ul>
                  </div>
              </div>

              <div class="row">
                @if($products->isEmpty())
                    <div class="col-12 text-center text-white">
                        <p>Chưa có sản phẩm nào</p>
                    </div>
                @else
                    @foreach ($products as $product)
                        @php
                            $basePrice = $product->discount_price ?? $product->price;
                            $isOutOfStock = $product->sizes->sum('quantity') === 0;
                        @endphp
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="product-item position-relative {{ $isOutOfStock ? 'out-of-stock' : '' }}">
                                <!-- Hình sản phẩm và badge -->
                                <div class="position-relative product-image-container">
                                    <img src="{{ $product->image ? asset($product->image) : 'https://via.placeholder.com/250x250' }}" class="product-image" alt="{{ $product->product_name }}">
                                    
                                    <!-- Hiển thị badge Hết hàng nếu sản phẩm hết hàng -->
                                    @if ($isOutOfStock)
                                        <div class="out-of-stock-badge position-absolute bg-dark text-white">
                                            Hết hàng
                                        </div>
                                    @elseif ($product->discount_price)
                                        <!-- Hiển thị badge giảm giá nếu còn hàng -->
                                        <div class="discount-badge position-absolute bg-danger text-white">
                                            -{{ round((($product->price - $product->discount_price) / $product->price) * 100) }}%
                                        </div>
                                    @endif
                                </div>
            
                                <!-- Thông tin sản phẩm -->
                                <div class="product-info text-start">
                                    <a href="{{ route('products.show', $product->id) }}" class="product-title-link">
                                        <h6 class="product-title text-truncate mb-2">{{ $product->product_name }}</h6>
                                    </a>
                                    <p class="product-price-text mb-2">
                                        @if ($product->discount_price)
                                            <span class="original-price text-muted text-decoration-line-through">
                                                {{ number_format($product->price, 0, ',', '.') }}₫
                                            </span>
                                        @endif
                                        <span class="fw-bold product-price" id="product-price-{{ $product->id }}">
                                            {{ number_format($basePrice, 0, ',', '.') }}₫
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            
            <style>
            /* Làm mờ card khi hết hàng */
            .out-of-stock {
                opacity: 0.6;
                pointer-events: none;
            }
            
            /* Badge "Hết hàng" giữ nguyên vị trí */
            .out-of-stock-badge {
                top: 10px;
                left: 10px;
                padding: 5px 10px;
                font-size: 0.9rem;
                font-weight: bold;
                border-radius: 5px;
            }
            
            /* Badge giảm giá giữ nguyên vị trí */
            .discount-badge {
                top: 10px;
                left: 10px;
                padding: 5px 10px;
                font-size: 0.9rem;
                font-weight: bold;
                border-radius: 5px;
            }
            </style>
            
            
            
            
            <!-- Phân trang -->
            <div class="d-flex justify-content-center">
                {{ $products->links('pagination::bootstrap-4') }}
            </div>
          </div>
      </div>
  </div>


 

  <!-- CSS tùy chỉnh cho product card và filter (dùng tên riêng) -->
  <style>
   
    .breadcrumb-item + .breadcrumb-item::before {
    content: "/";
    color: #fff; /* Màu trắng cho dấu "/" */
    padding: 0 0.5rem; /* Khoảng cách xung quanh dấu "/" */
}

      /* Breadcrumb Container */
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
            transition: text-decoration 0.3s ease;
        }
        .breadcrumb-item a:hover {
            text-decoration: underline;
        }
        .breadcrumb-item.active {
            color: #6c757d;
        }
/* Product Card */
.product-card {
    border: 0;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
    height: 300px;
    position: relative;
}

.product-image-container {
    position: relative; /* Để các phần tử con tuyệt đối được định vị dựa trên container này */
    overflow: hidden;
    height: 220px; /* Hoặc chiều cao bạn mong muốn */
    border: 1px solid #949494b3;

}
.product-image-container:hover {
    border: 1px solid #ddd;

}
.discount-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 50px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: bold;
    z-index: 10;
    border-radius: 4px; /* Bo góc nhẹ, không tròn hoàn toàn */
}
/* Hình sản phẩm */
.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease, border 0.3s ease, filter 0.3s ease;
}

/* Hover: tăng scale, thêm viền mờ và sáng lên */
.product-image-container:hover .product-image {
    transform: scale(1.1);
    border: 2px solid rgba(255, 255, 255, 0.3); /* viền mờ */
    filter: brightness(1.2); /* làm sáng ảnh */
    box-sizing: border-box;
}

/* Thông tin sản phẩm (thay cho .card-body) */
.product-info {
    padding: 15px;
    padding-left: 0px;
    height: 130px;
    background: #1e1e1e;
}

/* Link tiêu đề sản phẩm (thay cho .card-title-link) */
.product-title-link {
    text-decoration: none;
    color: #ddd;
    font-weight: bold;
    transition: color 0.3s ease;
    text-align: left;  /* căn trái */
}

.product-title-link:hover {
    color: #ff4081;
}

/* Tiêu đề sản phẩm (thay cho .card-title) */
.product-title {
    font-size: 0.9rem;
    font-weight: bold;
    text-align: left;       /* căn trái */
    white-space: normal;    /* cho phép hiển thị đầy đủ */
}

/* Mô tả hoặc thông tin khác của sản phẩm (thay cho .card-text) */
.product-text {
    font-size: 1rem;
    text-align: left;       /* căn trái */
}

/* Text muted: màu sáng hơn để phù hợp với nền tối */
.text-muted {
    color: #bbb !important;
}

/* Giá sản phẩm: đổi sang màu vàng */
.product-price {
    color: rgb(220, 220, 7);
    font-weight: bold;
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
