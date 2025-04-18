@extends('layouts.admin')
@section('title', 'Trang Thống Kê')
@section('content')

    <h1 class="text-center mb-3" style="font-size: 2.5rem; font-weight: bold; color: #343a40;">Bảng Thống Kê</h1>

    <style>
        body {
            background-color: #f9fafb;
        }

        .card {
            background-color: #ffffff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .card-header {
            background-color: #343a40;
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
            padding: 15px 20px;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .card-body {
            padding: 25px;
            font-size: 1.1rem;
        }

        .total-revenue {
            font-size: 1.5rem;
            color: #28a745;
            font-weight: bold;
        }
        .total-revenue-do {
            font-size: 1.5rem;
            color: #f70000;
            font-weight: bold;
        }

        .stats-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .stats-item {
            flex: 0 0 calc(25% - 20px);
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            transition: all 0.3s ease-in-out;
        }

        .stats-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .stats-item i {
            position: absolute;
            right: 20px;
            top: 70px;
            font-size: 1.5rem;
        }

        .stats-item .fa-check-circle {
            color: #28a745;
        }

        .stats-item .fa-times-circle {
            color: #dc3545;
        }

        .stats-item .fa-shopping-cart {
            color: #007bff;
        }

        .stats-item .fa-users {
            color: #ffc107;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table th, table td {
            text-align: left;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }

        table th {
            background-color: #f1f3f5;
            color: #343a40;
            font-weight: bold;
        }

        table td {
            color: #555555;
        }

        table tr:hover {
            background-color: #e9ecef;
        }

        .charts-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 20px;
        }

        .card.flex {
            flex: 1;
            min-width: 300px;
        }

        canvas {
            max-width: 100%;
            height: 300px;
        }

        /* Style cho progress bar của bảng sản phẩm bán chạy */
        .progress {
            background-color: #e9ecef;
            border-radius: 10px;
            height: 20px;
            overflow: hidden;
        }
        .progress-bar {
            height: 100%;
            line-height: 20px;
            color: #fff;
            text-align: center;
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            .stats-item {
                flex: 0 0 100%;
            }
            .charts-container {
                flex-direction: column;
            }
            h1 {
                font-size: 2rem;
            }
        }
    </style>

    <div class="container">
        <div class="container-stats">
            <div class="row stats-container">
                <div class="col-md-3 stats-item">
                    <h5>Tổng Số Đơn Hàng:</h5>
                    <p>{{ $totalOrders }}</p>
                    <i class="fas fa-shopping-cart" style="color: #007bff;"></i>
                </div>
                <div class="col-md-3 stats-item">
                    <h5>Tổng Số Khách Hàng:</h5>
                    <p>{{ $totalCustomers }}</p>
                    <i class="fas fa-users" style="color: #ffc107;"></i>
                </div>
                <div class="col-md-3 stats-item">
                    <h5>Tổng Doanh Thu (Đã Hoàn Thành):</h5>
                    <p class="total-revenue">{{ number_format($totalRevenueCompleted, 0, ',', '.') }} đ</p>
                    <i class="fas fa-wallet" style="color: #28a745;"></i>
                </div>
                <div class="col-md-3 stats-item">
                    <h5>Tổng Số Đơn Chưa Hoàn Thành:</h5>
                    <p>{{ $totalPendingOrders }}</p>
                    <i class="fas fa-hourglass-half" style="color: #17a2b8;"></i>
                </div>
                <div class="col-md-3 stats-item">
                    <h5>Tổng Số Đơn Đã Hoàn Thành:</h5>
                    <p>{{ $totalCompletedOrders }}</p>
                    <i class="fas fa-check-circle" style="color: #28a745;"></i>
                </div>
                <div class="col-md-3 stats-item">
                    <h5>Tổng Số Đơn Đã Hủy:</h5>
                    <p>{{ $totalCancelledOrders }}</p>
                    <i class="fas fa-ban" style="color: #dc3545;"></i>
                </div>
                <div class="col-md-3 stats-item">
                    <h5>Tổng Tiền Giảm Giá Voucher:</h5>
                    <p class="total-revenue-do">{{ number_format($totalVoucherDiscount, 0, ',', '.') }} đ</p>
                    <i class="fas fa-tag" style="color: #6f42c1;"></i>
                </div>
                <div class="col-md-3 stats-item">
                    <h5>Doanh Thu Nhận Về (Sau Voucher):</h5>
                    <p class="total-revenue">{{ number_format($totalNetRevenueReceived, 0, ',', '.') }} đ</p>
                    <i class="fas fa-money-bill-wave" style="color: #20c997;"></i>
                </div>
            </div>
        </div>

        <div class="charts-container mt-3">
            <div class="card flex">
                <div class="card-header">Doanh Thu Theo Thời Gian</div>
                <div class="card-body">
                    <form id="revenue-form" class="mb-4">
                        <div class="form-group">
                            <label for="timeframe">Chọn Thời Gian:</label>
                            <select id="timeframe" class="form-control" onchange="updateChart()">
                                <option value="day">Theo Ngày</option>
                                <option value="month">Theo Tháng</option>
                                <option value="year">Theo Năm</option>
                            </select>
                        </div>
                    </form>
                    <canvas id="revenueChart" width="300" height="150"></canvas>
                </div>
            </div>

            <div class="card flex">
                <div class="card-header">Doanh Thu Theo Danh Mục</div>
                <div class="card-body">
                    <canvas id="categoryChart" width="300" height="150"></canvas>
                </div>
            </div>

            <div class="card flex">
                <div class="card-header">Theo Phương Thức Thanh Toán</div>
                <div class="card-body">
                    <canvas id="paymentChart" width="300" height="150"></canvas>
                </div>
            </div>
        </div>

    <!-- Phần bổ sung: Sản phẩm bán chạy và Top 5 Người Mua Hàng Nhiều Nhất -->
    <div class="extra-stats">
        <div class="row">
