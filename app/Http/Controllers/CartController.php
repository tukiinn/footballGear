<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            // Lấy giỏ hàng từ database nếu đã đăng nhập
            $cartItems = Cart::where('user_id', Auth::id())->with('product')->get()->map(function ($cart) {
                return [
                    'product_id' => $cart->product->id,
                    'name'       => $cart->product->product_name,
                    'price'      => $cart->product->discount_price ?? $cart->product->price, 
                    'so_luong'   => $cart->quantity,
                    'image'      => $cart->product->image,
                    'unit'       => $cart->product->unit,
                    'size'       => $cart->size,
                ];
            })->toArray();
        } else {
            // Nếu chưa đăng nhập, lấy từ session
            $cartItems = session('cart', []);
        }
    
        return view('cart.index', compact('cartItems'));
    }
 
    public function checkout()
    {
        // Kiểm tra nếu người dùng chưa đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thanh toán.');
        }
        // Lấy id của người dùng đang đăng nhập
        $userId = Auth::id();
    
        // Lấy giỏ hàng của người dùng, kèm theo thông tin sản phẩm (nếu đã định nghĩa quan hệ 'product')
        $cartItems = Cart::where('user_id', $userId)->with('product')->get();
    
        // Nếu giỏ hàng trống, chuyển hướng về trang giỏ hàng và thông báo lỗi
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn trống!');
        }
    
        // Tính tổng giá trị giỏ hàng (đảm bảo nhân số lượng)
        $cartTotal = $cartItems->sum(function($cart) {
            return ($cart->product->discount_price ?? $cart->product->price) * $cart->quantity;
        });
    
        return view('cart.checkout.payment', compact('cartItems', 'cartTotal'));
    }
    
    public function addToCart(Request $request, $id)
    {
        try {
            // Lấy số lượng từ input, nếu không có thì mặc định 1
            $quantity = $request->input('quantity', 1);
            
            // Validate số lượng (chỉ validate nếu có input)
            $request->validate([
                'quantity' => 'sometimes|integer|min:1',
            ]);
    
            // Lấy sản phẩm theo ID
            $product = Product::findOrFail($id);
    
            // Nếu sản phẩm có size (theo bảng product_sizes), validate size từ request
            if ($product->sizes()->exists()) {
                $allowedSizes = $product->sizes->pluck('size')->toArray();
                $size = $request->input('size');
                if (!in_array($size, $allowedSizes)) {
                    return redirect()->back()->with('error', 'Size không hợp lệ.');
                }
            } else {
                // Nếu sản phẩm không có size (ví dụ: bán theo đơn vị túi), set size rỗng
                $size = '';
            }
    
            if (Auth::check()) {
                $user = Auth::user();
    
                // Tìm mục giỏ hàng của user theo product_id và size
                $cartItem = Cart::where('user_id', $user->id)
                                ->where('product_id', $product->id)
                                ->where('size', $size)
                                ->first();
    
                if ($cartItem) {
                    // Nếu sản phẩm cùng size đã có, tăng số lượng theo giá trị từ input
                    $cartItem->quantity += $quantity;
                    $cartItem->save();
                } else {
                    // Nếu chưa có, tạo mới mục trong giỏ hàng
                    Cart::create([
                        'user_id'    => $user->id,
                        'product_id' => $product->id,
                        'quantity'   => $quantity,
                        'size'       => $size,
                    ]);
                }
            } else {
                // Với người dùng chưa đăng nhập, lưu giỏ hàng vào session
                $cart = session()->get('cart', []);
    
                // Sử dụng key kết hợp product_id và size (nếu có) để phân biệt các mục
                $cartKey = $size === '' ? (string)$id : $id . '-' . $size;
    
                if (isset($cart[$cartKey])) {
                    // Nếu mục đã tồn tại, tăng số lượng theo giá trị từ input
                    $cart[$cartKey]['so_luong'] += $quantity;
                } else {
                    // Nếu chưa tồn tại, thêm mới mục với số lượng từ input và lưu size
                    $cart[$cartKey] = [
                        'product_id'  => $product->id,
                        'name'        => $product->product_name,
                        'price'       => $product->discount_price ?? $product->price,
                        'so_luong'    => $quantity,
                        'unit'        => $product->unit,
                        'image'       => $product->image,
                        'description' => $product->description,
                        'category'    => $product->category->category_name,
                        'size'        => $size,
                        'created_at'  => now(), // Lưu thời gian thêm
                    ];
                }
            
                // Cập nhật session giỏ hàng
                session()->put('cart', $cart);
            }
    
            // Redirect về trang giỏ hàng với thông báo thành công
            return redirect()->route('cart.index')->with('success', 'Thêm sản phẩm vào giỏ hàng thành công!');
        } catch (\Exception $e) {
            Log::error('Lỗi khi thêm sản phẩm vào giỏ hàng: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi thêm sản phẩm vào giỏ hàng.');
        }
    }
    
    
    public function removeFromCart(Request $request, $productId)
    {
        // Lấy size từ request (nếu có)
        $size = $request->input('size', null);

        if (Auth::check()) {
            // Xóa sản phẩm khỏi giỏ hàng trong cơ sở dữ liệu
            $query = Cart::where('user_id', Auth::id())
                         ->where('product_id', $productId);
            if ($size) {
                // Nếu có size, chỉ xóa mục có đúng size đó
                $query->where('size', $size);
            }
            $query->delete();
        } else {
            // Xóa sản phẩm khỏi giỏ hàng lưu trong session (chưa đăng nhập)
            $cart = session()->get('cart', []);
            // Nếu có size, key được tạo theo định dạng "productId-size", ngược lại chỉ dùng productId
            $cartKey = $size ? $productId . '-' . $size : $productId;
            if (isset($cart[$cartKey])) {
                unset($cart[$cartKey]);
                session()->put('cart', $cart);
            }
        }

        return redirect()->route('cart.index')->with('success', 'Xóa sản phẩm khỏi giỏ hàng thành công!');
    }
}
