<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\News; // Import model News nếu bạn có

class HomeController extends Controller
{
    public function index()
    {
        // Lấy tất cả danh mục sản phẩm từ model Category
        $categories = Category::all();
        $news = News::all();
    
        // Truyền biến $categories vào view 'shop'
        return view('shop', compact('categories','news'));
    }
    
}
