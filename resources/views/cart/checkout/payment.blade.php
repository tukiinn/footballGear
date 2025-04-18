@extends('layouts.app')

@section('content')
<!-- Custom CSS cho giao diện dark của trang thanh toán với hiệu ứng viền mờ -->
<style>
  .breadcrumb-item + .breadcrumb-item::before {
    content: "/";
    color: #fff; /* Màu trắng cho dấu "/" */
    padding: 0 0.5rem;
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
  /* Card dark theme với viền mờ */
  .card {
    background-color: #1e1e1e;
    border: 1px solid rgba(255,255,255,0.1);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
  }
  .card-header {
    background-color: #1e1e1e;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    color: #fff;
  }
  .card-body {
    background-color: #1e1e1e;
    color: #fff;
  }
  /* Form control dark theme với viền mờ */
  .form-control {
    background-color: #2c2c2c;
    color: #fff;
    border: 1px solid rgba(255,255,255,0.1);
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.5);
  }
  .form-control::placeholder {
    color: #aaa;
  }
  .form-label {
    color: #fff;
  }
  /* Table dark theme */
  .table {
    background-color: #1e1e1e;
    color: #fff;
  }
  .table thead th {
    background-color: #2c2c2c;
    color: #fff;
  }
  .table tbody tr {
    border-top: 1px solid #444;
  }
  .table-bordered td,
  .table-bordered th {
    border-left: 0 !important;
    border-right: 0 !important;
    border-color: rgba(255,255,255,0.1) !important;
  }
  .table-secondary {
    background-color: #2c2c2c !important;
    color: #fff;
  }
  /* Ghi đè dark theme cho bảng trong card (cột bên phải) */
  .card .table {
    background-color: #1e1e1e;
    color: #fff;
  }
  .card .table td,
  .card .table th {
    background-color: transparent;
    color: #fff;
    border-color: rgba(255,255,255,0.1) !important;
  }
  .card .table thead th {
    background-color: #2c2c2c;
    color: #fff;
  }
  /* Nút thông báo lỗi */
  .text-danger {
    color: #ff6b6b !important;
  }
  /* Các nút hành động */
  .btn-action:hover {
    opacity: 0.85;
  }
</style>

<!-- Breadcrumb -->
<div class="breadcrumb-container">
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{ route('home') }}">Trang chủ</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
      </ol>
    </nav>
  </div>
</div>

