<?php
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OnlPaymentController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\NAController;
use App\Models\News;

// Route nhóm cho admin
Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
    // Trang dashboard
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Quản lý sản phẩm
    Route::resource('products', AdminProductController::class);

    // Quản lý danh mục
    Route::resource('categories', AdminCategoryController::class);

    // Quản lý đơn hàng
    Route::resource('orders', AdminOrderController::class);

    Route::resource('news', NewsController::class);
});

Route::get('/', [HomeController::class, 'index'])->name('home');


Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login'); 
Route::post('login', [AuthController::class, 'login']);  
Route::post('logout', [AuthController::class, 'logout'])->name('logout'); 



Route::resource('products', ProductController::class);
Route::get('/recently-viewed', [ProductController::class, 'recentlyViewed'])->name('recently_viewed');


Route::resource('categories', CategoryController::class);


// Routes cho giỏ hàng
Route::get('/cart', [CartController::class, 'index'])->name('cart.index'); 
Route::post('/cart/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/remove/{id}', [CartController::class, 'removeFromCart'])->name('cart.remove');
Route::get('/cart/clear', [CartController::class, 'clearCart'])->name('cart.clear');


    Route::post('/cart/update/{id}', [CartController::class, 'updateCart'])->name('cart.update');

// Routes cho đơn hàng

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/create', [OrderController::class, 'createOrder'])->name('orders.create');
    Route::post('/orders/{id}/payCOD', [OrderController::class, 'payCOD'])->name('order.payCOD');
 // Route cho VNPay thanh toán và trả về
Route::get('/payment', [OrderController::class, 'paymentPage'])->name('payment.index');
Route::get('/vnpay_return', [OnlPaymentController::class, 'vnpayReturn'])->name('vnpay.return');

Route::post('/vnpay', [OnlPaymentController::class, 'vnpayment'])->name('vnpay.vn');
Route::get('/cart/ajax', [CartController::class, 'ajaxCart'])->name('cart.ajax');


Route::delete('/cart/remove/{id}', [CartController::class, 'removeFromCartAjax'])->name('cart.remove.ajax');

Route::get('/cart/count', [CartController::class, 'getCartItemCount'])->name('cart.count');
Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::get('/cart/checkout/order-received', [OnlPaymentController::class, 'checkoutthank'])->name('thankyouvnpay');



Route::controller(SocialController::class)->group(function(){
    Route::get('auth/google', 'redirectToGoogle')->name('auth.google');
    Route::get('auth/google/callback', 'handleGoogleCallback');
});

Route::get('/news', [NAController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [NAController::class, 'show'])->name('news.show');
Route::get('/address', [NAController::class, 'addressIndex'])->name('address.index');

Route::post('/contact-submit', [NAController::class, 'contactSSubmit'])->name('contact.submit');
