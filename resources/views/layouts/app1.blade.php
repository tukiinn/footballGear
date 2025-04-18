<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đại Nông</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<!-- jQuery UI JS -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="css/app.css">

</head>
<body>
  <!-- Floating Buttons -->
<div id="floating-buttons">
    <!-- Nút "Back to top" chỉ xuất hiện khi cuộn xuống -->
    <button type="button" class="round-btn" id="back-to-top" title="Back to top">
      <i class="fas fa-arrow-up"></i>
    </button>
    <!-- Nút "Sản phẩm xem gần đây" luôn hiển thị -->
    <a href="#" class="round-btn always-visible" id="recent-btn" title="Sản phẩm xem gần đây" data-bs-toggle="modal" data-bs-target="#recentlyViewedModal">
      <i class="fas fa-history"></i>
    </a>
  </div>
  
  <!-- Modal Sản phẩm xem gần đây (hiệu ứng right fade) -->
  <div class="modal right fade" id="recentlyViewedModal" tabindex="-1" aria-labelledby="recentlyViewedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="recentlyViewedModalLabel">Sản phẩm xem gần đây</h5>
          <!-- Nút đóng sử dụng cú pháp Bootstrap 5 -->
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
        </div>
        <div class="modal-body">
          @if(session()->has('recently_viewed') && count(session('recently_viewed')) > 0)
            <div class="row">
              @php
                // Lấy danh sách ID sản phẩm đã xem từ session
                $recentIds = session('recently_viewed');
                // Truy vấn sản phẩm theo danh sách ID và sắp xếp theo thứ tự xem (mới nhất đứng đầu)
                $recentProducts = \App\Models\Product::whereIn('id', $recentIds)
                                    ->orderByRaw("FIELD(id, " . implode(',', $recentIds) . ")")
                                    ->get();
              @endphp
  
  @foreach($recentProducts as $product)
  <div class="mb-3">
    <!-- Bọc toàn bộ thẻ trong <a> để khi bấm vào chuyển sang trang chi tiết sản phẩm -->
    <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none text-reset">
      <div class="product-card-recent shadow-sm position-relative">
        <!-- Badge "Recent" hiển thị ở góc phải trên của card -->
        <span class="badge bg-primary position-absolute" style="top: 10px; right: 10px;">Recent</span>
        <div class="row g-0 align-items-center">
          <!-- Cột chứa ảnh sản phẩm với kích thước 100x100 -->
          <div class="col-auto">
            @if($product->image)
              <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="img-fluid" style="width: 100px; height: 100px; object-fit: cover;">
            @else
              <img src="https://via.placeholder.com/100x100?text=No+Image" alt="No Image" class="img-fluid" style="width: 100px; height: 100px; object-fit: cover;">
            @endif
          </div>
          <!-- Cột chứa thông tin sản phẩm: tên và khoảng giá -->
          <div class="col">
            <div class="py-2">
              <h5 class="product-title mb-1 ms-2">{{ $product->product_name }}</h5>
              @php
                // Tính toán khoảng giá dựa trên đơn vị
                if($product->unit == 'kg'){
                  $price_min = $product->discount_price / 4;
                  $price_max = $product->discount_price;
                } else {
                  $price_min = $product->discount_price;
                  $price_max = $product->price;
                }
              @endphp
              <p class="product-price mb-0 ms-2">
                <strong>
                  {{ number_format($price_min, 0, ',', '.') }}đ - {{ number_format($price_max, 0, ',', '.') }}đ
                </strong>
              </p>
            </div>
          </div>
        </div>
      </div>
    </a>
  </div>
@endforeach
            </div>
          @else
            <p>Chưa có sản phẩm nào được xem gần đây.</p>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript: Hiển thị nút Back to top khi cuộn và xử lý sự kiện click -->
  <script>
    window.addEventListener('scroll', function() {
      const backToTopButton = document.getElementById('back-to-top');
      // Nếu cuộn xuống hơn 200px, thêm class 'show' vào nút Back to top
      if (window.scrollY > 200) {
        backToTopButton.classList.add('show');
      } else {
        backToTopButton.classList.remove('show');
      }
    });

    document.getElementById('back-to-top').addEventListener('click', function() {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  </script>
<!-- Navbar dòng 1 -->
<nav class="navbar navbar-expand-lg navbar-first">
    <div class="container d-flex align-items-center" style="flex-wrap: nowrap;">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="/images/logo/logofn.png" alt="Logo">
        </a>
        <form class="d-flex flex-grow-1 justify-content-center mx-3">
            <input class="form-control me-2" type="search" placeholder="Tìm kiếm sản phẩm" aria-label="Search" style="max-width: 400px;">
            <button class="btn btn-outline-light" type="submit">Tìm kiếm</button>
        </form>
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
            @php
            if(auth()->check()) {
                $totalItems = \App\Models\Cart::where('user_id', auth()->id())->count();
            } else {
                $cart = session()->get('cart', []);
                $totalItems = count($cart);
            }
        @endphp
        
        <!-- Nút kích hoạt modal giỏ hàng -->
        <button type="button" class="btn btn-outline-primary ms-3" data-bs-toggle="modal" data-bs-target="#cartModal" title="Giỏ hàng">
            <i class="fas fa-shopping-cart"></i> 
            <span id="cartItemCount" class="badge bg-danger">{{ $totalItems }}</span>

        </button>
       
        </div>
    </div>
</nav>

<!-- Navbar dòng 2 -->
<nav class="navbar navbar-expand-lg navbar-second">
    <div class="container">
        <ul class="navbar-nav" id="menu">
            <!-- Danh mục sản phẩm với dropdown -->
            <li class="nav-item position-relative">
                <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                    <i class="fa-solid fa-list me-2"></i>Danh mục sản phẩm 
                    <i class="fas fa-chevron-down muiten ms-2"></i>
                </a>
                <ul class="metismenu">
                    @foreach($categories as $category)
                        <li class="d-flex align-items-center position-relative">
                            <a href="{{ route('categories.show', $category->id) }}" class="flex-grow-1 d-flex justify-content-between align-items-center">                      
                                {{ $category->category_name }}
                                @if($category->hasSubCategories())
                                    <i class="fas fa-chevron-right ms-2"></i>
                                @endif
                            </a>
                            @if($category->hasSubCategories())
                                <ul class="submenu">
                                    @foreach($category->subCategories as $subCategory)
                                        <li>
                                            <a href="{{ route('categories.show', $subCategory->id) }}">
                                              
                                                {{ $subCategory->category_name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                        @if(!$loop->last)
                            <hr class="my-2 w-100">
                        @endif
                    @endforeach
                </ul>
            </li>
           <!-- Menu "Sản phẩm" -->
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
       Sản phẩm
    </a>
</li>
<!-- Menu "Đơn hàng" -->
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
        Đơn hàng
    </a>
</li>
<!-- Menu "Tin tức" -->
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('news.*') ? 'active' : '' }}" href="{{ route('news.index') }}">
        Tin tức
    </a>
</li>
<!-- Menu "Liên hệ" -->
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('address.*') ? 'active' : '' }}" href="{{ route('address.index') }}">
       Liên hệ 
    </a>
