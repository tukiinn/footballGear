<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome | HieuStore - Giày Bóng Đá Chính Hãng</title>
  <!-- Import Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;700&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-color: #ff5733;
      --secondary-color: #c70039;
      --text-color: #fff;
    }
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body, html {
      width: 100%;
      height: 100%;
      font-family: 'Poppins', sans-serif;
      background-color: #f5f5f5;
    }
    /* Hero Section */
    .hero {
      position: relative;
      width: 100%;
      height: 100vh;
      background: url("{{ asset('images/banner/bannergiay.jpg') }}") no-repeat center center/cover;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
    }
    .hero::before {
      content: "";
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background-color: rgba(0,0,0,0.6);
      z-index: 1;
    }
    .hero-content {
      position: relative;
      z-index: 2;
      color: var(--text-color);
      animation: fadeInUp 1.5s ease-out;
      padding: 20px;
    }
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    h1 {
      font-size: 3rem;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }
    p {
      font-size: 1.2rem;
      max-width: 600px;
      margin: auto;
      line-height: 1.6;
    }
    .btn {
      display: inline-block;
      background-color: var(--primary-color);
      color: var(--text-color);
      padding: 12px 25px;
      font-size: 1.1rem;
      text-decoration: none;
      border-radius: 50px;
      transition: 0.3s;
    }
    .btn:hover {
      background-color: var(--secondary-color);
      transform: scale(1.05);
    }
    .social-media-section {
      background-color: #222;
      color: white;
      padding: 50px 0;
      text-align: center;
    }
    .social-media-section h2 {
      font-size: 2rem;
      margin-bottom: 20px;
    }
    .social-container {
      display: flex;
      justify-content: center;
      gap: 30px;
      flex-wrap: wrap;
    }
    .social-box {
      width: 320px;
      background: white;
      border-radius: 10px;
      padding: 15px;
      text-align: center;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .social-box h5 {
      margin-bottom: 15px;
      color: #333;
    }
    @media (max-width: 768px) {
      h1 {
        font-size: 2.5rem;
      }
      p {
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>
  <section class="hero">
    <div class="hero-content">
      <h1>Chào mừng đến với HieuStore!</h1>
      <p>Chuyên cung cấp giày bóng đá chính hãng giúp bạn tỏa sáng trên sân cỏ. Dù bạn là newbie hay pro, chúng tôi đều có sản phẩm dành riêng cho bạn!</p>
      <a href="{{ url('/shop') }}" class="btn">Khám phá ngay</a>
    </div>
  </section>
  
  <!-- Social Media Section -->
  <section class="social-media-section">
    <h2>Kết nối với chúng tôi</h2>
    <div class="social-container">
      <!-- Facebook -->
      <div class="social-box">
        <h5>Facebook</h5>
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
      
      <!-- YouTube -->
      <div class="social-box">
        <h5>YouTube</h5>
        <iframe width="300" height="200"
          src="https://www.youtube.com/embed/T8qlYkkHXIs"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen>
        </iframe>
      </div>
      
      <!-- Instagram -->
      <div class="social-box">
        <h5>Instagram</h5>
        <iframe class="rounded" src="https://www.instagram.com/leomessi/embed" width="300" height="400" frameborder="0"></iframe>
      </div>
    </div>
  </section>

  <script async defer crossorigin="anonymous" 
    src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v16.0" 
    nonce="12345">
  </script>
</body>
</html>