<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class AdminProductController extends Controller
{
    // Hiển thị danh sách sản phẩm
    public function index(Request $request)
    {
        // Khởi tạo query với quan hệ 'category'
        $query = Product::with('category');

        // Nếu có từ khóa tìm kiếm, lọc theo 'product_name'
        if ($search = $request->input('search')) {
            $query->where('product_name', 'like', '%' . $search . '%');
        }

        // Sắp xếp theo ID giảm dần và phân trang 12 sản phẩm mỗi trang
        $products = $query->orderBy('id', 'desc')->paginate(12);

        return view('admin.products.index', compact('products'));
    }

    // Hiển thị chi tiết sản phẩm
    public function show($id)
    {
        // Lấy thông tin sản phẩm kèm quan hệ sizes và category
        $product = Product::with('sizes', 'category')->findOrFail($id);
        return view('admin.products.show', compact('product'));
    }

    // Trang tạo mới sản phẩm
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name'     => 'required|string|max:255',
            'description'      => 'nullable|string',
            'detail'           => 'required',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price'            => 'required|numeric|min:0',
            'discount_price'   => 'nullable|numeric|min:0|lte:price',
            'category_id'      => 'required|exists:categories,id',
            'status'           => 'required|boolean',
            'featured'         => 'nullable|boolean',
            'sizes'            => 'nullable|array',
            'sizes.*.size'     => 'required|string',
            'sizes.*.quantity' => 'required|numeric|min:0',
        ]);

        $data = $request->all();

        // Tạo slug tự động từ product_name
        $data['slug'] = Str::slug($data['product_name']);

        // Đảm bảo slug là duy nhất: nếu đã tồn tại thì thêm số đếm vào cuối slug
        $originalSlug = $data['slug'];
        $counter = 1;
        while (Product::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        if ($request->hasFile('image')) {
            $image     = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/products'), $imageName);
            $data['image'] = 'images/products/' . $imageName;
        }

        // Tạo sản phẩm, không cần lưu stock_quantity vì số lượng được quản lý qua bảng product_sizes
        $product = Product::create($data);

        // Nếu có sizes, lưu các record vào bảng product_sizes
        if ($request->filled('sizes')) {
            foreach ($request->input('sizes') as $sizeData) {
                ProductSize::create([
                    'product_id' => $product->id,
                    'size'       => $sizeData['size'],
                    'quantity'   => $sizeData['quantity'],
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    // Trang chỉnh sửa sản phẩm
    public function edit($id)
    {
        $product = Product::with('sizes')->findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    // Cập nhật sản phẩm trong cơ sở dữ liệu
    public function update(Request $request, $id)
    {
        $product = Product::with('sizes')->findOrFail($id);

        $request->validate([
            'product_name'     => 'required|string|max:255',
            'description'      => 'nullable|string',
            'detail'           => 'required',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price'            => 'required|numeric|min:0',
            'discount_price'   => 'nullable|numeric|min:0|lte:price',
            'category_id'      => 'required|exists:categories,id',
            'slug'             => 'required|string|unique:products,slug,' . $product->id,
            'status'           => 'required|boolean',
            'featured'         => 'nullable|boolean',
            'sizes'            => 'nullable|array',
            'sizes.*.size'     => 'required|string',
            'sizes.*.quantity' => 'required|numeric|min:0',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $image     = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/products'), $imageName);
            $data['image'] = 'images/products/' . $imageName;
        }

        $product->update($data);

        // Cập nhật sizes: xoá các record cũ rồi tạo mới
        if ($request->filled('sizes')) {
            $product->sizes()->delete();
            foreach ($request->input('sizes') as $sizeData) {
                ProductSize::create([
                    'product_id' => $product->id,
                    'size'       => $sizeData['size'],
                    'quantity'   => $sizeData['quantity'],
                ]);
            }
        } else {
            // Nếu không có sizes trong request, xoá các record sizes hiện tại (nếu có)
            $product->sizes()->delete();
        }

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    // Xóa sản phẩm
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image && file_exists(public_path($product->image))) {
            unlink(public_path($product->image));
        }
        // Xoá các record sizes của sản phẩm
        $product->sizes()->delete();
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Xóa sản phẩm thành công!');
    }
    
    // Cập nhật số lượng của một size cụ thể qua AJAX
    public function updateStock(Request $request, $productId, $sizeId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $productSize = ProductSize::where('product_id', $productId)
                    ->where('product_size_id', $sizeId)
                    ->firstOrFail();

        $productSize->quantity = $request->quantity;
        $productSize->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật số lượng thành công!',
            'data' => $productSize
        ]);
    }

    // Thêm mới một size cho sản phẩm qua AJAX
    public function storeSize(Request $request, $productId)
    {
        $request->validate([
            'size' => 'required|string|max:10',
            'quantity' => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($productId);
        $productSize = $product->sizes()->create([
            'size' => $request->size,
            'quantity' => $request->quantity,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thêm size thành công!',
            'data' => $productSize
        ]);
    }

    // Xóa một size của sản phẩm qua AJAX
    public function destroySize($productId, $sizeId)
    {
        $productSize = ProductSize::where('product_id', $productId)
        ->where('product_size_id', $sizeId)
        ->firstOrFail();

        $productSize->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa size thành công!'
        ]);
    }
}
