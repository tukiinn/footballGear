<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'HieuStore')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.ckeditor.com/4.20.0/full/contents.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;700&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  @stack('styles')
  <style>
    body {
      background-color: #1e1e1e;
    }
    :root {
      --primary-color: #ff4081;
      --secondary-color: #e91e63;
      --accent-color: #1a1a1a;
      --bg-color: #121212;
      --text-color: #fff;
      --navbar-height: 80px;
    }
    /* Custom Navbar CSS – giữ nguyên theo thiết kế cũ */
    .navbar {
      background: var(--accent-color);
      padding: 10px 0;
      height: 140px;
    }
    .inner-navbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: var(--navbar-height);
    }
    .left, .center, .right {
      flex: 1;
      display: flex;
      align-items: center;
    }
    .left {
      justify-content: flex-start;
    }
    .center {
      justify-content: center;
    }
    .right {
      justify-content: flex-end;
      gap: 20px;
    }
    .logo img {
      height: 80px;
    }
    .search-form {
      position: relative;
      width: 100%;
      max-width: 400px;
    }
    .search-form input[type="text"] {
      width: 100%;
      padding: 8px 12px;
      border: none;
      border-radius: 20px;
      outline: none;
    }
    .search-form button {
      position: absolute;
      right: 0;
      top: 0;
      bottom: 0;
      background: var(--primary-color);
      border: none;
      color: #fff;
      padding: 0 12px;
      border-radius: 0 20px 20px 0;
      cursor: pointer;
    }
    .right a {
      color: var(--text-color);
      text-decoration: none;
      font-size: 1.1rem;
      position: relative;
    }
    .right a .cart-count {
      position: absolute;
      top: -5px;
      right: -10px;
      background: var(--secondary-color);
      color: #fff;
      padding: 2px 6px;
      border-radius: 50%;
      font-size: 0.5rem;
    }
    /* Sub Navbar: Categories */
    .sub-navbar {
  background: var(--accent-color);
  padding: 10px 0;
  border-top: 0.5px solid rgba(255, 255, 255, 0.1);
  border-bottom: 0.5px solid rgba(255, 255, 255, 0.1);
}

  
    /* Hiệu ứng hover cho logo */
    .navbar .logo a img {
      transition: transform 0.3s ease, opacity 0.3s ease;
    }
    .navbar .logo a:hover img {
      transform: scale(1.05);
      opacity: 0.85;
    }
  
    /* Hiệu ứng hover cho ô tìm kiếm và nút tìm */
    .search-form input[type="text"] {
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    .search-form input[type="text"]:hover,
    .search-form input[type="text"]:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 5px rgba(255, 64, 129, 0.5);
    }
    .search-form button {
      transition: background-color 0.3s ease, transform 0.3s ease;
    }
    .search-form button:hover {
      background-color: var(--primary-color);

    }
  
    /* Hiệu ứng hover cho các liên kết ở bên phải */
    .right a,
    .right button {
      transition: transform 0.3s ease, opacity 0.3s ease;
    }
    .right a:hover,
    .right button:hover {
      transform: translateY(-2px);
      opacity: 0.9;
    }
    img {
  outline: none !important;
}

  /* Sale button & Hotline styles (như trước) */
  .sale-btn {
    color: #fff;
    background: linear-gradient(45deg, #ff416c, #ff4b2b);
    border: none;
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: bold;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .sale-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(255, 75, 43, 0.4);
  }
  .hotline {
    color: yellow;
    font-weight: bold;
    font-size: 1.1rem;
  }


  </style>
  
</head>
<body>
  <!-- Top Navbar -->
  <nav class="navbar">
    <div class="container inner-navbar">
      <!-- Left: Logo -->
      <div class="left">
        <div class="logo ms-5">
          <a href="{{ url('/') }}">
            <img src="{{ asset('images/logo/hieustorelogo.png') }}" alt="HieuStore Logo">
          </a>
        </div>
      </div>
      <!-- Center: Tìm kiếm -->
      <div class="center">
        <form action="{{ route('products.search') }}" method="GET" class="search-form">
          <input type="text" name="query" placeholder="Tìm kiếm sản phẩm...">
          <button type="submit">Tìm</button>
        </form>
        
      </div>
      <!-- Right: Giỏ hàng & Đăng nhập -->
      <div class="right">
       
        @if(auth()->check())
        <div class="dropdown">
          <button class="btn btn-custom dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa-solid fa-user me-2"></i>
            <span>{{ auth()->user()->name }}</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{ route('orders.index') }}">
                <i class="fa-solid fa-box me-2"></i> Đơn hàng
              </a>
            </li>
            <li>
              <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="dropdown-item d-flex align-items-center">
                  <i class="fa-solid fa-right-from-bracket me-2"></i> Đăng xuất
                </button>
              </form>
            </li>
          </ul>
        </div>
      
        <style>
          /* Nút tùy chỉnh dark mode */
          .btn-custom {
            background-color: #343a40;
            border: none;
            color: #fff;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            border-radius: 0.375rem;
            transition: background-color 0.3s, color 0.3s, opacity 0.3s;
          }
          .btn-custom:hover {
            background-color: #495057;
            color: #fff;
          }
          /* Loại bỏ hiệu ứng mờ khi active/focus */
          .btn-custom:focus,
          .btn-custom:active {
            background-color: #343a40;
            opacity: 1;
            box-shadow: none;
            color: #fff;

          }
      
          /* Dropdown menu */
          .dropdown-menu {
            background-color: #343a40;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            min-width: 220px;
          }
          .dropdown-item {
            color: #fff;
            transition: background-color 0.3s, color 0.3s;
          }
          /* Đảm bảo chữ luôn sáng khi hover, active hay focus */
          .dropdown-item:hover,
          .dropdown-item:focus,
          .dropdown-item.active {
            background-color: #495057;
            color: #fff;
          }
        </style>
      @else
        <a href="{{ route('login') }}" class="btn-custom me-2" title="Đăng ký/Đăng nhập">
          <i class="fa-solid fa-user"></i>
        </a>
      @endif
      
    @php
    $cartCount = auth()->check() 
      ? \App\Models\Cart::where('user_id', auth()->id())->count() 
      : 0;
  @endphp
  
  <a href="{{ url('/cart') }}">
    <i class="fa fa-shopping-cart"></i>
    <span class="cart-count">{{ $cartCount }}</span>
  </a>
  
      </div>
      
    </div>
  </nav>
  <style>
    .sale-btn {
      color: #fff;
      background: linear-gradient(45deg, #ff416c, #ff4b2b);
      border: none;
      padding: 10px 20px;
      border-radius: 10px;
      font-weight: bold;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .sale-btn:hover {
      transform: scale(1.1);
      box-shadow: 0 4px 15px rgba(255, 75, 43, 0.4);
    }
    .hotline {
      color: yellow;
      font-weight: bold;
      font-size: 1rem;
    }
  </style>
  
  <!-- Sub Navbar: Categories với dropdown và hotline -->
  <nav class="sub-navbar">
    <div class="container d-flex align-items-center justify-content-between">
      <ul class="navbar-nav flex-row gap-4 mb-0">
        <!-- Dropdown Sản Phẩm -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle ms-3" href="#" id="productDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Sản Phẩm
          </a>
          <ul class="dropdown-menu" aria-labelledby="productDropdown">
            <li>
              <a class="dropdown-item" href="{{ route('products.index') }}">Xem tất cả sản phẩm</a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ url('/products?category=6') }}">Giày bóng đá Nike</a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ url('/products?category=7') }}">Giày bóng đá Adidas</a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ url('/products?category=1') }}">Giày bóng đá Puma</a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ url('/products?category=2') }}">Giày bóng đá Adidas X</a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ url('/products?category=3') }}">Giày bóng đá Mizuno</a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ url('/products?category=9') }}">Bóng</a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ url('/products?category=15') }}">Phụ kiện bóng đá</a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ url('/products?category=13') }}">Áo bóng đá</a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ url('/products?category=14') }}">Găng thủ môn</a>
            </li>
          </ul>
        </li>
        
        <!-- Dropdown Kid được tách riêng -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="kidDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Kid
          </a>
          <ul class="dropdown-menu" aria-labelledby="kidDropdown">
            <li>
              <a class="dropdown-item" href="{{ url('/products?category=10') }}">Giày bóng đá kiz</a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ url('/products?category=11') }}">Găng tay thủ môn kiz</a>
            </li>
          </ul>
        </li>
        
        <!-- Dropdown Tin Tức -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="newsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Tin Tức
          </a>
          <ul class="dropdown-menu" aria-labelledby="newsDropdown">
            <li>
              <a class="dropdown-item" href="{{ url('/news/chuong-trinh-sales-het-don-tet-toan-bo-san-pham-giay-da-bong-chinh-hang') }}">Sales Hết Đón Tết</a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ url('/news/ve-dep-cua-adidas-predator-accuracy-trong-bst-crazy-rush') }}">Vẻ đẹp của Adidas Predator Accuracy</a>
            </li>
          </ul>
        </li>
        
        <!-- Dropdown Hướng Dẫn -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="guideDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Hướng Dẫn
          </a>
          <ul class="dropdown-menu" aria-labelledby="guideDropdown">
            <li>
              <a class="dropdown-item" href="{{ url('/news/huong-dan-dat-hang') }}">Hướng Dẫn Đặt Hàng</a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ url('/news/chinh-sach-doi-san-pham') }}">Hướng Dẫn Đổi Trả Sản Phẩm</a>
            </li>
          </ul>
        </li>
        
        <!-- Nút Sale với hiệu ứng đặc biệt -->
        <li class="nav-item">
          <a class="nav-link sale-btn" href="{{ url('/products?category=&condition=sale') }}">Sale</a>
        </li>
      </ul>
      <!-- Phần Hotline căn bên phải -->
      <div class="hotline">
        Hotline: 0123 456 789 - 0999 999 999
      </div>
    </div>
  </nav>
  
  

