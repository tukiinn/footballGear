@extends('layouts.app')

@section('title', 'Shop | HieuStore - Giày Bóng Đá Xịn Xò')

@push('styles')
<style>
  /* Tiêu đề trang */
  .shop-title {
    font-family: 'Montserrat', sans-serif;
    text-align: center;
    margin-bottom: 30px;
    font-size: 2rem;
    color: #fff;
  }

  /* ----- Masonry Layout cho phần danh mục ----- */
  .category-section {
    column-count: 3;
    column-gap: 20px;
    margin-bottom: 40px;
  }
  .category-card {
    display: inline-block;
    width: 100%;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
    border-radius: 0;
    box-shadow: none;
  }
  .category-card img {
    width: 100%;
    height: auto;
    display: block;
    transition: transform 0.3s ease;
  }
  .category-card:hover img {
    transform: scale(1.1);
  }
  .category-overlay {
    position: absolute;
    top: 0; 
    left: 0;
    width: 100%; 
    height: 100%;
    background: rgba(0,0,0,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
  }
  .category-overlay h3 {
    color: #fff;
    font-family: 'Montserrat', sans-serif;
    font-size: 1.2rem;
    text-transform: uppercase;
    text-align: center;
    padding: 0 10px;
  }
  .category-card:hover .category-overlay {
    opacity: 1;
  }

  /* News Cards */
  .news-section {
    margin-top: 50px;
  }
  .news-card {
    background-color: var(--card-bg);
    color: var(--text-color);
    border: none;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .news-card img {
    height: 200px;
    object-fit: cover;
    border-top-left-radius: 0px;
    border-top-right-radius: 0px;
  }
/* Loại bỏ gạch chân và giữ màu mặc định */
.news-card a {
    text-decoration: none;
    color: inherit;
}

/* Đổi màu tiêu đề khi hover */
.news-card:hover .card-title {
    color: #ff4081; /* Đổi sang màu hồng */
    transition: color 0.3s ease-in-out;
}

/* Hiệu ứng nhẹ khi hover toàn bộ card */
.news-card {
    transition: transform 0.2s ease-in-out;
}

.news-card:hover {
    transform: translateY(-5px);
}


</style>
@endpush

@section('content')
<div class="container my-4">
  <!-- Phần danh mục dạng Masonry -->
  <div class="category-section">
    @foreach($categories->where('status', 1)->take(9) as $category)
      <a href="{{ url('/products?category=' . $category->id) }}">
        <div class="category-card">
          <img src="{{ asset($category->image) }}" alt="{{ $category->category_name }}">
          <div class="category-overlay">
            <h3>{{ $category->category_name }}</h3>
          </div>
        </div>
      </a>
    @endforeach
  </div>

  <!-- Tiêu đề cửa hàng -->
  <h1 class="shop-title">Blog bóng đá</h1>

  <!-- Phần hiển thị 3 bài viết gần nhất -->
  <div class="news-section">
    <div class="row">
      @foreach($news->take(3) as $item)
        <div class="col-md-4 mb-4">
          <div class="card h-100 shadow-sm news-card">
            <a href="{{ route('news.show', $item->slug) }}">
              @if($item->image)
                <img src="{{ asset($item->image) }}" class="card-img-top" alt="{{ $item->title }}">
              @else
                <img src="https://via.placeholder.com/350x200?text=No+Image" class="card-img-top" alt="No Image">
              @endif
              <div class="card-body">
                <h5 class="card-title">{{ $item->title }}</h5>
                <p class="card-text">{{ Str::limit($item->summary, 100) }}</p>
              </div>
            </a>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>

<div class="social-media-section mt-5">
  <div class="row text-center">
    <!-- Facebook -->
    <div class="col-md-4">
      <h5 class="text-white mb-4 mt-4">Facebook</h5>
      <div class="fb-page" 
        data-href="https://www.facebook.com/leomessi" 
        data-tabs="timeline" 
        data-width="300" 
        data-height="400" 
        data-small-header="false" 
        data-adapt-container-width="true" 
        data-hide-cover="false" 
        data-show-facepile="true">
      </div>
    </div>

    <div class="col-md-4">
      <h5 class="text-white mb-4 mt-4">YouTube</h5>
      <iframe width="400" height="300"
        src="https://www.youtube.com/embed/T8qlYkkHXIs"
        frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        allowfullscreen>
      </iframe>
    </div>
  <!-- Instagram -->
  <div class="col-md-4">
   
    <h5 class="text-white mb-4 mt-4">Instagram</h5>
    <iframe class="rounded" src="https://www.instagram.com/leomessi/embed" width="100%" height="400" frameborder="0"></iframe>
  
</div>
  </div>
</div>
<script async defer crossorigin="anonymous" 
  src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v16.0" 
  nonce="12345">
</script>

@endsection
