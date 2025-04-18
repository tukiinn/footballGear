<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    
    public function index()
    {
        $categories = Category::orderBy('sort_order', 'asc')->get();
        return view('categories.index', compact('categories'));
    }
    public function show($id)
    {
        // Lấy thông tin sản phẩm theo ID
        $category = Category::findOrFail($id);
        // Trả về view hiển thị chi tiết sản phẩm
        return view('categories.show', compact('category'));
    }
    public function create()
    {
        $categories = Category::all();  // Lấy tất cả danh mục để chọn danh mục cha
        return view('categories.create', compact('categories'));
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
            'harvest_season' => 'nullable|string',
            'region' => 'nullable|string',
            'certifications' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Danh mục đã được tạo.');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $categories = Category::where('id', '!=', $id)->get(); // Loại bỏ chính danh mục
        return view('categories.edit', compact('category', 'categories'));
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
            'harvest_season' => 'nullable|string',
            'region' => 'nullable|string',
            'certifications' => 'nullable|string',
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
    
        return redirect()->route('categories.index')->with('success', 'Danh mục đã được cập nhật.');
    }
    
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Danh mục đã được xóa.');
    }
}