<div class="container">
  @yield('content')
</div>
  <style>
    .shop-footer {
      background: linear-gradient(135deg, #1a1a1a, #121212);
      color: #fff;
      padding: 40px 0;
    }
    .shop-footer-title {
      font-family: 'Montserrat', sans-serif;
      font-size: 1.25rem;
      margin-bottom: 15px;
      color: #ff4081;
    }
    .shop-footer-text {
      font-size: 0.95rem;
      line-height: 1.6;
      color: #ccc;
    }
    .shop-footer-link {
      color: #ccc;
      text-decoration: none;
      transition: color 0.3s ease;
    }
    .shop-footer-link:hover {
      color: #ff4081;
    }
    .shop-footer-social {
      display: flex;
      gap: 15px;
    }
    .shop-footer-social-link {
      color: #ccc;
      font-size: 1.25rem;
      transition: color 0.3s ease, transform 0.3s ease;
    }
    .shop-footer-social-link:hover {
      color: #ff4081;
      transform: scale(1.1);
    }
    .shop-footer-divider {
      border-color: #444;
      margin: 20px 0;
    }
    .shop-footer-copy {
      font-size: 0.875rem;
      color: #aaa;
    }
      .sub-navbar .navbar-nav .nav-link.dropdown-toggle,
  .sub-navbar .navbar-nav .dropdown > .nav-link {
    color: #fff; /* Màu hồng mặc định */
  }
      /* Hiệu ứng cho dropdown trigger (tên dropdown chính) */
      .sub-navbar .navbar-nav .nav-link.dropdown-toggle:hover,
      .sub-navbar .navbar-nav .dropdown:hover > .nav-link {
        color: #ff4081; /* Đổi màu chữ sang hồng */
      }
      /* Chỉ áp dụng cho dropdown trong .sub-navbar */
      .sub-navbar .navbar-nav .dropdown:hover > .dropdown-menu {
        display: block;
        position: absolute;
        z-index: 1050;
        background-color: #343a40; /* Nền tối cho dropdown */
        border: none;
        border-radius: 0px;
      }
      .sub-navbar .dropdown-menu {
        background-color: #343a40; /* Nền tối cho menu dropdown */
        border: none;
      }
      .sub-navbar .dropdown-menu .dropdown-item {
        color: #fff; /* Chữ trắng cho các mục trong dropdown */
      }
      .sub-navbar .dropdown-menu .dropdown-item:hover {
        background-color: var(--primary-color);
        color: #fff;
      }
      /* Hỗ trợ nested dropdown cho submenu */
      .sub-navbar .dropdown-submenu {
        position: relative;
      }
      .sub-navbar .dropdown-submenu > .dropdown-menu {
        top: 0;
        left: 100%;
        margin-top: -1px;
        position: absolute;
        z-index: 1060;
        background-color: #343a40;
        border: none;
        border-radius: 0px;
      }
      .sub-navbar .dropdown-submenu:hover > .dropdown-menu {
        display: block;
      }
      .dropdown-item {
    padding-top: 10px;
    padding-bottom: 10px;
  }
    </style>
<style>
  /* Tùy chỉnh giao diện cho Dialogflow Messenger (Chat bot) */
  df-messenger {
    --df-messenger-bot-message: #fff;
    --df-messenger-bot-message-background: #222;
    --df-messenger-user-message: #fff;
    --df-messenger-user-message-background: #444;
    --df-messenger-chat-background-color: #333;
    --df-messenger-button-titlebar-color: #222;
  }

  /* Floating Buttons Styles */

  /* Back to Top Button */
  #back-to-top {
    position: fixed;
    bottom: 210px; /* tăng vị trí lên 60px so với ban đầu */
    right: 20px;
    display: none; /* ẩn ban đầu, hiển thị khi scroll */
    z-index: 10; /* ưu tiên hơn các nút chat */
    background-color: #222; /* nền tối */
    color: #fff;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    cursor: pointer;
    border: 2px solid #ffd90050; /* viền vàng sáng */
    transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
  }
  #back-to-top:hover {
    background-color: #333;
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
  }

  /* Chat Buttons */
  .chat-btn {
    position: fixed;
    right: 20px;
    z-index: 10;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    text-decoration: none;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    cursor: pointer;
    border: 2px solid #ffd90050; /* viền vàng sáng */
    transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
  }
  /* Nút Zalo */
  #zalo-chat {
    background-color: #222; /* nền tối */
    bottom: 150px; /* tăng lên 60px */
  }
  #zalo-chat:hover {
    background-color: #333;
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
  }
  /* Nút Messenger */
  #messenger-chat {
    background-color: #222; /* nền tối */
    bottom: 90px; /* tăng lên 60px */
  }
  #messenger-chat:hover {
    background-color: #333;
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
  }
