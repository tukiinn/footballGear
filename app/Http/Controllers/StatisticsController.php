<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    // Hàm tính toán thống kê hiển thị trên dashboard
    public function dashboard()
    {
        // Tổng số đơn hàng
        $totalOrders = Order::count();

        // Tổng số khách hàng riêng biệt
        $totalCustomers = User::whereHas('orders')->count();

        // Tổng số đơn hàng chưa hoàn thành (sử dụng cột trang_thai)
        $totalPendingOrders = Order::whereIn('trang_thai', ['pending', 'paid', 'confirmed'])->count();

        // Tổng số đơn hàng đã hoàn thành
        $totalCompletedOrders = Order::where('trang_thai', 'completed')->count();

        // Tổng số đơn hàng đã hủy
        $totalCancelledOrders = Order::where('trang_thai', 'cancelled')->count();

        // Doanh thu theo danh mục cho đơn hàng đã hoàn thành
        $revenueByCategoryCompleted = $this->getCategoryRevenueForChart();

        // Tổng doanh thu cho đơn hàng đã hoàn thành (dùng cột thanh_tien của bảng order_items)
        $totalRevenueCompleted = OrderItem::whereIn('order_id', Order::where('trang_thai', 'completed')->pluck('id'))
            ->sum('thanh_tien');

        // Tổng doanh thu cho đơn hàng chưa hoàn thành
        $totalRevenuePending = OrderItem::whereIn('order_id', Order::whereIn('trang_thai', ['pending', 'paid', 'confirmed'])->pluck('id'))
            ->sum('thanh_tien');

        // Doanh thu theo ngày cho đơn hàng đã hoàn thành
        $revenueByDayCompleted = OrderItem::select(
                DB::raw('DATE(created_at) as day'),
                DB::raw('SUM(thanh_tien) as revenue')
            )
            ->whereIn('order_id', Order::where('trang_thai', 'completed')->pluck('id'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        // Doanh thu theo tháng cho đơn hàng đã hoàn thành
        $revenueByMonthCompleted = OrderItem::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(thanh_tien) as revenue')
            )
            ->whereIn('order_id', Order::where('trang_thai', 'completed')->pluck('id'))
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

        // Doanh thu theo năm cho đơn hàng đã hoàn thành
        $revenueByYearCompleted = OrderItem::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(thanh_tien) as revenue')
            )
            ->whereIn('order_id', Order::where('trang_thai', 'completed')->pluck('id'))
            ->groupBy(DB::raw('YEAR(created_at)'))
            ->get();

        // Thống kê tiền voucher được áp dụng:
        // Tính hiệu giữa tổng giá trị đơn hàng (order_items.thanh_tien) và tổng giá trị đơn hàng (orders.tong_tien)
        // chỉ với các đơn hàng hoàn thành có voucher (voucher_id khác null)
        $totalVoucherDiscount = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->select(
                'orders.id',
                'orders.tong_tien',
                DB::raw('SUM(order_items.thanh_tien) as order_items_total')
            )
            ->where('orders.trang_thai', 'completed')
            ->whereNotNull('orders.voucher_id')
            ->groupBy('orders.id', 'orders.tong_tien')
            ->get()
            ->sum(function($order) {
                return $order->order_items_total - $order->tong_tien;
            });

        // Lấy danh sách danh mục
        $categories = Category::all();
        
        // Doanh thu nhận về sau khi voucher được áp dụng (lấy từ orders.tong_tien)
        $totalNetRevenueReceived = Order::where('trang_thai', 'completed')->sum('tong_tien');

        $bestSellingProducts = DB::table('order_items')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->select(
            'products.id as id',  // Thêm dòng này
            'products.product_name as name', 
            'products.image as image', 
            DB::raw('SUM(order_items.so_luong) as sold_quantity')
        )
        ->groupBy('products.id', 'products.product_name', 'products.image')
        ->orderBy('sold_quantity', 'desc')
        ->get();
    
    // Tính phần trăm dựa trên sản phẩm có số lượng bán cao nhất
    $maxSold = $bestSellingProducts->max('sold_quantity');
    $bestSellingProducts = $bestSellingProducts->map(function($item) use ($maxSold) {
        $item->percentage = $maxSold > 0 ? round(($item->sold_quantity / $maxSold) * 100, 2) : 0;
        return $item;
    });
    

       // *** Phần bổ sung: Top 5 Người mua hàng nhiều nhất ***
$topCustomersData = DB::table('orders')
->select('user_id', DB::raw('SUM(tong_tien) as total_spent'))
->where('trang_thai', 'completed')
->groupBy('user_id')
->orderBy('total_spent', 'desc')
->limit(5)
->get();

$topCustomers = $topCustomersData->map(function ($data) {
$customer = User::find($data->user_id);
if ($customer) {
    $customer->total_spent = $data->total_spent;
    // Tính tổng số sản phẩm đã mua của user này (từ order_items)
    $totalQuantity = DB::table('orders')
        ->join('order_items', 'orders.id', '=', 'order_items.order_id')
        ->where('orders.trang_thai', 'completed')
        ->where('orders.user_id', $customer->id)
        ->sum('order_items.so_luong');
    $customer->total_quantity = $totalQuantity;
}
return $customer;
});


        return view('admin.dashboard', compact(
            'totalOrders',
            'totalCustomers',
            'totalPendingOrders',
            'totalCompletedOrders',
            'totalCancelledOrders',
            'revenueByCategoryCompleted',
            'revenueByDayCompleted',
            'revenueByMonthCompleted',
            'revenueByYearCompleted',
            'totalRevenueCompleted',
            'totalRevenuePending',
            'totalVoucherDiscount',
            'categories',
            'totalNetRevenueReceived',
            'bestSellingProducts',
          'topCustomers' 
        ));
    }

    // Hàm lấy dữ liệu doanh thu theo thời gian (cho biểu đồ AJAX)
    public function getRevenueData(Request $request)
    {
        $timeframe = $request->query('timeframe', 'day');

        $data = $this->getRevenueByTimeframe($timeframe);

        return response()->json([
            'completed' => $data['completed'],
            'pending'   => $data['pending'],
        ]);
    }

    private function getRevenueByTimeframe($timeframe)
    {
        switch ($timeframe) {
            case 'day':
                return [
                    'completed' => OrderItem::selectRaw('DATE(created_at) as label, SUM(thanh_tien) as revenue')
                        ->whereIn('order_id', Order::where('trang_thai', 'completed')->pluck('id'))
                        ->groupBy('label')
                        ->get(),
                    'pending' => OrderItem::selectRaw('DATE(created_at) as label, SUM(thanh_tien) as revenue')
                        ->whereIn('order_id', Order::whereIn('trang_thai', ['pending', 'paid', 'confirmed'])->pluck('id'))
                        ->groupBy('label')
                        ->get(),
                ];
            case 'month':
                return [
                    'completed' => OrderItem::selectRaw('MONTH(created_at) as label, SUM(thanh_tien) as revenue')
                        ->whereIn('order_id', Order::where('trang_thai', 'completed')->pluck('id'))
                        ->groupBy('label')
                        ->get(),
                    'pending' => OrderItem::selectRaw('MONTH(created_at) as label, SUM(thanh_tien) as revenue')
                        ->whereIn('order_id', Order::whereIn('trang_thai', ['pending', 'paid', 'confirmed'])->pluck('id'))
                        ->groupBy('label')
                        ->get(),
                ];
            case 'year':
                return [
                    'completed' => OrderItem::selectRaw('YEAR(created_at) as label, SUM(thanh_tien) as revenue')
                        ->whereIn('order_id', Order::where('trang_thai', 'completed')->pluck('id'))
                        ->groupBy('label')
                        ->get(),
                    'pending' => OrderItem::selectRaw('YEAR(created_at) as label, SUM(thanh_tien) as revenue')
                        ->whereIn('order_id', Order::whereIn('trang_thai', ['pending', 'paid', 'confirmed'])->pluck('id'))
                        ->groupBy('label')
                        ->get(),
                ];
            default:
                return [];
        }
    }

    // Lấy doanh thu theo danh mục (sử dụng bảng order_items với các trường: id, order_id, product_id, name, gia, so_luong, size, thanh_tien, created_at, updated_at)
    public function getCategoryRevenueForChart()
    {
        return Product::join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.trang_thai', 'completed')
            ->select(
                'categories.category_name as category_name',
                DB::raw('SUM(order_items.thanh_tien) as revenue')
            )
            ->groupBy('categories.category_name')
            ->get();
    }
    
    public function getPaymentMethodRevenueData()
    {
        $paymentData = Order::select(
                DB::raw("COALESCE(phuong_thuc_thanh_toan, 'Unknown') as phuong_thuc_thanh_toan"),
                DB::raw('SUM(tong_tien) as revenue')
            )
            ->where('trang_thai', 'completed')
            ->groupBy(DB::raw("COALESCE(phuong_thuc_thanh_toan, 'Unknown')"))
            ->get();
    
        return response()->json($paymentData);
    }
}
