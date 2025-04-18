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
            <li class="breadcrumb-item active" aria-current="page">Danh Sách Đơn Hàng</li>
        </ol>
    </nav>

    <!-- Search Section -->
    <div class="search-section mb-3 d-flex justify-content-end">
        <form method="GET" action="{{ route('admin.orders.index') }}">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm đơn hàng..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-secondary">
                    <i class="fas fa-search me-1"></i> Tìm kiếm
                </button>
            </div>
        </form>
    </div>

    @if($orders->count() > 0)
    <!-- Form dùng chung cho thao tác hàng loạt và thao tác cho 1 đơn -->
    <form id="bulkActionForm" action="{{ route('admin.orders.bulkConfirm') }}" method="POST">
        @csrf

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                        <tr>
                            <!-- Checkbox chọn tất cả -->
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>Mã</th>
                            <th>Tên khách hàng</th>
                            <th>Phương thức thanh toán</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                </thead>
                <tbody>
                    @php
                        // Mapping trạng thái đơn hàng
                        $statusMapping = [
                            'pending'   => 'Chờ xác nhận',
                            'confirmed' => 'Đã xác nhận',
                            'shipping'  => 'Đang giao hàng',
                            'completed' => 'Hoàn thành',
                            'cancelled' => 'Đã hủy',
                        ];

                        // Mapping trạng thái thanh toán
                        $paymentMapping = [
                            'pending' => 'Chưa thanh toán',
                            'paid'    => 'Đã thanh toán',
                        ];
                        
                        // Mapping phương thức thanh toán
                        $paymentMethodMapping = [
                            'COD'    => 'khi nhận hàng',
                            'Momo'   => 'Momo',
                            'VNPay'  => 'VNPay',
                            'Paypal' => 'Paypal',
                        ];
                    @endphp

                    @foreach($orders as $order)
                    <!-- Gán data-status vào mỗi hàng -->
                    <tr data-status="{{ $order->trang_thai }}">
                        <td>
                            <!-- Cho phép chọn nếu đơn hàng không phải completed hay cancelled -->
                            @if(!in_array($order->trang_thai, ['completed', 'cancelled']))
                                <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" class="order-checkbox">
                            @endif
                        </td>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->ten_khach_hang }}</td>
                        <td>
                            Thanh toán {{ $paymentMethodMapping[$order->phuong_thuc_thanh_toan] ?? $order->phuong_thuc_thanh_toan }}
                        </td>
                        <td>{{ number_format($order->tong_tien, 0) }} VND</td>
                        <td>
                            <!-- Badge trạng thái đơn hàng -->
                            <span class="badge 
                                @if($order->trang_thai == 'pending') bg-warning 
                                @elseif($order->trang_thai == 'confirmed') bg-primary 
                                @elseif($order->trang_thai == 'shipping') bg-info 
                                @elseif($order->trang_thai == 'completed') bg-success 
                                @elseif($order->trang_thai == 'cancelled') bg-danger 
                                @else bg-secondary @endif">
                                {{ $statusMapping[$order->trang_thai] ?? $order->trang_thai }}
                            </span>
                            <!-- Nếu phương thức thanh toán khác COD thì hiển thị trạng thái thanh toán -->
                            @if($order->phuong_thuc_thanh_toan != 'COD')
                                <span class="badge ms-2 
                                    @if($order->payment_status == 'pending') bg-secondary 
                                    @elseif($order->payment_status == 'paid') bg-success 
                                    @else bg-secondary @endif">
                                    {{ $paymentMapping[$order->payment_status] ?? $order->payment_status }}
                                </span>
                            @endif
                        </td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <!-- Icon Buttons -->
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-icon btn-info" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($order->trang_thai == 'pending')
                                <button type="button" class="btn btn-icon btn-success single-action-btn" title="Xác nhận"
                                    data-action="{{ route('admin.orders.bulkConfirm') }}"
                                    data-order-id="{{ $order->id }}">
                                    <i class="fas fa-check"></i>
                                </button>
                            @endif

                            @if($order->trang_thai == 'confirmed')
                                <button type="button" class="btn btn-icon btn-primary single-action-btn" title="Giao hàng"
                                    data-action="{{ route('admin.orders.bulkShip') }}"
                                    data-order-id="{{ $order->id }}">
                                    <i class="fas fa-truck"></i>
                                </button>
                            @endif

                            @if($order->trang_thai == 'shipping')
                                <button type="button" class="btn btn-icon btn-warning single-action-btn" title="Hoàn thành"
                                    data-action="{{ route('admin.orders.bulkComplete') }}"
                                    data-order-id="{{ $order->id }}">
                                    <i class="fas fa-check-double"></i>
                                </button>
                            @endif

                            @if(!in_array($order->trang_thai, ['completed', 'cancelled']))
                                <button type="button" class="btn btn-icon btn-danger single-action-btn" title="Hủy đơn hàng"
                                    data-action="{{ route('admin.orders.bulkCancel') }}"
                                    data-order-id="{{ $order->id }}">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Các nút thao tác hàng loạt (cho thao tác nhiều đơn cùng lúc) -->
        <div class="mt-3" id="bulkButtons" style="display: none;">
            <button type="button" class="btn btn-success bulk-action-btn" data-action="{{ route('admin.orders.bulkConfirm') }}">Xác nhận</button>
            <button type="button" class="btn btn-primary bulk-action-btn" data-action="{{ route('admin.orders.bulkShip') }}">Giao hàng</button>
            <button type="button" class="btn btn-warning bulk-action-btn" data-action="{{ route('admin.orders.bulkComplete') }}">Hoàn thành</button>
            <button type="button" class="btn btn-danger bulk-action-btn" data-action="{{ route('admin.orders.bulkCancel') }}">Hủy đơn hàng</button>
        </div>
    </form>

    <!-- Phân trang -->
    @if ($orders->hasPages())
        <nav>
            <ul class="pagination justify-content-center mt-3">
                {{-- Nút "Trang trước" --}}
                @if ($orders->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">«</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $orders->previousPageUrl() }}" rel="prev">«</a>
                    </li>
                @endif

                {{-- Hiển thị danh sách số trang --}}
                @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                    @if ($page == $orders->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach

                {{-- Nút "Trang tiếp" --}}
                @if ($orders->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $orders->nextPageUrl() }}" rel="next">»</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">»</span>
                    </li>
                @endif
            </ul>
        </nav>
    @endif

    @else
    <div>
        Không có đơn hàng nào.
    </div>
    @endif

</div>

<!-- JavaScript: xử lý checkbox, bulk action và single action -->
<script>
    // Khi bấm nút single action, tự động chọn checkbox của đơn đó, cập nhật action form và submit
    function singleOrderAction(button) {
        const orderId = button.getAttribute('data-order-id');
        const row = button.closest('tr');
        const checkbox = row.querySelector('input[name="order_ids[]"]');
        if(checkbox) {
            checkbox.checked = true;
        }
        const actionUrl = button.getAttribute('data-action');
        document.getElementById('bulkActionForm').setAttribute('action', actionUrl);
        document.getElementById('bulkActionForm').submit();
    }

    document.querySelectorAll('.single-action-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            singleOrderAction(this);
        });
    });

    function updateBulkActionButtons() {
        const selectedCheckboxes = document.querySelectorAll('input[name="order_ids[]"]:checked');
        const bulkButtonsContainer = document.getElementById('bulkButtons');
        if(selectedCheckboxes.length === 0){
            bulkButtonsContainer.style.display = 'none';
            return;
        }
        const statuses = new Set();
        selectedCheckboxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            if(row){
                statuses.add(row.getAttribute('data-status'));
            }
        });
        if(statuses.size > 1){
            bulkButtonsContainer.style.display = 'none';
            return;
        }
        const commonStatus = statuses.values().next().value;
        document.querySelectorAll('.bulk-action-btn').forEach(btn => {
            btn.style.display = 'none';
        });
        if(commonStatus === 'pending'){
            document.querySelector('.bulk-action-btn[data-action="{{ route('admin.orders.bulkConfirm') }}"]').style.display = 'inline-block';
            document.querySelector('.bulk-action-btn[data-action="{{ route('admin.orders.bulkCancel') }}"]').style.display = 'inline-block';
        } else if(commonStatus === 'confirmed'){
            document.querySelector('.bulk-action-btn[data-action="{{ route('admin.orders.bulkShip') }}"]').style.display = 'inline-block';
            document.querySelector('.bulk-action-btn[data-action="{{ route('admin.orders.bulkCancel') }}"]').style.display = 'inline-block';
        } else if(commonStatus === 'shipping'){
            document.querySelector('.bulk-action-btn[data-action="{{ route('admin.orders.bulkComplete') }}"]').style.display = 'inline-block';
            document.querySelector('.bulk-action-btn[data-action="{{ route('admin.orders.bulkCancel') }}"]').style.display = 'inline-block';
        } else {
            bulkButtonsContainer.style.display = 'none';
            return;
        }
        bulkButtonsContainer.style.display = 'block';
    }

    document.getElementById('selectAll').addEventListener('change', function(){
        const checkboxes = document.querySelectorAll('input[name="order_ids[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActionButtons();
    });

    document.querySelectorAll('input[name="order_ids[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActionButtons);
    });

    document.querySelectorAll('.bulk-action-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const actionUrl = this.getAttribute('data-action');
            document.getElementById('bulkActionForm').setAttribute('action', actionUrl);
            document.getElementById('bulkActionForm').submit();
        });
    });
