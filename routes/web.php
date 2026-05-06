<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\isAdmin;
use App\Http\Middleware\isMember;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BlogController; 
use App\Http\Controllers\HomeController; 
use App\Http\Controllers\CustomCategoryController; 
use App\Http\Controllers\ProductCartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\HelpPageController;
use App\Http\Controllers\AboutPageController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\NewsController;

Route::prefix('home')->group(function() {

   
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::middleware(['auth',isMember::class])->group(function () {
        Route::get('/edit',[HomeController::class,'editInformation'])->name('editInformation');
    
        Route::post('/update', [HomeController::class, 'updateInformation'])->name('updateInformation');
        
        Route::get('/editpassword',[HomeController::class,'editPassword'])->name('editPassword');
        
        Route::post('/updatepassword',[HomeController::class,'updatePassword'])->name('updatePassword');

        Route::get('checkout', [CheckoutController::class, 'show'])->name('checkout');

        Route::post('checkout', [CheckoutController::class, 'checkout'])->name('checkout.handle');

        Route::post('checkout/shipping-fee', [CheckoutController::class, 'shippingFee'])->name('checkout.shipping_fee');
        Route::post('checkout/calculate-shipping', [CheckoutController::class, 'calculateShippingByLocation'])->name('checkout.calculate_shipping');

        Route::get('locations/provinces', [CheckoutController::class, 'getProvinces'])->name('locations.provinces');
        Route::get('locations/{province}/districts', [CheckoutController::class, 'getDistricts'])->name('locations.districts');
        Route::get('locations/{province}/{district}/wards', [CheckoutController::class, 'getWards'])->name('locations.wards');

        Route::get('vnpay/return', [CheckoutController::class, 'vnpayReturn'])->name('vnpay.return');

        Route::get('order', [OrderController::class, 'index'])->name('order');
        
        // Show cart cho user
        Route::get('/show-CartProducts', [ProductCartController::class, 'index'])->name('showCart');

        // Thêm sản phẩm vào giỏ hàng
        Route::get('/products/add-to-cart/{id}', [CustomCategoryController::class, 'addToCart'])->name('addToCart');

        // Cập nhật giỏ hàng (AJAX)
        Route::post('/cart/update', [ProductCartController::class, 'updateCart'])->name('cart.update');

        // Xoá sản phẩm khỏi giỏ hàng (AJAX)
        Route::get('/delete/cart', [ProductCartController::class, 'deleteCart'])->name('cart.delete');
        //Trang liên hệ
        
    });
    
    Route::get('/lienhe', [HelpPageController::class, 'index'])->name('helppage.index');
    Route::get('/gioithieu', [AboutPageController::class, 'index'])->name('about.index');
    Route::get('/news', [NewsController::class, 'index'])->name('news.index');
    Route::get('product/{product}', [HomeController::class, 'show'])->name('product');
        
    Route::get('/category', [CustomCategoryController::class, 'index'])->name('custom.category.index');

    Route::get('/category/{id}', [CustomCategoryController::class, 'listproduct'])->name('category.product');
    

});


Route::get('/login', [AuthController::class, 'Showform_login'])->name('login');
Route::get('/register', [AuthController::class, 'Showform_register'])->name('register');

Route::post('/login', [AuthController::class, 'handle_login']);
Route::post('/register', [AuthController::class, 'handle_register']);

Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('google', [GoogleController::class, 'redirect'])->name('google');
Route::get('google/callback', [GoogleController::class, 'callback']);

Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('showForgotForm');
Route::post('/forgot-password', [AuthController::class, 'sendResetCode'])->name('sendResetCode');
Route::get('/verify-code', [AuthController::class, 'showVerifyForm'])->name('showVerifyForm');
Route::post('/verify-code', [AuthController::class, 'verifyCode'])->name('verifyCode');



Route::middleware(['auth',isAdmin::class])->prefix('admin')->group(function () {

    Route::get('/', [StatisticController::class, 'index'])->name('admin');

    Route::resource('products', ProductController::class);

    Route::resource('categories', CategoryController::class);

    Route::resource('tags', TagController::class);
    

    Route::prefix('banners')->group(function() {
        Route::get('/', [BannerController::class, 'index'])->name('banners.index');

        Route::post('create', [BannerController::class, 'store'])->name('banners.store');

        Route::delete('delete/{banner}', [BannerController::class, 'delete'])->name('banners.destroy');
    });
    // Route::get('/statistics', [StatisticController::class, 'index'])->name('admin.statistics');
    Route::get('/admin/statistics/filter', [StatisticController::class, 'filter'])->name('statistics.filter');
    Route::get('/admin/statistics/export', [StatisticController::class, 'exportExcel'])->name('statistics.export');
    Route::resource('blogs', BlogController::class);
    Route::prefix('setting')->group(function(){
        Route::get('/',[SettingController::class,'index'])->name('settings.index');
        Route::get('/create',[SettingController::class,'create'])->name('settings.create');
        Route::post('/store',[SettingController::class,'store'])->name('settings.store');
        Route::get('/edit/{id}',[SettingController::class,'edit'])->name('setting.edit');
        Route::post('/update/{id}',[SettingController::class,'update'])->name('setting.update');
        Route::delete('delete/{id}', [SettingController::class, 'destroy'])->name('setting.destroy');
    });

    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{id}/approve', [AdminOrderController::class, 'approve'])->name('orders.approve');
    Route::get('orders/{id}/print', [AdminOrderController::class, 'print'])->name('orders.print');

    
});