@extends('layouts.app')

@section('content')
  <!-- Breadcrumb -->
  <div class="breadcrumb-container">
    <div class="container">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
          <li class="breadcrumb-item active" aria-current="page">Tin Tức</li>
        </ol>
      </nav>
    </div>
  </div>

  <!-- News Cards -->
  <div class="container my-5">
    <div class="row">
      @foreach($news as $item)
        <div class="col-md-4 mb-4">
          <div class="card h-100 shadow-sm news-card position-relative">
            @if($item->image)
              <img src="{{ asset($item->image) }}" class="card-img-top" alt="{{ $item->title }}">
            @else
              <img src="https://via.placeholder.com/350x200?text=No+Image" class="card-img-top" alt="No Image">
            @endif
            <div class="card-body">
              <h5 class="card-title">{{ $item->title }}</h5>
              <p class="card-text">{{ $item->summary }}</p>
            </div>
            <!-- Sử dụng stretched-link để toàn bộ card có thể click -->
            <a href="{{ route('news.show', $item->slug) }}" class="stretched-link"></a>
          </div>
        </div>
      @endforeach
    </div>
    

    <!-- Phân trang -->
    <div class="d-flex justify-content-center">
      {{ $news->links() }}
    </div>
  </div>

  <style>
    /* Breadcrumb */
    .breadcrumb-item + .breadcrumb-item::before {
      content: "/";
      color: #fff;
      padding: 0 0.5rem;
    }
    .breadcrumb-container {
      background-color: #1e1e1e;
      padding: 10px 20px;
      border-bottom: 1px solid #444;
    }
    .breadcrumb {
      margin: 0;
      font-size: 0.9rem;
    }
    .breadcrumb-item a {
      color: #ff4081;
      text-decoration: none;
      transition: color 0.3s ease;
    }
    .breadcrumb-item a:hover {
      color: #d81b60;
    }
    .breadcrumb-item.active {
      color: #6c757d;
    }

    /* News Cards */
    .news-card {
      background-color: var(--card-bg);
      color: var(--text-color);
      border: none;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .news-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(255, 64, 129, 0.2);
    }
    .news-card .card-img-top {
      height: 200px;
      object-fit: cover;
      border-top-left-radius: 8px;
      border-top-right-radius: 8px;
    }
    .news-link {
      text-decoration: none;
      color: inherit;
      display: block;
    }
    .news-card .card-title {
      font-size: 1.2rem;
      font-weight: 600;
      margin-top: 10px;
    }
    .news-card .card-text {
      color: #ccc;
    }
  </style>
@endsection
