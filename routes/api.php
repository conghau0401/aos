<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Http\Middleware;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Login
Route::post("login", [Controllers\LoginController::class, 'login'])->name('api.login');
Route::get("logout", [Controllers\LoginController::class, 'logout'])->name('api.logout');

// API hook Cafe24 <==> Doosoun
Route::prefix('v2')->group(function () {
    // test
    Route::get("test", [Controllers\Api\TestController::class, 'index'])->name('api.test');

    Route::post("get-token", [Controllers\Api\AuthorizationController::class, 'getToken'])->name('get_token');
    Route::put("token-local", [Controllers\Api\AuthorizationController::class, 'tokenLocal'])->name('token_local');
    Route::post("oauth/token", [Controllers\Api\AuthorizationController::class, 'refreshToken'])->name('refresh_foken');
    Route::middleware([Middleware\AuthApi::class])->group(function () {
        // Product
        // Route::get("admin/products", [Controllers\Api\APIProductController::class, 'index'])->name('list.product');
        Route::post("admin/products", [Controllers\Api\APIProductController::class, 'create'])->name('create.product');
        Route::get("admin/products-list-id", [Controllers\Api\APIProductController::class, 'listId'])->name('detail.listId');
        Route::get("admin/products/{productNo}", [Controllers\Api\APIProductController::class, 'detail'])->name('detail.product');
        Route::put("admin/products/{productNo}", [Controllers\Api\APIProductController::class, 'update'])->name('update.product');
        Route::delete("admin/products/{productNo}", [Controllers\Api\APIProductController::class, 'delete'])->name('delete.product');
        // Category
        Route::get("admin/categories", [Controllers\Api\APICategoryController::class, 'index'])->name('list.category');
        Route::post("admin/categories", [Controllers\Api\APICategoryController::class, 'create'])->name('create.category');
        Route::get("admin/categories/{categoryNo}", [Controllers\Api\APICategoryController::class, 'detail'])->name('detail.category');
        Route::put("admin/categories/{categoryNo}", [Controllers\Api\APICategoryController::class, 'update'])->name('update.category');
        Route::delete("admin/categories/{categoryNo}", [Controllers\Api\APICategoryController::class, 'delete'])->name('delete.category');
        // Order
        Route::put("admin/orders/shipments", [Controllers\Api\APIOrderController::class, 'updateOrderStatus'])->name('update.order.status');
        Route::post("admin/orders/cancellation-request", [Controllers\Api\APIOrderController::class, 'cancellationRequest'])->name('update.order.cancellationrequest');
        Route::post("admin/orders/cancellation", [Controllers\Api\APIOrderController::class, 'cancellation'])->name('order.cancellation');
        Route::post("admin/orders/exchange", [Controllers\Api\APIOrderController::class, 'exchange'])->name('order.exchange');
        Route::post("admin/orders/return", [Controllers\Api\APIOrderController::class, 'return'])->name('order.return');
        Route::post("admin/orders/return-update", [Controllers\Api\APIOrderController::class, 'returnUpdate'])->name('order.return.update');
    });
});

// API for frontend dev
// check token
Route::get("check-token", [Controllers\CoreController::class, 'check'])->name('check_token');

