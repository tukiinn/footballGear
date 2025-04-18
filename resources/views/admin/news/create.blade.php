@extends('layouts.admin')

@section('content')
<div class="container p-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">     <i class="fas fa-home"></i></a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.news.index') }}">Tin Tức</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Thêm Bài Viết Mới</li>
            </ol>
        </nav>
    <!-- Form Thêm bài viết -->
    <div class="card shadow-sm">
      <div class="card-body">
        <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
           @csrf
           <div class="mb-3">
               <label for="title" class="form-label">Tiêu đề</label>
               <input type="text" class="form-control" name="title" id="title" value="{{ old('title') }}" placeholder="Nhập tiêu đề bài viết..." required>
           </div>
           <div class="mb-3">
            <label for="summary" class="form-label">Tóm tắt</label>
            <textarea class="form-control" name="summary" id="summary" rows="3" placeholder="Nhập tóm tắt bài viết..." required></textarea>
        </div>
           <div class="mb-3">
               <label for="content" class="form-label">Nội dung</label>
               <textarea class="form-control" name="content" id="content" rows="5" placeholder="Soạn nội dung bài viết...">{{ old('content') }}</textarea>
           </div>
           <div class="mb-3">
               <label for="image" class="form-label">Ảnh đại diện (nếu có)</label>
               <input type="file" class="form-control" name="image" id="image" onchange="previewImage(event)">
               <img id="image-preview" src="#" alt="Preview" class="img-fluid mt-2 d-none" style="max-height: 200px;">
           </div>
           <div class="d-flex justify-content-end">
               <button type="submit" class="btn btn-primary">Lưu bài viết</button>
           </div>
        </form>
      </div>
    </div>
</div>

<!-- Preview Image Script -->
<script>
  function previewImage(event) {
    var input = event.target;
    var preview = document.getElementById('image-preview');
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        preview.src = e.target.result;
        preview.classList.remove('d-none');
      }
      reader.readAsDataURL(input.files[0]);
    }
  }
</script>

<script src="https://cdn.ckeditor.com/4.22.0/full/ckeditor.js"></script>

<script>
    CKEDITOR.replace('content', {
        // Nếu bạn muốn hỗ trợ upload ảnh, cấu hình thêm filebrowserUploadUrl:
        filebrowserUploadUrl: "{{ route('upload', ['_token' => csrf_token()]) }}",
        filebrowserUploadMethod: 'form'
    });
</script>
<style>
    .container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    /* Breadcrumb styles */
    .breadcrumb {
        background-color: transparent;
        padding: 0;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }
    .breadcrumb-item a {
      color: #ff4081;
      text-decoration: none;
  }
    .breadcrumb-item a:hover {
        text-decoration: underline;
    }
    .breadcrumb-item.active {
        color: #6c757d;
    }
    /* Title styles */
    h1 {
        color: #343a40;
    }
</style>
@endsection