</style>

<!-- Floating Buttons -->
<script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
<df-messenger
  intent="WELCOME"
  chat-title="HieuStore"
  agent-id="31239aeb-183f-4b97-b612-2f03369a1d20"
  language-code="vi"
></df-messenger>

<!-- Back to Top Button -->
<a id="back-to-top" href="#" title="Back to Top">
  <i class="fa-solid fa-arrow-up"></i>
</a>
<a id="zalo-chat" href="https://zalo.me/0988651560" target="_blank" title="Chat qua Zalo" class="chat-btn">
  <i class="fa-solid fa-comment-dots"></i>
</a>
<a id="messenger-chat" href="https://m.me/ominhhieu.402531" target="_blank" title="Chat qua Messenger" class="chat-btn">
  <i class="fa-brands fa-facebook-messenger"></i>
</a>


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
    right: 0px;
    transform: translateY(-50%);
    filter: invert(1);
    opacity: 0.8;
    transition: opacity 0.3s ease;
  }
  .alert-modern .btn-close:hover {
    opacity: 1;
  }
</style>


<script>
  // Tự động đóng alert sau 5 giây
  setTimeout(function() {
    document.querySelectorAll('.alert').forEach(function(alertEl) {
      var alertInstance = new bootstrap.Alert(alertEl);
      alertInstance.close();
    });
  }, 5000);