<!-- Sản phẩm bán chạy -->
<div class="col-md-7">
    <div class="card">
        <div class="card-header">Sản Phẩm Bán Chạy</div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Sản Phẩm</th>
                        <th>SL</th>
                        <th>Phần trăm</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bestSellingProducts as $product)
                        <tr>
                            <td>
                                <img src="{{ asset($product->image) }}" 
                                     alt="{{ $product->name }}" 
                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                            </td>
                            <td>
                                <!-- Thêm link vào tên sản phẩm -->
                                <a href="{{ route('admin.products.show', $product->id) }}" class="product-link">
                                    {{ $product->name }}
                                </a>
                            </td>
                            <td>{{ $product->sold_quantity }}</td>
                            <td>
                                <div class="progress progress-custom">
                                    <div class="progress-bar progress-bar-custom" role="progressbar"
                                         style="width: {{ $product->percentage }}%;"
                                         aria-valuenow="{{ $product->percentage }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                        <span class="progress-text">{{ $product->percentage }}%</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- CSS tùy chỉnh -->
<style>
    .progress {
        background-color: #e9ecef;
        border-radius: 8px;
        height: 25px;
        overflow: hidden;
    }
    .progress-bar {
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: bold;
        font-size: 0.9rem;
    }
    .progress-bar-custom {
        background: linear-gradient(45deg, #007bff, #00c6ff);
        min-width: 28px; /* Đảm bảo text không bị quá hẹp khi % nhỏ */
    }
    .progress-text {
        z-index: 2;
    }
    /* Link sản phẩm: bỏ gạch chân, đổi màu và màu chữ hover */
    .product-link {
        color: #2c3e50;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }
    .product-link:hover {
        color: #fd3086;
        text-decoration: none;
    }
</style>


            <!-- Top 5 Người Mua Hàng Nhiều Nhất -->
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">Top 5 Người Mua Hàng Nhiều Nhất</div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Hạng</th>
                                    <th>ID</th>
                                    <th>Tên</th>
                                    <th>Tổng Tiền</th>
                                    <th>Tổng Sản Phẩm</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topCustomers as $index => $customer)
                                    @php
                                        $rank = $index + 1;
                                        $rankIcon = '';
                                        switch ($rank) {
                                            case 1:
                                                $rankIcon = '<i class="fas fa-gem" style="color: #b9f2ff;"></i>'; // Kim cương
                                                break;
                                            case 2:
                                                $rankIcon = '<i class="fas fa-medal" style="color: gold;"></i>'; // Vàng
                                                break;
                                            case 3:
                                                $rankIcon = '<i class="fas fa-medal" style="color: silver;"></i>'; // Bạc
                                                break;
                                            case 4:
                                                $rankIcon = '<i class="fas fa-medal" style="color: #cd7f32;"></i>'; // Đồng
                                                break;
                                            case 5:
                                                $rankIcon = '<i class="fas fa-trophy" style="color: #6c757d;"></i>'; // Trophy
                                                break;
                                            default:
                                                $rankIcon = '';
                                        }
                                    @endphp
                                    <tr>
                                        <td>{!! $rankIcon !!}</td>
                                        <td>{{ $customer->id }}</td>
                                        <td>{{ $customer->name }}</td>
                                        <td>{{ number_format($customer->total_spent, 0, ',', '.') }} đ</td>
                                        <td>{{ $customer->total_quantity }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>

<!-- Script Chart.js và các logic tương tự như cũ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const revenueCanvas = document.getElementById('revenueChart');
        const categoryCanvas = document.getElementById('categoryChart');
        const paymentCanvas = document.getElementById('paymentChart');

        if (!revenueCanvas || !categoryCanvas || !paymentCanvas) {
            console.error('Không tìm thấy một hoặc nhiều canvas cần thiết.');
            return;
        }

        const ctx = revenueCanvas.getContext('2d');
        const categoryCtx = categoryCanvas.getContext('2d');
        const paymentCtx = paymentCanvas.getContext('2d');

        let revenueChart;
        let categoryChart;
        let paymentChart;

        function updateChart() {
            const timeframe = document.getElementById('timeframe').value;

            // Fetch dữ liệu doanh thu theo thời gian
            fetch(`/admin/revenue-data?timeframe=${timeframe}`)
                .then(response => response.json())
                .then(data => {
                    const completedLabels = data.completed.map(item => item.label);
                    const completedRevenue = data.completed.map(item => item.revenue);

                    if (revenueChart) {
                        revenueChart.destroy();
                    }

                    revenueChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: completedLabels,
                            datasets: [{
                                label: 'Doanh thu (VNĐ)',
                                data: completedRevenue,
                                backgroundColor: 'rgba(40, 167, 69, 0.5)',
                                borderColor: 'rgba(40, 167, 69, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Lỗi khi lấy dữ liệu doanh thu:', error));

            // Custom colors cho biểu đồ danh mục
            const customColors = [
                '#FF5733',
                '#33FF57',
                '#3357FF',
                '#FF33A1',
                '#FFFF33',
                '#33FFF7',
                '#FF8C33'
            ];

            // Fetch dữ liệu doanh thu theo danh mục
            fetch('/admin/category-revenue-data')
                .then(response => response.json())
                .then(data => {
                    const categoryLabels = data.map(item => item.category_name);
                    const categoryRevenue = data.map(item => item.revenue);

                    if (categoryChart) {
                        categoryChart.destroy();
                    }

                    categoryChart = new Chart(categoryCtx, {
                        type: 'doughnut',
                        data: {
                            labels: categoryLabels,
                            datasets: [{
                                label: 'Doanh Thu Theo Danh Mục',
                                data: categoryRevenue,
                                backgroundColor: customColors,
                                borderColor: '#fff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            const formattedValue = new Intl.NumberFormat('vi-VN', {
                                                style: 'currency',
                                                currency: 'VND'
                                            }).format(tooltipItem.raw);
                                            return `${tooltipItem.label}: ${formattedValue}`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Lỗi khi lấy dữ liệu doanh thu theo danh mục:', error));
        }

        // Fetch dữ liệu doanh thu theo phương thức thanh toán
        fetch('/admin/payment-revenue-data')
            .then(response => response.json())
            .then(data => {
                const paymentLabels = data.map(item => {
                    const method = item.phuong_thuc_thanh_toan || 'Unknown';
                    switch (method) {
                        case 'COD':
                            return 'Thanh toán khi nhận hàng';
                        case 'momo':
                            return 'MoMo';
                        case 'momo_qr':
                            return 'MoMo(QR)';
                        default:
                            return method.charAt(0).toUpperCase() + method.slice(1);
                    }
                });

                const paymentRevenue = data.map(item => item.revenue);

                paymentChart = new Chart(paymentCtx, {
                    type: 'pie',
                    data: {
                        labels: paymentLabels,
                        datasets: [{
                            label: 'Doanh Thu Theo Phương Thức Thanh Toán',
                            data: paymentRevenue,
                            backgroundColor: paymentRevenue.map((_, index) => `hsl(${index * 50}, 70%, 50%)`),
                            borderColor: '#fff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        const formattedValue = new Intl.NumberFormat('vi-VN', {
                                            style: 'currency',
                                            currency: 'VND'
                                        }).format(tooltipItem.raw);
                                        return `${tooltipItem.label}: ${formattedValue}`;
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Lỗi khi lấy dữ liệu doanh thu theo phương thức thanh toán:', error));

        const timeframeInput = document.getElementById('timeframe');
        if (timeframeInput) {
            timeframeInput.addEventListener('change', updateChart);
        } else {
            console.error('Không tìm thấy phần tử timeframe.');
        }

        updateChart();
    });
</script>
@endsection
