@extends('layouts.app')

@section('content')
<!-- Banner & Breadcrumb -->
<div class="breadcrumb-container">
    <div class="container">
        <h2 class="breadcrumb-title mb-2">{{ $product->product_name }}</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Trang chủ</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('products.index') }}">Sản phẩm</a>
                </li>
            </ol>
        </nav>
    </div>
  </div>
  
  <style>
    /* Text muted: màu sáng hơn để phù hợp với nền tối */
.text-muted {
    color: #bbb !important;
}
    /* Breadcrumb Container */
    .breadcrumb-container {
      background-color: #1e1e1e;
      padding: 10px 20px;
      border-bottom: 1px solid #444;
    }
    .breadcrumb-title {
      color: #fff;
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
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
      color: #aaa;
    }
  </style>
  
  <style>
  /* Style cho phần lựa chọn size */
  .size-selection .size-option {
      cursor: pointer;
      width: 35px;
      height: 35px;
      line-height: 35px;
      text-align: center;
      transition: transform 0.3s ease, border 0.3s ease;
      border: 1px solid #555;
      border-radius: 50%;
      padding: 2px;
      background-color: #2c2f33;
      color: #fff;
  }
  .size-selection .size-option.selected {
      border: 1px solid #ff4081;
      transform: scale(1.1);
      background-color: #3a3f44;
  }
  
  /* Input & Focus */
  input:focus,
  .form-control:focus {
      outline: none !important;
      box-shadow: none !important;
      border-color: #555 !important;
  }
  
  /* Chrome, Safari, Edge, Opera */
  input::-webkit-outer-spin-button,
  input::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
  }
  
  /* Giảm kích thước ô input */
  .small-input {
      width: 50px;
      height: 60px;
      padding: 0;
      font-size: 0.875rem;
      line-height: 30px;
      background-color: #2c2f33;
      color: #fff;
      border: 1px solid #555;
  }
  
  /* Nút tăng/giảm số lượng */
  .quantity-selector .btn {
      height: 30.45px;
      width: 30px;
      padding: 0 5px;
      font-size: 0.875rem;
      line-height: 30px;
      background-color: #2c2f33;
      color: #fff;
      border: 1px solid #555;
  }
  
  /* Nhóm input tổng thể */
  .quantity-selector {
      max-width: 70px;
  }
  
  /* Loại bỏ bo tròn nút tăng/giảm */
  [data-bs-step="up"],
  [data-bs-step="down"] {
      border-radius: 0 !important;
  }
  </style>
  
  <!-- Products Card -->
