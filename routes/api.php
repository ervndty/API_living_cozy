<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistItemController;
use App\Http\Controllers\ShoppingCartController;
use App\Http\Controllers\PaymentsController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
});


Route::apiResource('categories', CategoryController::class);

Route::get('products', [ProductController::class, 'index']);
Route::get('products/{id}', [ProductController::class, 'show']);
Route::get('products/category/{category_id}', [ProductController::class, 'getByCategory']);
Route::get('categories', [ProductController::class, 'getAllCategories']);


Route::get('products/{productId}/images', [ProductImageController::class, 'index']);
Route::post('products/{productId}/images', [ProductImageController::class, 'store']);
Route::delete('products/{productId}/images/{imageId}', [ProductImageController::class, 'destroy']);


Route::get('/orders', [OrderController::class, 'index']);
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{id}', [OrderController::class, 'show']);
Route::put('/orders/{id}', [OrderController::class, 'update']);
Route::delete('/orders/{id}', [OrderController::class, 'destroy']);



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

Route::get('/shopping-carts', [ShoppingCartController::class, 'index']);
Route::post('/shopping-carts', [ShoppingCartController::class, 'store']);
Route::get('/shopping-carts/{id}', [ShoppingCartController::class, 'show']);
Route::put('/shopping-carts/{id}', [ShoppingCartController::class, 'update']);
Route::delete('/shopping-carts/{id}', [ShoppingCartController::class, 'destroy']);



Route::post('/payments', [PaymentsController::class, 'create']);
Route::get('/payments/{order_id}', [PaymentsController::class, 'getPaymentStatus']);