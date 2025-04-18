<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\News;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;
class NAController extends Controller
{

    public function search(Request $request)
    {
        // Validate input: từ khóa phải có độ dài tối thiểu và không quá dài
        $request->validate([
            'query' => 'required|string|min:2|max:255',
        ]);
    
        $query = $request->input('query');
    
        // Tạo key cho cache (bao gồm cả trang hiện tại)
        $page = $request->input('page', 1);
        $cacheKey = 'search_' . md5($query . '_' . $page);
    
        // Sử dụng cache trong 60 giây nếu truy vấn lặp lại
        $products = Cache::remember($cacheKey, 60, function() use ($query) {
            return Product::where('product_name', 'like', '%' . $query . '%')
                          ->paginate(8);
        });
    
        return view('products.search-results', compact('products', 'query'));
    }
    



    public function index()
    {
        // Lấy tin tức mới nhất, phân trang 10 bài viết/trang
        $news = News::latest()->paginate(10);
        return view('news.index', compact('news'));
    }

    // Hiển thị chi tiết bài viết tin tức theo slug
    public function show($slug)
    {
        $newsItem = News::where('slug', $slug)->firstOrFail();
        return view('news.show', compact('newsItem'));
    }
    public function addressIndex()
    {
       
        return view('address.index');
    }
    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $originName = $file->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $fileName = $fileName . '_' . time() . '.' . $extension;
            
            // Lưu file vào thư mục public/uploads
            $file->move(public_path('uploads'), $fileName);
            $url = asset('uploads/' . $fileName);

            // CKEditor yêu cầu trả về một đoạn script gọi lại hàm JS với URL ảnh
            $funcNum = $request->input('CKEditorFuncNum');
            $message = '';
            $response = "<script>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";

            @header('Content-type: text/html; charset=utf-8');
            echo $response;
        }
    }
}