<div class="container">
  <div class="card border-0 mb-5 dark-card">
      <div class="row g-0">
          <!-- Ảnh sản phẩm -->
          <div class="col-md-5 d-flex align-items-center justify-content-center" style="background-color: #1e1e1e;">
              <img src="{{ asset($product->image) }}" alt="{{ $product->product_name }}" class="img-fluid" style="max-height: 90%; border: 1px solid #ddd; object-fit: cover;">
          </div>
          <!-- Thông tin sản phẩm -->
          <div class="col-md-7">
              <div class="card-body">
                  <h4 class="card-title mb-3">{{ $product->product_name }}</h4>
                  <div class="mb-4">
                      @php
                          $basePrice = $product->discount_price ?? $product->price;
                      @endphp
                      @if($product->discount_price)
                          <p class="mb-2">
                              <del class="text-muted fs-5">{{ number_format($product->price, 0, ',', '.') }} ₫</del>
                          </p>
                          <p class="fw-bold fs-3" id="product-price-{{ $product->id }}" style="color: #ff4081;">
                              {{ number_format($basePrice, 0, ',', '.') }} ₫
                          </p>
                      @else
                          <p class="fw-bold fs-3" id="product-price-{{ $product->id }}" style="color: #ffcc00;">
                              {{ number_format($basePrice, 0, ',', '.') }} ₫
                          </p>
                      @endif

                      <p class="card-text mb-4 my-3" style="color: #ccc;">
                          {{ $product->description ?? 'Không có mô tả.' }}
                      </p>
                  </div>

                  <!-- Form thêm sản phẩm vào giỏ hàng -->
                  <form action="{{ route('cart.add', $product->id) }}" method="POST" class="add-to-cart-form">
                      @csrf
                      {{-- Nếu sản phẩm có size, hiển thị lựa chọn size --}}
                      @if($product->sizes && $product->sizes->count() > 0)
                      <div class="mb-4">
                          <label class="form-label" style="color: #fff;">Chọn Size:</label>
                          <div class="size-selection d-flex gap-2">
                              @foreach($product->sizes as $size)
                                  <span class="size-option d-flex align-items-center justify-content-center"
                                        data-size="{{ $size->size }}"
                                        data-available="{{ $size->quantity }}">
                                      {{ $size->size }}
                                  </span>
                              @endforeach
                          </div>
                      </div>
                      @endif

                      <style>
                        /* Đặt màu nền và kiểu chữ cho ô input số lượng */
                        input[type="number"].form-control {
                          background-color: #40444b;
                          color: #fff;
                          border: 1px solid #40444b;
                        }
                        input[type="number"].form-control:focus {
                          background-color: #40444b;
                          color: #fff;
                          border-color: #40444b;
                          box-shadow: none;
                          outline: none;
                        }
                      </style>

                      <div class="d-flex gap-3 align-items-end">
                          <!-- Ô nhập số lượng với nút + và - -->
                          <div class="rounded-3">
                              <label for="inputQuantitySelector" class="form-label" style="color: #fff;">Số lượng:</label>
                              <div class="input-group quantity-selector">
                                  <input type="number" id="inputQuantitySelector" class="form-control text-center small-input" name="quantity" value="1" min="1" max="10" step="1">
                                  <div class="input-group-append">
                                      <div class="btn-group-vertical" role="group" aria-label="Stepper">
                                          <button type="button" class="btn btn-secondary" data-bs-step="up">
                                              <span class="visually-hidden">Tăng số lượng</span>
                                              +
                                          </button>
                                          <button type="button" class="btn btn-secondary" data-bs-step="down">
                                              <span class="visually-hidden">Giảm số lượng</span>
                                              -
                                          </button>
                                      </div>
                                  </div>
                              </div>
                          </div>

                          <!-- Nút MUA NGAY -->
                          <div>
                              <button type="submit" class="btn btn-warning btn-lg d-flex align-items-center gap-2">
                                  <i class="fa-solid fa-cart-plus"></i>
                                  MUA NGAY
                              </button>
                          </div>
                      </div>
                      <!-- Hidden input để truyền size -->
                      <input type="hidden" name="size" id="selected-size" value="">
                  </form>
              </div>
          </div>
      </div>
   
  </div>
</div>

<!-- Thông tin sản phẩm -->
<div class="container mb-5">
    <h3 class="text-white mb-3">Thông tin sản phẩm</h3>
    <div class="card border-0 dark-card">
      <div class="card-body">
        <div class="product-detail-wrapper">
            <div class="product-detail-content">
                {!! $product->detail !!}
            </div>
            <div class="fade-overlay"></div>
            <!-- Mũi tên xuống dùng để mở rộng -->
            <div class="expand-arrow"><i class="fa fa-chevron-down"></i></div>
        </div>
      </div>
    </div>
  </div>
  
  
  </div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.expand-arrow').forEach(function(arrow) {
        arrow.addEventListener('click', function() {
            var wrapper = this.parentElement;
            wrapper.classList.add('expanded');
            // Ẩn mũi tên sau khi mở rộng nội dung
            this.style.display = 'none';
        });
    });
});

</script>
  <style>
 /* Định nghĩa keyframes cho hiệu ứng fadeIn */
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

/* Phần hiển thị chi tiết sản phẩm */
.product-detail-wrapper {
    position: relative;
    max-height: 150px; /* Giới hạn chiều cao ban đầu */
    overflow: hidden;
    transition: max-height 0.5s ease;
}
.product-detail-wrapper.expanded {
    max-height: none;
}
.product-detail-wrapper.expanded .product-detail-content {
    /* Khi mở rộng, nội dung sẽ fade in */
    animation: fadeIn 0.5s ease-out;
}
.product-detail-content {
    font-size: 0.9rem;
    line-height: 1.5;
    color: #ccc;
}

