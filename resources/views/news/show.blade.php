@extends('layouts.app')

@section('content')
<div class="container">
  <!-- Breadcrumb -->
  <div class="breadcrumb-container mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
        <li class="breadcrumb-item"><a href="{{ route('news.index') }}">Tin Tức</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $newsItem->title }}</li>
      </ol>
    </nav>
  </div>

  <!-- News Container -->
  <div class="news-container">
    <h1 class="news-title">{{ $newsItem->title }}</h1>

    @if($newsItem->image)
      <div class="news-image">
        <img src="{{ asset($newsItem->image) }}" alt="{{ $newsItem->title }}">
      </div>
    @endif

    <div class="news-content">
      {!! $newsItem->content !!}
    </div>
  </div>
</div>

<!-- CSS -->
<style>
  /* Breadcrumb */
  .breadcrumb-container {
    background-color: #1e1e1e;
    padding: 10px 20px;
    border-bottom: 1px solid #444;
  }
  .breadcrumb-item + .breadcrumb-item::before {
    content: "/";
    color: #fff;
    padding: 0 0.5rem;
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

  /* News Container */
  .news-container {
    padding: 30px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-top: 30px;
    max-width: 900px;
    margin-left: auto;
    margin-right: auto;
  }

  /* Tiêu đề bài viết */
.news-title {
  font-size: 2rem;
  font-weight: 700;
  color: #ff4081; /* Màu nổi bật hơn */
  text-align: center;
  margin-bottom: 20px;
}

/* Hình ảnh bài viết */
.news-image {
  text-align: center;
  margin-bottom: 20px;
}
.news-image img {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(255,255,255,0.1);
}

/* Nội dung bài viết */
.news-content {
  font-family: 'Roboto', sans-serif;
  font-size: 1.1rem;
  line-height: 1.8;
  color: #e0e0e0; /* Chữ sáng hơn */
}

/* Tiêu đề trong bài viết */
.news-content h1, 
.news-content h2, 
.news-content h3 {
  font-weight: bold;
  margin-top: 1.5rem;
  margin-bottom: 0.5rem;
  color: #ff4081; /* Màu nổi bật */
}

/* Định dạng đoạn văn */
.news-content p {
  margin-bottom: 1rem;
  text-align: justify;
  color: #f0f0f0; /* Chữ trắng hơn */
}

/* Danh sách */
.news-content ul, 
.news-content ol {
  margin: 1rem 0;
  padding-left: 2rem;
  color: #ddd; /* Màu sáng */
}
.news-content ul {
  list-style: disc;
}
.news-content ol {
  list-style: decimal;
}

/* Liên kết */
.news-content a {
  color: #ff4081;
  text-decoration: underline;
  font-weight: 600;
}
.news-content a:hover {
  color: #d81b60;
}

/* Trích dẫn */
.news-content blockquote {
  border-left: 4px solid #ff4081;
  padding-left: 15px;
  font-style: italic;
  color: #bbb;
  margin: 20px 0;
}

/* Bảng */
.news-content table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
  background: #222;
}
.news-content table td, 
.news-content table th {
  border: 1px solid #444;
  padding: 8px;
  color: #f0f0f0;
}
.news-content table th {
  background: #333;
  font-weight: bold;
}

/* Hình ảnh trong nội dung */
.news-content img {
  max-width: 100%;
  height: auto;
  display: block;
  margin: 15px auto;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(255,255,255,0.1);
}

</style>
@endsection
