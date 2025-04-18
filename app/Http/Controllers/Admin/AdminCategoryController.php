<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminCategoryController extends Controller
{
    public function index(Request $request)
    {
        // Khởi tạo query
        $query = Category::query();
    
        // Nếu có từ khóa tìm kiếm, lọc theo tên danh mục
        if ($search = $request->input('search')) {
            $query->where('category_name', 'like', '%' . $search . '%');
        }
    
        // Sắp xếp theo sort_order tăng dần
        $categories = $query->orderBy('sort_order', 'asc')->get();
    
        return view('admin.categories.index', compact('categories'));
    }
    
    public function show($id)
    {
        // Lấy thông tin sản phẩm theo ID
        $category = Category::findOrFail($id);
        // Trả về view hiển thị chi tiết sản phẩm
        return view('admin.categories.show', compact('category'));
    }
    public function create()
    {
        $categories = Category::all();  // Lấy tất cả danh mục để chọn danh mục cha
        return view('admin.categories.create', compact('categories'));
    }
    

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'slug' => 'required|unique:categories,slug',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

          
        // Lưu ảnh mới vào thư mục public/images/categories
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/categories'), $imageName);
            $validated['image'] = 'images/categories/' . $imageName;
    
        }

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Danh mục đã được tạo.');
    }

    public function edit($id)   
    {
        $category = Category::findOrFail($id);
        $categories = Category::where('id', '!=', $id)->get(); // Loại bỏ chính danh mục
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
    
        $validated = $request->validate([
            'category_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'slug' => 'required|unique:categories,slug,' . $category->id,
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer',
            'parent_id' => 'nullable|exists:categories,id',
        ]);
    
        // Lưu ảnh mới vào thư mục public/images/categories
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/categories'), $imageName);
            $validated['image'] = 'images/categories/' . $imageName;
    
            // Xóa ảnh cũ nếu tồn tại
            if ($category->image && file_exists(public_path($category->image))) {
                unlink(public_path($category->image));
            }
        }
    
        $category->update($validated);
    
        return redirect()->route('admin.categories.index')->with('success', 'Danh mục đã được cập nhật.');
    }
    
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Danh mục đã được xóa.');
    }
}
