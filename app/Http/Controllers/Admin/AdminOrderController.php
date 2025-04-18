<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminOrderController extends Controller
{
    // Hiển thị danh sách đơn hàng
    public function index(Request $request)
    {
        // Khởi tạo query cho đơn hàng
        $query = Order::query();
    
        // Nếu có từ khóa tìm kiếm, lọc theo id hoặc tên khách hàng
        if ($search = $request->input('search')) {
            $query->where('id', 'like', '%' . $search . '%')
                  ->orWhere('ten_khach_hang', 'like', '%' . $search . '%');
        }
    
        // Sắp xếp theo ngày tạo giảm dần và phân trang 12 đơn hàng mỗi trang
        $orders = $query->orderBy('created_at', 'desc')->paginate(12);
    
        return view('admin.orders.index', compact('orders'));
    }
    
    
    // Hiển thị chi tiết đơn hàng
    public function show($id)
    {
        $order = Order::findOrFail($id);
        $orderItems = OrderItem::where('order_id', $order->id)->get();
        return view('admin.orders.show', compact('order', 'orderItems'));
    }
    
    // Xác nhận đơn hàng (đơn hàng COD hoặc các đơn hàng đang ở trạng thái pending)
    public function confirm(Order $order)
    {
        if ($order->trang_thai !== 'pending') {
            return redirect()->back()->with('error', 'Đơn hàng này không thể xác nhận');
        }
    
        $order->trang_thai = 'confirmed';
        $order->save();
    
        return redirect()->back()->with('success', 'Đơn hàng xác nhận thành công!');
    }
    
    // Giao hàng đơn hàng
    public function ship(Order $order)
    {
        if ($order->trang_thai !== 'confirmed' || ($order->phuong_thuc_thanh_toan !== 'cod' && $order->payment_status !== 'paid')) {
            return redirect()->back()->with('error', 'Đơn hàng này không thể shipped!');
        }
        $order->trang_thai = 'shipping';
        $order->save();
    
        return redirect()->back()->with('success', 'Đơn hàng đã được chuyển sang trạng thái giao hàng.');
    }
    
    // Hoàn thành đơn hàng
    public function complete(Order $order)
    {
        if ($order->trang_thai !== 'shipping') {
            return redirect()->back()->with('error', 'Đơn hàng này không thể completed!');
        }
    
        $order->trang_thai = 'completed';
        $order->save();
    
        if ($order->user) {
            $user = $order->user;
            $user->remaining_spins += 1;
            $user->save();
        }
    
        return redirect()->back()->with('success', 'Đơn hàng đã hoàn thành! Người dùng được +1 lượt quay.');
    }
    
    // Hủy đơn hàng
    public function cancel(Order $order)
    {
        if (in_array($order->trang_thai, ['completed', 'cancelled'])) {
            return redirect()->back()->with('error', 'Đơn hàng không thể hủy');
        }
    
        $order->trang_thai = 'cancelled';
        $order->save();
    
        return redirect()->back()->with('success', 'Đơn hàng đã được hủy!');
    }
    
    // Bulk Actions
    
    // Bulk Confirm: Xác nhận nhiều đơn hàng cùng lúc (chỉ đơn hàng đang ở trạng thái pending)
    public function bulkConfirm(Request $request)
    {
        $orderIds = $request->input('order_ids');
    
        if (empty($orderIds)) {
            return redirect()->back()->with('error', 'Không có đơn hàng nào được chọn.');
        }
    
        $orders = Order::whereIn('id', $orderIds)
                    ->where('trang_thai', 'pending')
                    ->get();
    
        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Không có đơn hàng nào ở trạng thái chờ xác nhận.');
        }
    
        foreach ($orders as $order) {
            $order->trang_thai = 'confirmed';
            $order->save();
        }
    
        return redirect()->back()->with('success', count($orders) . ' đơn hàng đã được xác nhận thành công!');
    }
    
    // Bulk Ship: Giao hàng nhiều đơn hàng cùng lúc
    public function bulkShip(Request $request)
    {
        $orderIds = $request->input('order_ids');
    
        if (empty($orderIds)) {
            return redirect()->back()->with('error', 'Không có đơn hàng nào được chọn.');
        }
    
        $orders = Order::whereIn('id', $orderIds)
                    ->where('trang_thai', 'confirmed')
                    ->where(function($query) {
                        $query->where('phuong_thuc_thanh_toan', 'cod')
                              ->orWhere(function($query) {
                                  $query->where('phuong_thuc_thanh_toan', '!=', 'cod')
                                        ->where('payment_status', 'paid');
                              });
                    })->get();
    
        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Không có đơn hàng nào đủ điều kiện chuyển sang trạng thái giao hàng.');
        }
    
        foreach ($orders as $order) {
            $order->trang_thai = 'shipping';
            $order->save();
        }
    
        return redirect()->back()->with('success', count($orders) . ' đơn hàng đã được chuyển sang trạng thái giao hàng.');
    }
    
    // Bulk Complete: Hoàn thành nhiều đơn hàng cùng lúc (chỉ đơn hàng ở trạng thái shipping)
    public function bulkComplete(Request $request)
    {
        $orderIds = $request->input('order_ids');
    
        if (empty($orderIds)) {
            return redirect()->back()->with('error', 'Không có đơn hàng nào được chọn.');
        }
    
        $orders = Order::whereIn('id', $orderIds)
                    ->where('trang_thai', 'shipping')
                    ->get();
    
        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Không có đơn hàng nào ở trạng thái giao hàng.');
        }
    
        foreach ($orders as $order) {
            $order->trang_thai = 'completed';
            $order->save();
    
            if ($order->user) {
                $user = $order->user;
                $user->save();
            }
        }
    
        return redirect()->back()->with('success', count($orders) . ' đơn hàng đã hoàn thành.');
    }
    
    // Bulk Cancel: Hủy nhiều đơn hàng cùng lúc (chỉ đơn hàng chưa hoàn thành và chưa bị hủy)
    public function bulkCancel(Request $request)
    {
        $orderIds = $request->input('order_ids');
    
        if (empty($orderIds)) {
            return redirect()->back()->with('error', 'Không có đơn hàng nào được chọn.');
        }
    
        $orders = Order::whereIn('id', $orderIds)
                    ->whereNotIn('trang_thai', ['completed', 'cancelled'])
                    ->get();
    
        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Không có đơn hàng nào đủ điều kiện hủy.');
        }
    
        foreach ($orders as $order) {
            $order->trang_thai = 'cancelled';
            $order->save();
        }
    
        return redirect()->back()->with('success', count($orders) . ' đơn hàng đã được hủy.');
    }
}
