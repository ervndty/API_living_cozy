<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistItemController;
use App\Http\Controllers\ShoppingCartController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Api\AdminAuthController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/user/count', [AuthController::class, 'getUserCount']);
Route::get('users', [AuthController::class, 'getAllUsers']);
Route::delete('/users/{id}', [AuthController::class, 'deleteUser']);
Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile/update', [AuthController::class, 'updateProfile']);
});



Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');



Route::get('/categories/count', [CategoryController::class, 'getCategoryCount']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
Route::get('/categories/name/{name}', [CategoryController::class, 'getByName']);


Route::get('/products/count', [ProductController::class, 'getProductCount']);
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{id}', [ProductController::class, 'show']);
Route::get('products/category/{category_id}', [ProductController::class, 'getByCategory']);
Route::post('/products', [ProductController::class, 'store']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);

Route::get('products/{productId}/images', [ProductImageController::class, 'index']);
Route::post('products/{productId}/images', [ProductImageController::class, 'store']);
Route::delete('products/{productId}/images/{imageId}', [ProductImageController::class, 'destroy']);
Route::put('/products/{productId}/images/{imageId}', [ProductImageController::class, 'update']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}', [OrderController::class, 'update']);
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
});


Route::get('reviews', [ReviewController::class, 'index']);
Route::post('reviews', [ReviewController::class, 'store']);
Route::get('reviews/{id}', [ReviewController::class, 'show']);
Route::put('reviews/{id}', [ReviewController::class, 'update']);
Route::delete('reviews/{id}', [ReviewController::class, 'destroy']);



Route::get('wishlist-items', [WishlistItemController::class, 'index']);
Route::post('wishlist-items', [WishlistItemController::class, 'store']);
Route::get('wishlist-items/{id}', [WishlistItemController::class, 'show']);
Route::put('wishlist-items/{id}', [WishlistItemController::class, 'update']);
Route::delete('wishlist-items/{id}', [WishlistItemController::class, 'destroy']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/shopping-carts', [ShoppingCartController::class, 'index']);
    Route::post('/shopping-carts', [ShoppingCartController::class, 'store']);
    Route::put('/shopping-carts/{id}', [ShoppingCartController::class, 'update']);
    Route::delete('/shopping-carts/{id}', [ShoppingCartController::class, 'destroy']);
});


Route::post('/payment', [PaymentController::class, 'create']);
Route::post('/payment/webhook', [PaymentController::class, 'webhook']);
Route::get('/payment/history', [PaymentController::class, 'history']);
Route::post('/payment/approve/{id}', [PaymentController::class, 'approveTransaction']);
Route::get('/payment/all-history', [PaymentController::class, 'getAllTransactions']); 
Route::delete('payment/{id}', [PaymentController::class, 'destroy']);
Route::get('payment/count', [PaymentController::class, 'getTranscationCount']);