<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Models\Product;
use GuzzleHttp\Psr7\Response;

// Route::resource('users', UserController::class);
// Route::resource('products', ProductController::class);
// Route::resource('categories', CategoryController::class);
// Route::resource('orders', OrderController::class);

Route::get('/products', function () {
    $products = Product::get();
    return response()->json($products);
});
Route::get('/users', [UserController::class, 'index']);


Route::get('/products/{id}', [ProductController::class, 'show']);