/* Hiệu ứng mờ ở dưới cùng */
.fade-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 40px;
    background: linear-gradient(to bottom, rgba(30,30,30,0), rgba(30,30,30,1));
    pointer-events: none;
    transition: opacity 0.5s ease;
}
.product-detail-wrapper.expanded .fade-overlay {
    opacity: 0;
}

/* Style cho mũi tên xuống */
.expand-arrow {
    position: absolute;
    bottom: -10px; /* Di chuyển mũi tên xuống dưới 20px */
    left: 50%;
    transform: translateX(-50%);
    color: #ff69b4; /* Màu hồng (hotpink) */
    cursor: pointer;
    font-size: 24px;
    opacity: 0;
    animation: fadeIn 1s forwards; /* Hiệu ứng fade in cho mũi tên */
}
.expand-arrow:hover {
    color: #ff1493; /* Màu hồng đậm hơn khi hover */
}



/* Các style chung */
.breadcrumb-item + .breadcrumb-item::before {
  content: "/";
  color: #fff;
  padding: 0 0.5rem;
}

.dark-card {
  background-color: #1e1e1e;
  color: #fff;
}
.dark-card .card-body {
  background-color: #1e1e1e;
}
.dark-card .card-title {
  color: #fff;
}
.dark-card .card-text {
  color: #ccc;
}

.btn-success {
  background-color: #28a745;
  border: none;
}
.btn-success:hover {
  background-color: #218838;
}
.btn-primary {
  background-color: #007bff;
  border: none;
}
.btn-primary:hover {
  background-color: #0069d9;
}

  </style>
  

<!-- Script JS xử lý UI cho lựa chọn size và nút tăng/giảm số lượng -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Xử lý lựa chọn size: update hidden input "selected-size"
    const sizeOptions = document.querySelectorAll('.size-option');
    sizeOptions.forEach(function(option) {
        option.addEventListener('click', function(){
            const selectedSize = this.getAttribute('data-size');
            document.getElementById('selected-size').value = selectedSize;
            
            // Nếu có thông tin số lượng tối đa, cập nhật max của input số lượng
            const available = this.getAttribute('data-available');
            if (available) {
                document.getElementById('inputQuantitySelector').setAttribute('max', available);
            }

            // Hiệu ứng viền cho size được chọn
            sizeOptions.forEach(el => el.classList.remove('selected'));
            this.classList.add('selected');
        });
    });

    // Xử lý nút tăng/giảm số lượng
    const quantityInput = document.getElementById('inputQuantitySelector');
    const btnUp = document.querySelector('[data-bs-step="up"]');
    const btnDown = document.querySelector('[data-bs-step="down"]');
    
    btnUp.addEventListener('click', function() {
        let currentValue = parseInt(quantityInput.value) || 1;
        let max = parseInt(quantityInput.getAttribute('max')) || 10;
        let step = parseInt(quantityInput.getAttribute('step')) || 1;
        if (currentValue < max) {
            quantityInput.value = currentValue + step;
        }
    });
    
    btnDown.addEventListener('click', function() {
        let currentValue = parseInt(quantityInput.value) || 1;
        let min = parseInt(quantityInput.getAttribute('min')) || 1;
        let step = parseInt(quantityInput.getAttribute('step')) || 1;
        if (currentValue > min) {
            quantityInput.value = currentValue - step;
        }
    });
    
    // Biến đánh dấu đăng nhập (true nếu đã đăng nhập, false nếu chưa)
    var isLoggedIn = @json(Auth::check());
    // Nếu chưa đăng nhập, bắt sự kiện submit form để hiện swal.fire
    if (!isLoggedIn) {
        const addToCartForm = document.querySelector('.add-to-cart-form');
        addToCartForm.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Bạn cần đăng nhập',
                text: 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng.',
                showCancelButton: true,
                confirmButtonText: 'Đăng nhập',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if(result.isConfirmed) {
                    window.location.href = "{{ route('login') }}";
                }
            });
        });
    }
});


</script>

@endsection
