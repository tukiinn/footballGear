<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    public function applyVoucher(Request $request)
{
    // Lấy mã voucher từ request
    $code = $request->input('code');
    Log::info('Apply voucher request received', ['code' => $code]);

    $voucher = Voucher::where('code', $code)->first();

    if (!$voucher) {
        Log::warning('Voucher not found', ['code' => $code]);
        return response()->json(['error' => 'Mã voucher không hợp lệ!'], 400);
    }

    // Kiểm tra ngày hiện tại so với ngày bắt đầu/kết thúc của voucher
    $currentDate = \Carbon\Carbon::now();
    Log::info('Current date', ['current_date' => $currentDate->toDateTimeString()]);

    if ($voucher->start_date && \Carbon\Carbon::parse($voucher->start_date)->gt($currentDate)) {
        Log::warning('Voucher not started yet', ['voucher_start_date' => $voucher->start_date]);
        return response()->json(['error' => 'Voucher chưa bắt đầu!'], 400);
    }

    if ($voucher->end_date && \Carbon\Carbon::parse($voucher->end_date)->lt($currentDate)) {
        Log::warning('Voucher expired', ['voucher_end_date' => $voucher->end_date]);
        return response()->json(['error' => 'Voucher đã hết hạn!'], 400);
    }

    // Lấy giỏ hàng của người dùng và tính tổng giá trị giỏ hàng
    $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
    Log::info('Cart items count', ['count' => $cartItems->count()]);

    $cartTotal = $cartItems->sum(function ($cartItem) {
        $price = $cartItem->product->discount_price ?? $cartItem->product->price;
        // Không sử dụng hệ số cân nặng nữa – chỉ nhân số lượng
        return $price * $cartItem->quantity;
    });
    Log::info('Cart total calculated', ['cartTotal' => $cartTotal]);

    // Kiểm tra số lần sử dụng tối đa của voucher
    if ($voucher->used >= $voucher->max_usage) {
        Log::warning('Voucher usage exceeded', ['voucher_used' => $voucher->used, 'max_usage' => $voucher->max_usage]);
        return response()->json(['error' => 'Voucher đã hết lượt sử dụng!'], 400);
    }

    // Tính toán giảm giá từ voucher
    $voucherDiscount = 0;
    if ($voucher->type === 'percentage') {
        $voucherDiscount = ($cartTotal * ((float) $voucher->discount)) / 100;
        Log::info('Voucher discount calculated (percentage)', [
            'voucher_discount'    => $voucher->discount,
            'calculated_discount' => $voucherDiscount
        ]);
    } else {
        $voucherDiscount = (float)$voucher->discount;
        Log::info('Voucher discount calculated (fixed)', ['voucherDiscount' => $voucherDiscount]);
    }

    // Tính tổng tiền sau khi áp dụng voucher, đảm bảo không âm
    $finalTotal = max($cartTotal - $voucherDiscount, 0);
    Log::info('Final total calculated', ['finalTotal' => $finalTotal]);

    return response()->json([
        'voucher'      => $voucher,
        'voucher_code' => $voucher->code,
        'discount'     => $voucherDiscount,
        'finalTotal'   => $finalTotal,
        'type'         => $voucher->type,
        'voucherValue' => $voucher->discount
    ]);
}


    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'code'       => 'required|unique:vouchers,code',
            'discount'   => 'required',
            'type'       => 'required',
            'max_usage'  => 'required|integer',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date',
        ]);
    
        // Xử lý ngày bắt đầu và kết thúc (nếu có)
        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->format('Y-m-d H:i:s') : null;
        $endDate   = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->format('Y-m-d H:i:s') : null;
    
        // Lưu voucher vào bảng vouchers
        $voucher = Voucher::create([
            'code'       => $request->code,
            'discount'   => $request->discount,
            'type'       => $request->type,
            'max_usage'  => $request->max_usage,
            'start_date' => $startDate,
            'end_date'   => $endDate,

        ]);
    
        if (Auth::check()) {
            $user = Auth::user();
            $result = DB::table('user_voucher')->updateOrInsert(
                [
                    'user_id'    => $user->id,
                    'voucher_id' => $voucher->id,
                ],
                [
                    'expiry_date' => null, // Thay đổi nếu có hạn sử dụng cụ thể
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]
            );
        
            Log::info('Voucher saved', [ 
                'user_id'    => $user->id, 
                'voucher_id' => $voucher->id, 
                'result'     => $result 
            ]);
        }
 
    return response()->json([
        'success' => true,
        'voucher' => $voucher,
    ]);
    }
    
}
