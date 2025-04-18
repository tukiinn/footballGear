<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;




class ProductController extends Controller
{
    // Hiển thị danh sách sản phẩm
    public function index(Request $request)
    {
        $query = Product::query();
    
        if ($request->filled('category')) {
            $categoryId = $request->category;
            // Lấy danh sách các danh mục con của danh mục cha đã chọn
            $childCategoryIds = Category::where('parent_id', $categoryId)->pluck('id')->toArray();
            // Gom nhóm danh mục cha và các danh mục con
            $categoryIds = array_merge([$categoryId], $childCategoryIds);
            
            // Lọc sản phẩm theo danh mục trong tập hợp vừa lấy
            $query->whereIn('category_id', $categoryIds);
        }
        
    
        // Lọc theo tình trạng
        if ($request->filled('condition')) {
            switch ($request->condition) {
                case 'sale':
                    $query->where('discount_price', '>', 0);
                    break;
                case 'featured':
                    $query->where('featured', 1)
                          ->orderBy('created_at', 'desc');
                    break;
                case 'in-stock':
                    $query->whereHas('sizes', function($q) {
                        $q->where('quantity', '>', 0);
                    });
                    break;
            }
        }
    
        // Sắp xếp sản phẩm
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'featured':
                    $query->orderBy('featured', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                default:
                    $query->orderBy('id', 'desc');
                    break;
            }
        } else {
            $query->orderBy('id', 'desc');
        }
    
        // Lọc theo giá
        if ($request->filled('min_price') && $request->filled('max_price')) {
            $minPrice = (float) $request->min_price;
            $maxPrice = (float) $request->max_price;
        
            $query->whereBetween(DB::raw('IFNULL(discount_price, price)'), [$minPrice, $maxPrice]);
        }
        
        // Lấy sản phẩm với phân trang
        $products = $query->with('sizes')->paginate(12);
    
        $categories = Category::with([
            'children' => function($q) {
                $q->withCount('products');
            }
        ])->withCount('products')->get();
        
        foreach ($categories as $category) {
            $childProductCount = $category->children->sum('products_count');
            $category->total_products = $category->products_count + $childProductCount;
        }
        
    
        return view('products.index', compact('products', 'categories'));
    }
    

    public function show($id)
    {
        // Lấy thông tin sản phẩm theo ID
        $product = Product::findOrFail($id);
         // Lấy danh sách sản phẩm đã xem từ session (nếu chưa có thì mặc định là mảng rỗng)
    $recentlyViewed = session()->get('recently_viewed', []);

    // Nếu sản phẩm đã tồn tại trong danh sách, xóa nó đi để cập nhật thứ tự
    if (($key = array_search($product->id, $recentlyViewed)) !== false) {
        unset($recentlyViewed[$key]);
    }

    // Thêm ID sản phẩm vào đầu danh sách (sản phẩm mới xem luôn nằm đầu)
    array_unshift($recentlyViewed, $product->id);

    // Giới hạn danh sách (ví dụ chỉ lưu tối đa 5 sản phẩm)
    $recentlyViewed = array_slice($recentlyViewed, 0, 5);

    // Lưu lại danh sách vào session
    session()->put('recently_viewed', $recentlyViewed);
    
        // Trả về view hiển thị chi tiết sản phẩm
        return view('products.show', compact('product'));
    }
   
   
}
