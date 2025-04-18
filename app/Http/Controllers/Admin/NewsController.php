<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    // Hiển thị danh sách bài viết tin tức cho admin
    public function index()
    {
        $news = News::latest()->paginate(10);
        return view('admin.news.index', compact('news'));
    }

    // Hiển thị form tạo bài viết mới
    public function create()
    {
        return view('admin.news.create');
    }

    // Lưu bài viết mới vào database
    public function store(Request $request)
    {
        // Validate dữ liệu
        $request->validate([
            'title'   => 'required|max:255',
            'content' => 'required',
            'summary' => 'required',
            'image'   => 'nullable|image'
        ]);

        // Tạo slug cho bài viết
        $slug = Str::slug($request->title);
        // Nếu slug đã tồn tại, thêm hậu tố thời gian
        $slugCount = News::where('slug', $slug)->count();
        if ($slugCount) {
            $slug .= '-' . time();
        }

        $news = new News;
        $news->title   = $request->title;
        $news->slug    = $slug;
        $news->content = $request->content;
        $news->summary = $request->summary;


        if ($request->hasFile('image')) {
            $image    = $request->file('image');
            // Tạo tên file duy nhất, có thể sử dụng thời gian và tên gốc của file
            $filename = time() . '-' . $image->getClientOriginalName();
            // Đường dẫn tới thư mục public/images/news
            $destinationPath = public_path('images/news');
            // Di chuyển file vào thư mục đích
            $image->move($destinationPath, $filename);
            // Lưu đường dẫn tương đối vào CSDL
            $news->image = 'images/news/' . $filename;
        }
        

        $news->save();

        return redirect()->route('admin.news.index')->with('success', 'Bài viết được tạo thành công!');
    }

    // Hiển thị form chỉnh sửa bài viết
    public function edit($id)
    {
        $news = News::findOrFail($id);
        return view('admin.news.edit', compact('news'));
    }

    // Cập nhật bài viết
   public function update(Request $request, $id)
{
    $news = News::findOrFail($id);

    $request->validate([
        'title'   => 'required|max:255',
        'content' => 'required',
        'summary' => 'required',
        'image'   => 'nullable|image'
    ]);

    $news->title   = $request->title;
    $news->content = $request->content;
    $news->summary = $request->summary;

    // Cập nhật slug nếu tiêu đề thay đổi
    $slug = Str::slug($request->title);
    $slugCount = News::where('slug', $slug)->where('id', '!=', $id)->count();
    if ($slugCount) {
        $slug .= '-' . time();
    }
    $news->slug = $slug;

    // Xử lý upload ảnh (nếu có)
    if ($request->hasFile('image')) {
        // Nếu muốn xóa ảnh cũ trước khi lưu ảnh mới
        if ($news->image && file_exists(public_path($news->image))) {
            unlink(public_path($news->image));
        }
        
        $image = $request->file('image');
        // Tạo tên file duy nhất
        $filename = time() . '-' . $image->getClientOriginalName();
        // Đường dẫn tới thư mục public/images/news
        $destinationPath = public_path('images/news');
        // Di chuyển file đến thư mục đích
        $image->move($destinationPath, $filename);
        // Lưu đường dẫn tương đối
        $news->image = 'images/news/' . $filename;
    }

    $news->save();

    return redirect()->route('admin.news.index')->with('success', 'Bài viết được cập nhật thành công!');
}


    // Xóa bài viết
    public function destroy($id)
    {
        $news = News::findOrFail($id);
        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'Bài viết đã được xóa!');
    }
}