<div class="container my-5">
  <div class="row">
    <!-- Cột bên trái: Thông tin thanh toán -->
    <div class="col-md-7">
      <div class="card shadow-sm mb-4">
        <div class="card-header">
          <h4 class="mb-0">Thông tin thanh toán</h4>
        </div>
        <div class="card-body">
          <!-- Form đặt hàng -->
          <form id="order-form" method="POST" class="mt-3">
            @csrf
            <div class="mb-3">
              <label for="ten_khach_hang" class="form-label">Tên khách hàng</label>
              <input type="text" class="form-control" id="ten_khach_hang" name="ten_khach_hang" placeholder="Nhập tên khách hàng" required>
              <div id="error-ten-khach-hang" class="text-danger mt-1 d-none">
                Tên khách hàng phải từ 6 đến 20 ký tự.
              </div>
            </div>
            <div class="mb-3">
              <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
              <input type="text" class="form-control" id="so_dien_thoai" name="so_dien_thoai" placeholder="Nhập số điện thoại" required>
              <div id="error-so-dien-thoai" class="text-danger mt-1 d-none">
                Số điện thoại phải gồm đúng 10 chữ số.
              </div>
            </div>
            
            <!-- Khối chọn địa chỉ từ API -->
            <div class="mb-3">
              <div class="row">
                <div class="col-md-4">
                  <label for="province" class="form-label">Tỉnh/Thành Phố</label>
                  <select id="province" class="form-select" required>
                    <option value="">Chọn Tỉnh/Thành</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="district" class="form-label">Quận/Huyện</label>
                  <select id="district" class="form-select" required disabled>
                    <option value="">Chọn Quận/Huyện</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="ward" class="form-label">Phường/Xã</label>
                  <select id="ward" class="form-select" required disabled>
                    <option value="">Chọn Phường/Xã</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label for="detailed_address" class="form-label">Địa chỉ chi tiết</label>
              <input type="text" class="form-control" id="detailed_address" placeholder="Số nhà, đường..." required>
            </div>
            <!-- Input ẩn chứa địa chỉ hoàn chỉnh -->
            <input type="hidden" name="dia_chi" id="dia_chi">
            
            <!-- Hidden inputs cho voucher và final total -->
            <input type="hidden" id="final-total-input" name="final_total" value="{{ $cartTotal }}">
            <input type="hidden" id="voucher_code" name="voucher_code" value="">
          </form>
        </div>
      </div>
    </div>
    
    <!-- Cột bên phải: Chi tiết đơn hàng & lựa chọn phương thức thanh toán -->
    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-header">
          <h4 class="mb-0">Chi tiết đơn hàng</h4>
        </div>
        <div class="card-body">
          <table class="table">
            <tbody>
              <tr>
                <th>Sản phẩm</th>
                <th class="text-end">Tổng phụ</th>
              </tr>
              @php
                $total = 0;
              @endphp
              @foreach ($cartItems as $item)
                @php
                  $price = $item->product->discount_price ?? $item->product->price;
                  $lineTotal = $price * $item->quantity;
                  $total += $lineTotal;
                @endphp
                <tr>
                  <td>
                    <div class="d-flex align-items-center position-relative">
                      <img src="{{ asset($item->product->image ?? 'https://placehold.it/50x50') }}"
                           alt="{{ $item->product->product_name }}"
                           style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                      <span class="position-absolute top-0 start-0 translate-middle badge bg-danger">
                        {{ $item->quantity }}
                      </span>
                      <div>
                        <span class="d-block fw-bold">{{ $item->product->product_name }}</span>
                        <p class="mb-0 text-secondary">Size:{{ $item->size }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="text-end">
                    {{ number_format($lineTotal, 0, ',', '.') }}₫
                  </td>
                </tr>
              @endforeach
              <tr>
                <td>Giao hàng:</td>
                <td class="text-end">Giao hàng miễn phí</td>
              </tr>
              <tr>
                <td>Tạm tính:</td>
                <td class="text-end">{{ number_format($total, 0, ',', '.') }}₫</td>
              </tr>
              <tr>
                <td colspan="2">
                  <div class="input-group">
                    <input type="text" id="voucher-code" class="form-control" placeholder="Nhập mã giảm giá">
                    <button id="apply-voucher" class="btn btn-primary">Áp dụng</button>
                  </div>
                  <p id="voucher-message" class="mt-2"></p>
                </td>
              </tr>
              <tr id="voucher-info" style="display: none;">
                <td>Giảm giá từ voucher:</td>
                <td class="text-end text-danger">
                  <span id="discount-type"></span><br>
                  <span id="discount-amount"></span>
                </td>
              </tr>
              <tr class="table-secondary">
                <td><strong>Tổng:</strong></td>
                <td class="text-end fw-bold" id="final-total">
                  {{ number_format($total, 0, ',', '.') }}₫
                </td>
              </tr>
            </tbody>
          </table>
          <div class="mb-3">
            <label class="form-label"><strong>Phương thức thanh toán</strong></label>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="phuong_thuc_thanh_toan" id="COD"
                     value="COD" form="order-form" required style="vertical-align: middle;">
              <label class="form-check-label" for="COD" style="display: inline-flex; align-items: center;">
                Thanh toán khi nhận hàng (COD)
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="phuong_thuc_thanh_toan" id="Momo"
                     value="Momo" form="order-form" required style="vertical-align: middle;">
              <label class="form-check-label" for="Momo" style="display: inline-flex; align-items: center;">
                Thanh toán Momo
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="phuong_thuc_thanh_toan" id="VNPay"
                     value="VNPay" form="order-form" required style="vertical-align: middle;">
              <label class="form-check-label" for="VNPay" style="display: inline-flex; align-items: center;">
                Thanh toán VNPay
              </label>
            </div>
          </div>
          <div class="d-grid">
            <button type="submit" form="order-form" name="redirect" class="btn btn-success">Đặt hàng</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- JavaScript lấy địa chỉ qua API -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Lấy danh sách Tỉnh/Thành phố
  fetch('https://provinces.open-api.vn/api/?depth=1')
    .then(response => response.json())
    .then(data => {
      const provinceSelect = document.getElementById('province');
      provinceSelect.innerHTML = '<option value="">Chọn Tỉnh/Thành</option>';
      data.forEach(province => {
        let option = document.createElement('option');
        option.value = province.code;
        option.text = province.name;
        provinceSelect.add(option);
      });
    });

  // Hàm tải Quận/Huyện
  function loadDistricts(provinceCode) {
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');
    fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`)
      .then(response => response.json())
      .then(data => {
        districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
        districtSelect.disabled = false;
        data.districts.forEach(district => {
          let option = document.createElement('option');
          option.value = district.code;
          option.text = district.name;
          districtSelect.add(option);
        });
        wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
        wardSelect.disabled = true;
      });
  }

  // Hàm tải Phường/Xã
  function loadWards(districtCode) {
    const wardSelect = document.getElementById('ward');
    fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
      .then(response => response.json())
      .then(data => {
        wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
        wardSelect.disabled = false;
        data.wards.forEach(ward => {
          let option = document.createElement('option');
          option.value = ward.code;
          option.text = ward.name;
          wardSelect.add(option);
        });
      });
  }

  // Xử lý thay đổi chọn Tỉnh/Thành
  document.getElementById('province').addEventListener('change', function () {
    const provinceCode = this.value;
    if (provinceCode) {
      loadDistricts(provinceCode);
    } else {
      document.getElementById('district').innerHTML = '<option value="">Chọn Quận/Huyện</option>';
      document.getElementById('district').disabled = true;
      document.getElementById('ward').innerHTML = '<option value="">Chọn Phường/Xã</option>';
      document.getElementById('ward').disabled = true;
    }
    updateFullAddress();
  });

  // Xử lý thay đổi Quận/Huyện
  document.getElementById('district').addEventListener('change', function () {
    const districtCode = this.value;
    if (districtCode) {
      loadWards(districtCode);
    } else {
      document.getElementById('ward').innerHTML = '<option value="">Chọn Phường/Xã</option>';
      document.getElementById('ward').disabled = true;
    }
    updateFullAddress();
  });

  // Khi chọn Phường/Xã hoặc nhập Địa chỉ chi tiết
  document.getElementById('ward').addEventListener('change', updateFullAddress);
  document.getElementById('detailed_address').addEventListener('input', updateFullAddress);

  // Hợp nhất các trường địa chỉ thành 1 chuỗi đầy đủ
  function updateFullAddress() {
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');
    const detailed = document.getElementById('detailed_address').value.trim();

    const provinceText = provinceSelect.selectedOptions[0] ? provinceSelect.selectedOptions[0].text : '';
    const districtText = districtSelect.selectedOptions[0] ? districtSelect.selectedOptions[0].text : '';
    const wardText = wardSelect.selectedOptions[0] ? wardSelect.selectedOptions[0].text : '';

    let fullAddress = detailed;
    if (wardText && wardText !== 'Chọn Phường/Xã') {
      fullAddress += ', ' + wardText;
    }
    if (districtText && districtText !== 'Chọn Quận/Huyện') {
      fullAddress += ', ' + districtText;
    }
    if (provinceText && provinceText !== 'Chọn Tỉnh/Thành') {
      fullAddress += ', ' + provinceText;
    }
    document.getElementById('dia_chi').value = fullAddress;
  }
});
</script>



<!-- JavaScript kiểm tra dữ liệu và thay đổi action của form theo phương thức thanh toán -->
<script>
  
  document.getElementById('order-form').addEventListener('submit', function(e) {
      let tenKhachHang = document.getElementById('ten_khach_hang').value.trim();
      let soDienThoai = document.getElementById('so_dien_thoai').value.trim();

      let isValid = true;

      // Reset các thông báo lỗi
      document.getElementById('error-ten-khach-hang').classList.add('d-none');
      document.getElementById('error-so-dien-thoai').classList.add('d-none');

      if (tenKhachHang.length < 6 || tenKhachHang.length > 20) {
          document.getElementById('error-ten-khach-hang').classList.remove('d-none');
          isValid = false;
      }
      if (!/^\d{10}$/.test(soDienThoai)) {
          document.getElementById('error-so-dien-thoai').classList.remove('d-none');
          isValid = false;
      }
      if (!isValid) {
          e.preventDefault();
      }
  });

  document.addEventListener("DOMContentLoaded", function () {
      const orderForm = document.getElementById("order-form");
      const paymentMethods = document.querySelectorAll("input[name='phuong_thuc_thanh_toan']");
      paymentMethods.forEach(method => {
          method.addEventListener("change", function () {
              if (this.value === "COD") {
                  orderForm.action = "{{ route('orders.create') }}";
              } else if (this.value === "VNPay") {
                  orderForm.action = "{{ route('vnpay.vn') }}";
              }
              else if (this.value === "Momo") {
                  orderForm.action = "{{ route('momo.vn') }}";
              }
          });
      });
  });

  // Voucher application
  document.getElementById('apply-voucher').addEventListener('click', function (e) {
      e.preventDefault();
      const code = document.getElementById('voucher-code').value;
      const cartTotal = {{ $total }}; // Tổng tiền ban đầu

      fetch("{{ route('voucher.apply') }}", {
          method: "POST",
          headers: {
              "Content-Type": "application/json",
              "X-CSRF-TOKEN": "{{ csrf_token() }}"
          },
          body: JSON.stringify({ code: code })
      })
      .then(response => response.json())
      .then(data => {
          const messageEl = document.getElementById('voucher-message');
          const voucherInfoRow = document.getElementById('voucher-info');
          const discountTypeEl = document.getElementById('discount-type');
          const discountAmountEl = document.getElementById('discount-amount');
          const finalTotalEl = document.getElementById('final-total');
          const finalTotalInput = document.getElementById('final-total-input');
          const voucherCodeInput = document.getElementById('voucher_code');

          if (data.error) {
              messageEl.textContent = data.error;
              messageEl.classList.remove('text-success');
              messageEl.classList.add('text-danger');
              voucherInfoRow.style.display = 'none';
          } else {
              messageEl.textContent = "Voucher hợp lệ!";
              messageEl.classList.remove('text-danger');
              messageEl.classList.add('text-success');
              if (data.type === 'percentage') {
                  discountTypeEl.textContent = "Giảm " + Math.floor(data.voucherValue) + "%";
              } else {
                  discountTypeEl.textContent = "Giảm " + Math.floor(data.voucherValue).toLocaleString() + "₫";
              }
              discountAmountEl.textContent = "- " + Math.floor(data.discount).toLocaleString() + "₫";
              finalTotalEl.textContent = Math.floor(data.finalTotal).toLocaleString() + "₫";
              finalTotalInput.value = Math.floor(data.finalTotal);
              voucherCodeInput.value = data.voucher_code;
              voucherInfoRow.style.display = 'table-row';
          }
      })
      .catch(error => {
          console.error("Có lỗi xảy ra khi áp dụng voucher:", error);
      });
  });
</script>


@endsection
