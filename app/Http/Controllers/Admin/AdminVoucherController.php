<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminVoucherController extends Controller
{
    
    public function store(Request $request)
{
    // Kiểm tra xem ngày bắt đầu và ngày kết thúc có hợp lệ hay không
    $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d H:i:s') : null;
    $endDate = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d H:i:s') : null;

    // Lưu vào cơ sở dữ liệu
    Voucher::create([
        'code' => $request->code,
        'discount' => $request->discount,
        'type' => $request->type,
        'max_usage' => $request->max_usage,
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]);

    return redirect()->route('admin.vouchers.index')->with('success', 'Voucher đã được tạo thành công!');
}

public function index(Request $request)
{
    // Khởi tạo query cho Voucher
    $query = Voucher::query();

    // Nếu có tham số tìm kiếm, lọc theo mã voucher (code) hoặc loại (type)
    if ($search = $request->input('search')) {
        $query->where(function($q) use ($search) {
            $q->where('code', 'like', '%' . $search . '%')
              ->orWhere('type', 'like', '%' . $search . '%');
        });
    }

    // Lấy danh sách voucher (sử dụng get(), có thể dùng paginate nếu cần)
    $vouchers = $query->get();

    return view('admin.vouchers.index', compact('vouchers'));
}


    // Hiển thị form tạo voucher
    public function create()
    {
        return view('admin.vouchers.create');
    }



    // Hiển thị form chỉnh sửa voucher
    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    // Cập nhật thông tin voucher
    public function update(Request $request, Voucher $voucher)
    {
        $request->validate([
            'discount' => 'required|numeric',
            'type' => 'required|in:percentage,fixed',
            'max_usage' => 'required|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $voucher->update($request->all());

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher đã được cập nhật!');
    }

    // Xóa voucher
    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher đã được xóa!');
    }
}
