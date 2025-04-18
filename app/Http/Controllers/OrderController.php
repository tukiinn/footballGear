<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    // Hiển thị danh sách đơn hàng của người dùng hiện tại  
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để xem đơn hàng.');
        }
        $userId = Auth::id();
        $orders = Order::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(6);

        return view('orders.index', compact('orders'));
    }

    public function createOrder(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để mua hàng.');
        }
        $userId = Auth::id();

        // Validate input
        $validated = $request->validate([
            'ten_khach_hang'  => 'required|string|min:6|max:20',
            'so_dien_thoai'   => 'required|digits:10|numeric',
            'dia_chi'         => 'required|string|max:255',
        ], [
            'ten_khach_hang.required' => 'Tên khách hàng là bắt buộc.',
            'ten_khach_hang.min'      => 'Tên khách hàng phải có ít nhất 6 ký tự.',
            'ten_khach_hang.max'      => 'Tên khách hàng không được vượt quá 20 ký tự.',
            'so_dien_thoai.required'  => 'Số điện thoại là bắt buộc.',
            'so_dien_thoai.digits'     => 'Số điện thoại phải có đúng 10 chữ số.',
            'so_dien_thoai.numeric'   => 'Số điện thoại chỉ được chứa các số.',
            'dia_chi.required'        => 'Địa chỉ là bắt buộc.',
            'dia_chi.max'             => 'Địa chỉ không được vượt quá 255 ký tự.',
        ]);

        // Lấy giỏ hàng của user
        $cartItems = Cart::where('user_id', $userId)->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống.');
        }

        // Kiểm tra số lượng tồn kho theo product_sizes
        $errors = [];
        foreach ($cartItems as $cart) {
            $product = Product::find($cart->product_id);
            if (!$product) {
                $errors[] = 'Sản phẩm không xác định.';
                continue;
            }
            // Lấy thông tin kích cỡ từ product_sizes
            $productSize = $product->sizes()->where('size', $cart->size)->first();
            if (!$productSize) {
                $errors[] = 'Không tìm thấy kích cỡ ' . $cart->size . ' cho sản phẩm ' . $product->product_name;
                continue;
            }
            if ($productSize->quantity < $cart->quantity) {
                $errors[] = 'Sản phẩm ' . $product->product_name . ' không đủ số lượng trong kích cỡ ' . $cart->size;
            }
        }
        if (!empty($errors)) {
            return redirect()->route('cart.index')->with('error', implode('<br>', $errors));
        }

        $finalTotal = $request->input('final_total');
        $voucherCode = $request->input('voucher_code');

        try {
            DB::transaction(function() use ($request, $validated, $userId, $cartItems, $finalTotal, $voucherCode, &$order) {
                // Tạo đơn hàng với trạng thái pending
                $order = Order::create([
                    'user_id'                => $userId,
                    'ten_khach_hang'         => $validated['ten_khach_hang'],
                    'so_dien_thoai'          => $validated['so_dien_thoai'],
                    'dia_chi'                => $validated['dia_chi'],
                    'tong_tien'              => $finalTotal,
                    'phuong_thuc_thanh_toan' => $request->phuong_thuc_thanh_toan,
                    'trang_thai'             => 'pending',
                    'payment_status'         => 'pending'
                ]);

                // Xử lý voucher nếu có
                if ($voucherCode) {
                    Log::info("Processing voucher in createOrder", ['voucher_code' => $voucherCode]);
                    $voucher = Voucher::where('code', $voucherCode)->lockForUpdate()->first();
                    if (!$voucher) {
                        throw new \Exception('Voucher không hợp lệ!');
                    }
                    if ($voucher->used >= $voucher->max_usage) {
                        throw new \Exception('Voucher đã hết lượt sử dụng!');
                    }
                    Log::info("Voucher before increment", ['used' => $voucher->used]);
                    $voucher->increment('used');
                    $voucher->refresh();
                    Log::info("Voucher after increment", ['used' => $voucher->used]);
                    $order->voucher_id = $voucher->id;
                    $order->save();
                }

                // Thêm sản phẩm vào đơn hàng và cập nhật số lượng trong product_sizes
                foreach ($cartItems as $cart) {
                    $product = Product::find($cart->product_id);
                    if (!$product) {
                        continue;
                    }
                    $productSize = $product->sizes()->where('size', $cart->size)->first();
                    if (!$productSize) {
                        continue;
                    }
                    if ($productSize->quantity < $cart->quantity) {
                        throw new \Exception('Sản phẩm ' . $product->product_name . ' không đủ số lượng trong kích cỡ ' . $cart->size);
                    }
                    // Cập nhật số lượng tồn kho trong product_sizes
                    $productSize->quantity -= $cart->quantity;
                    $productSize->save();

                    // Tính giá: ưu tiên discount_price nếu có
                    $basePrice = $product->discount_price ?? $product->price;

                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $product->id,
                        'name'       => $product->product_name,
                        'gia'        => $basePrice,
                        'so_luong'   => $cart->quantity,
                        'size'       => $cart->size,
                        'thanh_tien' => $basePrice * $cart->quantity,
                    ]);
                }

                if ($finalTotal <= 0) {
                    $order->payment_status = 'paid';
                    $order->save();
                }

                // Xóa giỏ hàng của người dùng sau khi tạo đơn hàng thành công
                Cart::where('user_id', $userId)->delete();
            });

            $order = Order::with('orderItems.product')->find($order->id);
            return view('cart.checkout.thankyouCOD', compact('order'));
        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }
    }

    // Hiển thị chi tiết đơn hàng
    public function showOrder($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để xem chi tiết đơn hàng.');
        }
        $userId = Auth::id();
        $order = Order::with('voucher')->where('id', $id)->where('user_id', $userId)->firstOrFail();
        $orderItems = OrderItem::where('order_id', $order->id)->get();

        $discountText = null;
        if ($order->voucher) {
            if ($order->voucher->type === 'percentage') {
                $discountText = '-' . $order->voucher->discount . '%';
            } else {
                $discountText = '-' . number_format($order->voucher->discount, 0) . '₫';
            }
        }

        return view('orders.show', compact('order', 'orderItems', 'discountText'));
    }

    public function cancelOrder(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để hủy đơn hàng.');
        }
        $userId = Auth::id();
        $order = Order::with('orderItems')->where('id', $id)->where('user_id', $userId)->first();
        if (!$order) {
            return redirect()->route('orders.index')->with('error', 'Đơn hàng không tồn tại.');
        }

        // Chỉ cho phép hủy nếu đơn hàng đang ở trạng thái pending và phương thức thanh toán không phải VNPay
        if ($order->trang_thai !== 'pending' || $order->phuong_thuc_thanh_toan == 'VNPay') {
            return redirect()->route('orders.show', $order->id)
                             ->with('error', 'Đơn hàng này không được phép hủy.');
        }

        try {
            DB::transaction(function() use ($order) {
                $order->trang_thai = 'cancelled';
                $order->save();

                foreach ($order->orderItems as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        // Lấy bản ghi product_size tương ứng theo size
                        $productSize = $product->sizes()->where('size', $item->size)->first();
                        if ($productSize) {
                            $productSize->quantity += $item->so_luong;
                            $productSize->save();
                        }
                    }
                }

                if ($order->voucher_id) {
                    $voucher = Voucher::find($order->voucher_id);
                    if ($voucher && $voucher->used > 0) {
                        $voucher->decrement('used');
                    }
                }
            });

            return redirect()->route('orders.show', $order->id)
                             ->with('success', 'Đơn hàng đã được hủy thành công.');
        } catch (\Exception $e) {
            return redirect()->route('orders.show', $order->id)
                             ->with('error', 'Có lỗi xảy ra khi hủy đơn hàng: ' . $e->getMessage());
        }
    }
}
