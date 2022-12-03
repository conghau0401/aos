<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Install app cafe24
Route::get("install", [Controllers\Admin\InstallController::class, 'install'])->name('install');

// Admin dashboard
// Route::get("admin", [Controllers\Admin\LoginController::class, 'index'])->name('admin.login');
// Route::middleware(['auth.admin'])->prefix('admin')->group(function () {
//     Route::resource('products', Controllers\Admin\ProductController::class);
//     Route::post("product/upload-image", [Controllers\Admin\AjaxController::class, 'uploadProductImage']);
//     Route::post("product/delete-variant", [Controllers\Admin\AjaxController::class, 'deleteVariant']);
//     Route::resource('category', Controllers\Admin\CategoryController::class);
//     Route::resource('banners', Controllers\Admin\BannerController::class);
//     Route::post("banner/upload-image", [Controllers\Admin\AjaxController::class, 'uploadBannerImage']);
//     Route::post("banner/delete-image", [Controllers\Admin\AjaxController::class, 'deleteBannerImage']);
// });

// popup
Route::get("popup-address", [Controllers\MainController::class, 'popup']);
Route::post("popup-address", [Controllers\MainController::class, 'popup']);
Route::post("order-form", [Controllers\MainController::class, 'paymentResult']);

// Route for vuejs
Route::view('/{any}', 'app')->where('any', '.*');