</script>

<!-- CSS Tùy Chỉnh -->
<style>
    .container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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
    /* Tiêu đề */
    h2 {
        font-size: 1.75rem;
        font-weight: 600;
        color: #343a40;
        margin-bottom: 20px;
    }
    /* Table */
    .table {
        margin-bottom: 0;
    }
    .table thead {
        background-color: #007bff;
        color: #fff;
    }
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0,0,0,.05);
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,.075);
    }
    /* Pagination */
    .pagination .page-link { }
    .pagination .active .page-link {
        background-color: #007bff;
        border-color: #007bff;
    }
    /* CSS cho search section */
    .search-section .input-group {
        border: 1px solid #ff4dab;
        border-radius: 50px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        max-width: 400px;
    }
    .search-section .form-control {
        border: none;
        padding: 12px 15px;
    }
    .search-section .form-control:focus {
        box-shadow: none;
        outline: none;
    }
    .search-section .btn {
        padding: 12px 20px;
        background-color: #ff4dab;
        border: none;
        color: #fff;
        transition: background 0.3s ease;
    }
    .search-section .btn:hover {
        background-color: #0056b3;
    }
    /* Nút Icon cho hành động */
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
    .btn-icon:hover {
        transform: scale(1.1);
    }
    .btn-info {
        background-color: #17a2b8;
    }
    .btn-success {
        background-color: #28a745;
    }
    .btn-primary {
        background-color: #007bff;
    }
    .btn-warning {
        background-color: #ffc107;
    }
    .btn-danger {
        background-color: #dc3545;
    }
</style>
@endsection
