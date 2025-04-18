<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\Voucher;
use App\Models\News;
use App\Http\Controllers\Controller;

class AdminSearchController extends Controller
{
    public function search(Request $request)
    {
        // Lấy tham số từ request, mặc định nếu không truyền là 'all'
        $dataType = $request->input('dataType', 'all');
        $query = $request->input('query');

        // Khởi tạo mảng kết quả
        $results = [];

        // Tìm kiếm trong danh mục: sử dụng các trường 'name' và 'description'
        if ($dataType === 'all' || $dataType === 'categories') {
            $categories = Category::where('category_name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->get();
            $results['categories'] = $categories;
        }

        // Tìm kiếm trong sản phẩm: sử dụng các trường 'name', 'sku' và 'description'
        if ($dataType === 'all' || $dataType === 'products') {
            $products = Product::where('product_name', 'like', "%{$query}%")
                ->orWhere('slug', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->get();
            $results['products'] = $products;
        }

        // Tìm kiếm trong đơn hàng: sử dụng các trường 'order_number' và 'customer_name'
        if ($dataType === 'all' || $dataType === 'orders') {
            $orders = Order::where('id', 'like', "%{$query}%")
                ->orWhere('ten_khach_hang', 'like', "%{$query}%")
                ->get();
            $results['orders'] = $orders;
        }

        // Tìm kiếm trong voucher: sử dụng các trường 'code' và 'description'
        if ($dataType === 'all' || $dataType === 'vouchers') {
            $vouchers = Voucher::where('code', 'like', "%{$query}%")
                ->orWhere('type', 'like', "%{$query}%")
                ->get();
            $results['vouchers'] = $vouchers;
        }

        // Tìm kiếm trong bài viết (news): sử dụng các trường 'title' và 'body'
        if ($dataType === 'all' || $dataType === 'news') {
            $news = News::where('title', 'like', "%{$query}%")
                ->orWhere('content', 'like', "%{$query}%")
                ->get();
            $results['news'] = $news;
        }

        // Trả về kết quả dưới dạng JSON
        return response()->json($results);
    }
}
