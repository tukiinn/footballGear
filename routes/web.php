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
use App\Http\Controllers\Admin\AdminVoucherController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\AdminSearchController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\NAController;
use App\Http\Controllers\StatisticsController;

// Route nhóm cho admin
Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
    // Trang dashboard
    Route::get('/', [StatisticsController::class, 'dashboard'])->name('dashboard');

    // Quản lý sản phẩm
    Route::resource('products', AdminProductController::class);
      // Các route cho quản lý size của sản phẩm qua AJAX
      Route::patch('products/{productId}/sizes/{sizeId}', [AdminProductController::class, 'updateStock'])
      ->name('admin.products.sizes.update');
  Route::post('products/{productId}/sizes', [AdminProductController::class, 'storeSize'])
      ->name('admin.products.sizes.store');
  Route::delete('products/{productId}/sizes/{sizeId}', [AdminProductController::class, 'destroySize'])
      ->name('admin.products.sizes.destroy');

    // Quản lý danh mục
    Route::resource('categories', AdminCategoryController::class);

       // Quản lý đơn hàng
       Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
       Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
       
   
   Route::post('/orders/{order}/confirm', [AdminOrderController::class, 'confirm'])->name('orders.confirm');
   Route::post('/orders/{order}/ship', [AdminOrderController::class, 'ship'])->name('orders.ship');
   Route::post('/orders/{order}/complete', [AdminOrderController::class, 'complete'])->name('orders.complete');
   Route::post('/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
   
   Route::post('/orders/bulk-confirm', [AdminOrderController::class, 'bulkConfirm'])->name('orders.bulkConfirm');
   Route::post('/orders/bulk-ship', [AdminOrderController::class, 'bulkShip'])->name('orders.bulkShip');
   Route::post('/orders/bulk-complete', [AdminOrderController::class, 'bulkComplete'])->name('orders.bulkComplete');
   Route::post('/orders/bulk-cancel', [AdminOrderController::class, 'bulkCancel'])->name('orders.bulkCancel');
   

    Route::resource('news', NewsController::class);

    Route::resource('vouchers', AdminVoucherController::class);

    Route::get('/search', [AdminSearchController::class, 'search']);
});
Route::get('/admin/revenue-data', [StatisticsController::class, 'getRevenueData']);
Route::get('/admin/category-revenue-data', [StatisticsController::class, 'getCategoryRevenueForChart']);
Route::get('/admin/payment-revenue-data', [StatisticsController::class, 'getPaymentMethodRevenueData']);
Route::get('/wel', function () {
    return view('welcome');
});
Route::get('shop', [HomeController::class, 'index'])->name('home');

Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login'); 
Route::post('login', [AuthController::class, 'login']);  
Route::post('logout', [AuthController::class, 'logout'])->name('logout'); 


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [NAController::class, 'search'])->name('products.search');
Route::post('upload', [NAController::class, 'upload'])->name('upload');



// Hiển thị form nhập email để nhận liên kết đặt lại mật khẩu
Route::get('password/reset', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [AuthController::class, 'reset'])->name('password.update');

Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login'); 
Route::post('login', [AuthController::class, 'login']);  
Route::post('logout', [AuthController::class, 'logout'])->name('logout'); 



Route::resource('products', ProductController::class);
Route::get('/recently-viewed', [ProductController::class, 'recentlyViewed'])->name('recently_viewed');


Route::resource('categories', CategoryController::class);
Route::resource('news', NAController::class);

Route::get('/vnpay_return', [OnlPaymentController::class, 'vnpayReturn'])->name('vnpay.return');
Route::post('/apply-voucher', [VoucherController::class, 'applyVoucher'])->name('voucher.apply');


 // Route cho VNPay thanh toán và trả về
 Route::get('/payment', [OrderController::class, 'paymentPage'])->name('payment.index');
 Route::get('/vnpay_return', [OnlPaymentController::class, 'vnpayReturn'])->name('vnpay.return');
 
 Route::post('/vnpay', [OnlPaymentController::class, 'vnpayment'])->name('vnpay.vn');
 Route::post('/momo', [OnlPaymentController::class, 'momopayment'])->name('momo.vn');
 Route::post('/retry-vnpay', [OnlPaymentController::class, 'retryvnpayment'])->name('retryvnpay.vn');
 Route::post('/retrymomo', [OnlPaymentController::class, 'retrymomo'])->name('retrymomo.vn');

 Route::get('/cart/vnpay/order-received', [OnlPaymentController::class, 'checkoutthank'])->name('thankyouvnpay');
Route::get('/cart/momo/order-received', [OnlPaymentController::class, 'thankmomo'])->name('thankyoumomo');

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
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancelOrder'])->name('orders.cancel');
    Route::post('/orders/{id}/payCOD', [OrderController::class, 'payCOD'])->name('order.payCOD');
 // Route cho VNPay thanh toán và trả về
Route::get('/payment', [OrderController::class, 'paymentPage'])->name('payment.index');




Route::delete('/cart/remove/{id}', [CartController::class, 'removeFromCartAjax'])->name('cart.remove.ajax');

Route::get('/cart/count', [CartController::class, 'getCartItemCount'])->name('cart.count');
Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');