Route::middleware([Middleware\AuthAOS::class])->group(function () {
    // menu
    Route::get("category-menu", [Controllers\CategoryController::class, 'menu'])->name('category_menu');

    // api/banners
    Route::get("banners", [Controllers\BannerController::class, 'list'])->name('banner_list');
    Route::get("banners/{id}", [Controllers\BannerController::class, 'detail'])->name('banner_detail');

    // api/core/...
    Route::prefix('core')->group(function () {
        Route::get("common-code", [Controllers\CoreController::class, 'commonCode'])->name('common_code');
        Route::get("center-info", [Controllers\CoreController::class, 'centerInfo'])->name('common_center_info');
        Route::get("center-option/{centerCode}", [Controllers\CoreController::class, 'centerOption'])->name('common_center_option');
        Route::post("common-code/type", [Controllers\CoreController::class, 'getByCodeType'])->name('common_code_type');
    });

    // api/product/...
    Route::prefix('product')->group(function () {
        Route::get("product", [Controllers\ProductController::class, 'list'])->name('product_list');
        Route::get("product/{id}", [Controllers\ProductController::class, 'detail'])->name('product_detail');
        // Discount
        Route::get("discount", [Controllers\ProductController::class, 'discount'])->name('product_discount');
        // Products by store size
        Route::get("bundle-storesize", [Controllers\ProductController::class, 'bundleStoreSize'])->name('store_size');
        Route::get("bundle-storesize/{id}", [Controllers\ProductController::class, 'bundleStoreSizeDetail'])->name('store_size_detail');
        // Products by events
        Route::get("recommended-products-by-event", [Controllers\ProductController::class, 'listEvents'])->name('product_list_event');
        Route::get("recommended-products-by-event/detail-products", [Controllers\ProductController::class, 'recommendedProductByEvent'])->name('product_by_event');
        // Wishlist
        Route::get("wishlist", [Controllers\ProductController::class, 'wishlist'])->name('product_wishlist');
        Route::put("add-wishlist", [Controllers\ProductController::class, 'addToWishlist'])->name('product_add_wishlist');
        // New
        Route::get("newest", [Controllers\ProductController::class, 'newest'])->name('product_newest');
        // Best
        Route::get("best/{number}", [Controllers\ProductController::class, 'best'])->name('product_best');
        // Regular delivery
        Route::get("regular", [Controllers\ProductController::class, 'getListRegular'])->name('product_regular_list');
        Route::put("regular-delivery", [Controllers\ProductController::class, 'regularDelivery'])->name('product_regular_delivery');
        // monthly purchase history
        Route::post("monthly-history", [Controllers\ProductController::class, 'monthlyHistory'])->name('product_monthly_history');
        // Barcode
        Route::get("product-option-list-by-barcode", [Controllers\ProductController::class, 'barcode'])->name('product_barcode');
    });

    // api/files/...
    Route::prefix('files')->group(function () {
        // Upload image
        Route::post("upload-image", [Controllers\FileController::class, 'uploadImage'])->name('files_upload_image');
    });

    // api/account/...
    Route::prefix('account')->group(function () {
        // Product deal
        Route::get("product-deal", [Controllers\ProductController::class, 'listDeal'])->name('product_deal');
        Route::get("product-deal/{productDealId}", [Controllers\ProductController::class, 'detailDeal'])->name('product_deal_detail');
    });

    // api/user/...
    Route::prefix('user')->group(function () {
        Route::get("store", [Controllers\UserController::class, 'index'])->name('user_store');
        Route::get("store-settings", [Controllers\UserController::class, 'storeSettings'])->name('user_store_setting');
        Route::put("store", [Controllers\UserController::class, 'updateStore'])->name('user_store_update');
        Route::get("store-deposit-deduction", [Controllers\UserController::class, 'getDepositDeduction'])->name('store_deposit_deduction');
        Route::post("create-deposit", [Controllers\UserController::class, 'createDeposit'])->name('create_deposit');
        Route::post("create-deduction", [Controllers\UserController::class, 'createDeduction'])->name('create_deduction');
        Route::get("credit", [Controllers\UserController::class, 'credit'])->name('credit_user');
        Route::get("store-certificate", [Controllers\UserController::class, 'storeCertificate'])->name('certificate_user');
        Route::get("list-certificate", [Controllers\UserController::class, 'listCertificate'])->name('list_certificate_user');
        // mypage
        Route::get("my-page", [Controllers\UserController::class, 'mypage'])->name('user_mypage');
        Route::get("{id}", [Controllers\UserController::class, 'detail'])->name('user_detail');
        Route::put("update-info", [Controllers\UserController::class, 'updateInfo'])->name('user_update_info');
        Route::put("update-language", [Controllers\UserController::class, 'updateLanguage'])->name('user_language');
    });

    // api/cart/...
    Route::prefix('carts')->group(function () {
        Route::get("/", [Controllers\CartController::class, 'list'])->name('cart_list');
        Route::post("create", [Controllers\CartController::class, 'create'])->name('cart_create');
        Route::post("create-multiple", [Controllers\CartController::class, 'createMultiple'])->name('cart_create_multiple');
        Route::put("update", [Controllers\CartController::class, 'update'])->name('cart_update');
        Route::put("update-list-quantity", [Controllers\CartController::class, 'updateListQuantity'])->name('cart_update_list_quantity');
        Route::delete("delete/{id}", [Controllers\CartController::class, 'delete'])->name('cart_delete');
        // get margin rate to calculate shipping fee
        Route::get("margin-rate", [Controllers\CartController::class, 'getMarginRate'])->name('cart_margin_rate');
    });


    // api/order/...
    Route::prefix('order')->group(function () {
        Route::get("list", [Controllers\OrderController::class, 'list'])->name('order_list');
        Route::get("detail/{id}", [Controllers\OrderController::class, 'detail'])->name('order_detail');
        Route::post("create", [Controllers\OrderController::class, 'create'])->name('order_create');
        Route::post("cancel", [Controllers\OrderController::class, 'cancel'])->name('order_cancel');
        Route::post("cancel-by-ids", [Controllers\OrderController::class, 'cancelByIds'])->name('order_cancel_by_ids');
        Route::post("exchange", [Controllers\OrderController::class, 'exchange'])->name('order_exchange');
        Route::post("return-request", [Controllers\OrderController::class, 'returnRequest'])->name('order_return_request');
        Route::post("return", [Controllers\OrderController::class, 'return'])->name('order_return');
        // payment info
        Route::post("payment-info", [Controllers\OrderController::class, 'getPaymentInfo'])->name('order_payment_info');
        Route::post("payment-result", [Controllers\OrderController::class, 'paymentResult'])->name('order_payment_result');
    });

    // address
    Route::resource('addresses', Controllers\AddressController::class);

    Route::get('/{any}', [Controllers\ProductController::class, 'anyGet'])->where('any', '.*')->name('any_get');
    Route::post('/{any}', [Controllers\ProductController::class, 'anyPost'])->where('any', '.*')->name('any_post');
});