</script>
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




<!-- JavaScript cho nút Back to Top -->
<script>
  // Hiển thị/ẩn nút Back to Top khi scroll
  window.addEventListener('scroll', function() {
    var backToTop = document.getElementById('back-to-top');
    if (window.pageYOffset > 200) {
      backToTop.style.display = 'flex';
    } else {
      backToTop.style.display = 'none';
    }
  });

  // Khi nhấp vào nút Back to Top thì scroll về đầu trang
  document.getElementById('back-to-top').addEventListener('click', function(e) {
    e.preventDefault();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
</script>

<footer class="shop-footer mt-5">
  <div class="container">
    <div class="row">
      <!-- Giới thiệu -->
      <div class="col-md-4 mb-3">
        <h5 class="shop-footer-title">Giới thiệu</h5>
        <p class="shop-footer-text">
          HieuStore là địa chỉ bán giày bóng đá và các phụ kiện xịn xò, nơi các fan bóng đá bùng nổ phong cách. Keep it lit!
        </p>
      </div>
      <!-- Liên kết nhanh -->
      <div class="col-md-4 mb-3">
        <h5 class="shop-footer-title">Liên kết nhanh</h5>
        <ul class="list-unstyled">
          <li><a href="{{ url('/') }}" class="shop-footer-link">Trang chủ</a></li>
          <li><a href="{{ url('/shop') }}" class="shop-footer-link">Shop</a></li>
          <li><a href="{{ url('/products') }}" class="shop-footer-link">Sản phẩm</a></li>
          <li><a href="{{ url('/wel') }}" class="shop-footer-link">Giới thiệu</a></li>
        </ul>
      </div>
      <!-- Social media -->
      <div class="col-md-4 mb-3">
        <h5 class="shop-footer-title">Theo dõi chúng tôi</h5>
        <div class="shop-footer-social">
          <a href="#" class="shop-footer-social-link">
            <i class="fab fa-facebook-f"></i>
          </a>
          <a href="#" class="shop-footer-social-link">
            <i class="fab fa-twitter"></i>
          </a>
          <a href="#" class="shop-footer-social-link">
            <i class="fab fa-instagram"></i>
          </a>
          <a href="#" class="shop-footer-social-link">
            <i class="fab fa-youtube"></i>
          </a>
        </div>
      </div>
    </div>
    <hr class="shop-footer-divider">
    <div class="text-center">
      <p class="shop-footer-copy">&copy; 2025 HieuStore. All rights reserved.</p>
    </div>
  </div>
</footer>
  <!-- Bootstrap JS Bundle (includes Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  @stack('scripts')
</body>
</html>