</li>

        </ul>
    </div>
</nav>



    @if(session('success'))
    <div class="toast-container">
        <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">{{ session('success') }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="toast-container">
        <div class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">{{ session('error') }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif
    
    <main>
        @yield('content')
    </main>
 @include('cart._cart_modal')
 <!-- Footer -->
  <!-- Đường gạch ngăn cách giữa nội dung và dòng chữ cuối -->
  <hr class="border-top border-secondary">
  <!-- Footer -->
<footer class="bg-white text-dark pt-5">
    
    <div class="container">
      <div class="row">
        <!-- Cột Giới thiệu -->
        <div class="col-md-3 mb-4">
          <h5 class="text-uppercase">Giới thiệu</h5>
          <p>Đại Nông là nền tảng thương mại điện tử hàng đầu chuyên cung cấp nông sản sạch, an toàn và đảm bảo chất lượng.</p>
        </div>
        <!-- Cột Thông tin -->
        <div class="col-md-3 mb-4">
          <h5 class="text-uppercase">Thông tin</h5>
          <ul class="list-unstyled">
            <li><a href="#" class="text-dark text-decoration-none">Giới thiệu</a></li>
            <li><a href="#" class="text-dark text-decoration-none">Tin tức</a></li>
            <li><a href="#" class="text-dark text-decoration-none">Liên hệ</a></li>
            <li><a href="#" class="text-dark text-decoration-none">Tuyển dụng</a></li>
          </ul>
        </div>
        <!-- Cột Hỗ trợ khách hàng -->
        <div class="col-md-3 mb-4">
          <h5 class="text-uppercase">Hỗ trợ khách hàng</h5>
          <ul class="list-unstyled">
            <li><a href="#" class="text-dark text-decoration-none">Hướng dẫn mua hàng</a></li>
            <li><a href="#" class="text-dark text-decoration-none">Chính sách đổi trả</a></li>
            <li><a href="#" class="text-dark text-decoration-none">Chính sách bảo mật</a></li>
            <li><a href="#" class="text-dark text-decoration-none">Điều khoản sử dụng</a></li>
          </ul>
        </div>
        <!-- Cột Liên hệ -->
        <div class="col-md-3 mb-4">
          <h5 class="text-uppercase">Liên hệ</h5>
          <ul class="list-unstyled">
            <li><i class="fas fa-map-marker-alt me-2"></i>41A Đ. Phú Diễn, Phú Diễn, Bắc Từ Liêm, Hà Nội</li>
            <li><i class="fas fa-phone me-2"></i>0123 456 789</li>
            <li><i class="fas fa-envelope me-2"></i>donhotu03.dev@gmail.com</li>
          </ul>
          <div class="mt-3">
            <a href="https://www.facebook.com/topkiin.Tu" class="text-dark me-2"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="text-dark me-2"><i class="fab fa-twitter"></i></a>
            <a href="#" class="text-dark me-2"><i class="fab fa-instagram"></i></a>
            <a href="#" class="text-dark me-2"><i class="fab fa-youtube"></i></a>
          </div>
        </div>
      </div>
      <!-- Đường gạch ngăn cách giữa nội dung và dòng chữ cuối -->
      <hr class="border-top border-secondary">
      <div class="row">
        <div class="col text-center pb-3">
          <p class="mb-0">&copy; 2025 Đại Nông. All rights reserved.</p>
        </div>
      </div>
    </div>
  </footer>
  <!-- End Footer -->
  
</body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="js/toast.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    var cartModal = document.getElementById('cartModal');

    if (cartModal) {
        cartModal.addEventListener('shown.bs.modal', function () {
            document.body.style.paddingRight = '0px';
        });

        cartModal.addEventListener('hidden.bs.modal', function () {
            document.body.style.paddingRight = '0px';
        });
    }
});

</script>

</html>
