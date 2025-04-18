<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Voucher;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class OnlPaymentController extends Controller
{
    private function createOrder($request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để mua hàng.');
        }
        $userId = Auth::id();
    
        // Xác thực dữ liệu đầu vào
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
    
        // Lấy phương thức thanh toán từ form
        $paymentMethod = $request->input('phuong_thuc_thanh_toan');
    
        // Lấy giỏ hàng của người dùng
        $cartItems = Cart::where('user_id', $userId)->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống.');
        }
    
        // Kiểm tra số lượng sản phẩm theo product_sizes
        $errors = [];
        foreach ($cartItems as $cart) {
            $product = Product::find($cart->product_id);
            if (!$product) {
                $errors[] = 'Sản phẩm không xác định.';
                continue;
            }
            // Lấy bản ghi product_sizes theo product_id và size
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
            DB::transaction(function() use ($request, $validated, $userId, $cartItems, $finalTotal, $voucherCode, $paymentMethod, &$order) {
                // Tạo đơn hàng với trạng thái "pending"
                $order = Order::create([
                    'user_id'                => $userId,
                    'ten_khach_hang'         => $validated['ten_khach_hang'],
                    'so_dien_thoai'          => $validated['so_dien_thoai'],
                    'dia_chi'                => $validated['dia_chi'],
                    'tong_tien'              => $finalTotal,
                    'phuong_thuc_thanh_toan' => $paymentMethod,
                    'trang_thai'             => 'pending',
                    'payment_status'         => 'pending',
                ]);
    
                // Xử lý voucher nếu có
                if ($voucherCode) {
                    Log::info("Processing voucher", ['voucher_code' => $voucherCode]);
                    $voucher = Voucher::where('code', $voucherCode)->lockForUpdate()->first();
                    if (!$voucher) {
                        throw new \Exception('Voucher không hợp lệ!');
                    }
                    if ($voucher->used >= $voucher->max_usage) {
                        throw new \Exception('Voucher đã hết lượt sử dụng!');
                    }
                    Log::info("Voucher trước khi increment", ['used' => $voucher->used]);
                    $voucher->increment('used');
                    $voucher->refresh();
                    Log::info("Voucher sau khi increment", ['used' => $voucher->used]);
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
                        throw new \Exception('Không tìm thấy kích cỡ ' . $cart->size . ' cho sản phẩm ' . $product->product_name);
                    }
                    if ($productSize->quantity < $cart->quantity) {
                        throw new \Exception('Sản phẩm ' . $product->product_name . ' không đủ số lượng trong kích cỡ ' . $cart->size);
                    }
                    // Cập nhật số lượng tồn kho trong product_sizes
                    $productSize->quantity -= $cart->quantity;
                    $productSize->save();
    
                    // Tính giá: ưu tiên discount_price nếu có
                    $basePrice = $product->discount_price ?? $product->price;
    
                    // Tạo chi tiết đơn hàng
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
    
                // Nếu sau khi áp voucher, tổng tiền đơn hàng bằng 0, đánh dấu đơn hàng là "paid"
                if ($finalTotal <= 0) {
                    $order->payment_status = 'paid';
                    $order->save();
                }
    
                // Xóa giỏ hàng sau khi đơn hàng được tạo thành công
                Cart::where('user_id', $userId)->delete();
            });
    
            return [
                'order_id'     => $order->id,
                'total_amount' => $order->tong_tien,
            ];
        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }
    }
    



    public function vnpayment(Request $request) {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thanh toán.');
        }
    
        $orderData = $this->createOrder($request);

$order_id = $orderData['order_id'];
$totalAmount = $orderData['total_amount'];
    
if (!$order_id) {
    return redirect()->route('cart.index')->with('error', 'Dữ liệu thanh toán không hợp lệ.');
}

if ($totalAmount <= 0) {
    return redirect()->route('thankyouvnpay', ['order_id' => $order_id]);
}

// Nếu $totalAmount > 0, xử lý thanh toán bình thường (ví dụ chuyển sang VNPay)

    
        // Cấu hình VNPay
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = route('vnpay.return', ['order_id' => $order_id]); // Gửi order_id khi trả về
        $vnp_TmnCode = "Z4TFCUTA"; // Mã website tại VNPay
        $vnp_HashSecret = "YZH3Y12YNHEMI6FKI7AIEANDEJVG0QIM"; // Chuỗi bí mật
    
        $vnp_TxnRef = $order_id; // Dùng order_id làm mã giao dịch
        $vnp_OrderInfo = 'Thanh toán đơn hàng #' . $order_id;
        $vnp_Amount = $totalAmount * 100; // VNPay yêu cầu nhân 100
        $vnp_OrderType = 'billpayment';
        $vnp_Locale = 'vn';
        $vnp_BankCode = '';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
    
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            
        );
    
        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = $vnp_Bill_State;
        }
        

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        
        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);//  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $returnData = array('code' => '00'
            , 'message' => 'success'
            , 'data' => $vnp_Url);
            if (isset($_POST['redirect'])) {
                header('Location: ' . $vnp_Url);
                die();
            } else {
                echo json_encode($returnData);
            }
}
            
        
    
    public function vnpayReturn(Request $request) {
        $order_id = $request->input('order_id'); // Lấy order_id từ URL
        $vnp_ResponseCode = $request->input('vnp_ResponseCode'); // Mã phản hồi
        $vnp_Amount = $request->input('vnp_Amount') / 100; // Chia lại 100 để lấy số tiền thực
    
        if ($vnp_ResponseCode == "00") {
            // Thanh toán thành công -> Cập nhật trạng thái đơn hàng
            Order::where('id', $order_id)->update(['payment_status' => 'paid']);
            return redirect()->route('thankyouvnpay', ['order_id' => $order_id]);
        } else {
            return redirect()->route('cart.index')->with('error', 'Thanh toán thất bại, vui lòng thử lại.');
        }
        
    }
    
    public function checkoutthank(Request $request) {
        $order_id = $request->input('order_id');
        $order = Order::with('orderItems.product')->where('id', $order_id)->first();
    
        if (!$order) {
            return redirect()->route('cart.index')->with('error', 'Không tìm thấy đơn hàng!');
        }
        $totalSubtotal = $order->orderItems->sum('thanh_tien');
        return view('cart.checkout.thankyouvnpay', compact('order', 'totalSubtotal'));
    }

    public function retryvnpayment(Request $request) {
        Log::info("VNPay initiated for order ID: {$request->order_id}");
    
        if (!Auth::check()) {
            Log::error("User not authenticated");
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thanh toán.');
        }
    
        $order_id = $request->input('order_id');
        $totalAmount = Order::find($order_id)->tong_tien;
    
        if (!$totalAmount || $totalAmount <= 0 || !$order_id) {
            Log::error("Invalid payment data for order ID: {$order_id}");
            return redirect()->route('cart.index')->with('error', 'Dữ liệu thanh toán không hợp lệ.');
        }
    
        Log::info("VNPay Configuration for order ID: {$order_id}");
    
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = route('vnpay.return', ['order_id' => $order_id]);
        $vnp_TmnCode = "Z4TFCUTA";
        $vnp_HashSecret = "YZH3Y12YNHEMI6FKI7AIEANDEJVG0QIM";
    
        $vnp_TxnRef = $order_id;
        $vnp_OrderInfo = 'Thanh toán đơn hàng #' . $order_id;
        $vnp_Amount = $totalAmount * 100;
        $vnp_OrderType = 'billpayment';
        $vnp_Locale = 'vn';
        $vnp_BankCode = '';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
    
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );
    
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
    
        $vnp_Url = $vnp_Url . "?" . $query;
        Log::info("VNPay URL: {$vnp_Url}");
    
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
            Log::info("VNPay Secure Hash: {$vnpSecureHash}");
        }
    
        // Chuyển hướng đến URL VNPay
        return redirect()->away($vnp_Url);
    }
    public function momopayment(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thanh toán.');
        }
    
        // Tạo đơn hàng (hàm createOrder cần nhận $request)
        $orderData = $this->createOrder($request);
        $order_id = $orderData['order_id'];
        $totalAmount = $orderData['total_amount'];
    
        if (!$order_id) {
            return redirect()->route('cart.index')->with('error', 'Dữ liệu thanh toán không hợp lệ.');
        }
    
        // Nếu tổng tiền đơn hàng <= 0, chuyển ngay đến trang thankyoumomo
        if ($totalAmount <= 0) {
            return redirect()->route('thankyoumomo', ['order_id' => $order_id]);
        }

        // Chuẩn bị tham số thanh toán cho MoMo
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
    
        $partnerCode = 'MOMOBKUN20180529';
        $accessKey   = 'klm05TvNBzhg7h7j';
        $secretKey   = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
        $orderInfo   = "Thanh toán qua MoMo";
        
        // Sử dụng tổng tiền đơn hàng cho amount
        $amount = $totalAmount;
        
        // Tạo orderId độc nhất: kết hợp order_id và timestamp
        $orderId = $order_id ;
        
        // Redirect URL và IPN URL: cần cấu hình route phù hợp trong dự án của bạn
        $redirectUrl = route('thankyoumomo', ['order_id' => $order_id]);
        $ipnUrl      = route('thankyoumomo', ['order_id' => $order_id]);
        
        $extraData   = ""; // Nếu có dữ liệu thêm, truyền vào đây
    
        $requestId   = time() . "";
        $requestType = "payWithMethod"; // Có thể thay đổi theo phương thức thanh toán mà bạn muốn sử dụng
    
        // Tạo chuỗi rawHash theo định dạng của MoMo
        $rawHash = "accessKey=" . $accessKey .
                   "&amount=" . $amount .
                   "&extraData=" . $extraData .
                   "&ipnUrl=" . $ipnUrl .
                   "&orderId=" . $orderId .
                   "&orderInfo=" . $orderInfo .
                   "&partnerCode=" . $partnerCode .
                   "&redirectUrl=" . $redirectUrl .
                   "&requestId=" . $requestId .
                   "&requestType=" . $requestType;
        
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
    
        // Chuẩn bị dữ liệu gửi đi
        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            "storeId"     => "MomoTestStore",
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl'      => $ipnUrl,
            'lang'        => 'vi',
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature
        ];
    
        // Gửi yêu cầu thanh toán qua MoMo bằng cURL
        $result = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);
        Log::info('MoMo Payment Response: ', ['response' => $result]);
    
        if (isset($jsonResult['payUrl'])) {
            // Chuyển hướng người dùng đến trang thanh toán của MoMo
            return redirect($jsonResult['payUrl']);
        }
    
        return redirect()->route('cart.index')->with('error', 'Đã xảy ra lỗi trong quá trình thanh toán với MoMo.');
    }
    
    private function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Tăng thời gian chờ nếu cần
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Tăng thời gian kết nối nếu cần
    
        $result = curl_exec($ch);
    
        // Xử lý lỗi cURL
        if (curl_errno($ch)) {
            $errorMessage = curl_error($ch);
            Log::error("cURL Error: " . $errorMessage);
            return json_encode(['error' => 'Đã xảy ra lỗi khi kết nối đến dịch vụ thanh toán.']);
        }
    
        curl_close($ch);
        return $result;
    }
    public function thankmomo(Request $request)
    {
        
        // Kiểm tra kết quả giao dịch
        $resultCode = $request->input('resultCode'); // Lấy mã kết quả từ request
        $orderId = $request->input('orderId'); // Lấy ID đơn hàng từ request
        $order = Order::with('orderItems.product')->where('id', $orderId)->first();
    

        // Kiểm tra nếu giao dịch thành công
        if ($resultCode === '0') {
            // Cập nhật trạng thái đơn hàng
            $order = Order::find($orderId); // Tìm đơn hàng theo ID
            if ($order) {
                $order->payment_status = 'paid'; // Cập nhật trạng thái
                $order->save(); // Lưu thay đổi
            }
            $totalSubtotal = $order->orderItems->sum('thanh_tien');
            // Trả về view cảm ơn
            return view('cart.checkout.thankyoumomo', compact('order', 'totalSubtotal'))->with('success', 'Thanh toán thành công!');
       
        }
      // Tạo thông báo lỗi chi tiết từ thông tin trả về
      $message = $request->input('message', 'Không có thông tin cụ thể về lỗi.');

      // Trả về thông báo lỗi cho người dùng
      return redirect()->route('cart.index')->with('error', 'Thanh toán không thành công: ' . $message);
    }

    public function retrymomo(Request $request)
    {
        Log::info("momo initiated for order ID: {$request->order_id}", $request->all());
    
        if (!Auth::check()) {
            Log::error("User not authenticated");
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thanh toán.');
        }
    
        $order_id = $request->input('order_id');
        $order = Order::find($order_id);
        if (!$order) {
            Log::error("Order not found", ['order_id' => $order_id]);
            return redirect()->route('cart.index')->with('error', 'Không tìm thấy đơn hàng.');
        }
    
        $totalAmount = $order->tong_tien;
    
        if (!$order_id || !$totalAmount || $totalAmount <= 0) {
            Log::error("Invalid payment data for order ID: {$order_id}", [
                'order_id'   => $order_id,
                'totalAmount'=> $totalAmount
            ]);
            return redirect()->route('cart.index')->with('error', 'Dữ liệu thanh toán không hợp lệ.');
        }
    
        Log::info("momo Configuration for order ID: {$order_id}");
    
        // Chuẩn bị tham số thanh toán cho MoMo
        $endpoint    = "https://test-payment.momo.vn/v2/gateway/api/create";
        $partnerCode = 'MOMOBKUN20180529';
        $accessKey   = 'klm05TvNBzhg7h7j';
        $secretKey   = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
        $orderInfo   = "Thanh toán qua MoMo";
    
        // Ép amount về số nguyên và chuyển thành chuỗi (vd: "90000")
        $amount = (string)((int)$totalAmount);
        // Ép orderId về chuỗi; bạn có thể kết hợp thêm timestamp nếu cần: $order_id . "_" . time()
        $orderId = $order_id . "_" . time();


    
        // Redirect URL và IPN URL: hãy đảm bảo các route này đã được định nghĩa
        $redirectUrl = route('thankyoumomo', ['order_id' => $order_id]);
        $ipnUrl      = route('thankyoumomo', ['order_id' => $order_id]);
    
        $extraData   = "";
        $requestId   = time() . "";
        $requestType = "payWithMethod";
    
        // Tạo chuỗi rawHash theo định dạng của MoMo
        $rawHash = "accessKey=" . $accessKey .
                   "&amount=" . $amount .
                   "&extraData=" . $extraData .
                   "&ipnUrl=" . $ipnUrl .
                   "&orderId=" . $orderId .
                   "&orderInfo=" . $orderInfo .
                   "&partnerCode=" . $partnerCode .
                   "&redirectUrl=" . $redirectUrl .
                   "&requestId=" . $requestId .
                   "&requestType=" . $requestType;
    
        Log::debug("generate rawHash", ['rawHash' => $rawHash]);
    
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        Log::debug("Generated signature", ['signature' => $signature]);
    
        // Chuẩn bị dữ liệu gửi đến MoMo
        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            'storeId'     => "MomoTestStore",
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl'      => $ipnUrl,
            'lang'        => 'vi',
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature
        ];
    
        Log::info("MoMo Payment Data", $data);
    
        // Gửi yêu cầu thanh toán qua MoMo bằng CURL
        $result = $this->execPostRequest($endpoint, json_encode($data));
        Log::info("MoMo Payment Response", ['response' => $result]);
    
        $jsonResult = json_decode($result, true);
    
        if (isset($jsonResult['payUrl'])) {
            Log::info("Redirecting to MoMo payUrl", ['payUrl' => $jsonResult['payUrl']]);
            return redirect($jsonResult['payUrl']);
        }
    
        Log::error("MoMo did not return a payUrl", ['response' => $jsonResult]);
        return redirect()->route('cart.index')->with('error', 'Đã xảy ra lỗi trong quá trình thanh toán với MoMo.');
    }
    
    
}