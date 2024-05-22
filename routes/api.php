<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CitiesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProducerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Models\Product;
use GuzzleHttp\Psr7\Response;


Route::get('/products', function () {
    $products = Product::get();
    return response()->json($products ?? []);
});
Route::get('/users', [UserController::class, 'index']);
Route::get('/user/{id}', [UserController::class, 'show']);
Route::put('/user/{id}', [UserController::class, 'update']);
Route::post('/user/register', [UserController::class, 'store']);
Route::post('/user/login', [UserController::class, 'login']);
// admins
Route::get('/admins', [AdminController::class, 'index']);
Route::get('/admins/{id}', [AdminController::class, 'show']);
Route::put('/admins/{id}', [AdminController::class, 'update']);
Route::post('/admins/add', [AdminController::class, 'store']);

//dashboard
Route::controller(DashboardController::class)->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
    });
    Route::get('/total/product', 'totalProducts');
    Route::get('/total/client', 'totalClients');
    Route::get('/total/user', 'totalUsers');
    Route::get('/total/order', 'totalOrders');
    Route::get('/top/product', 'getTopSellingProducts');
    Route::get('/stock/product', 'getAvailableProducts');
    Route::get('/credit/clients', 'getClientCredit');
    Route::get('/order/check', 'getPaymentOrders');
    Route::get('/order/facture', 'getFactureOrders');
});
//PRODUCTS
Route::controller(ProductController::class)->group(function () {
    Route::get('/products', 'index');
    Route::get('/products/get/{id}', 'edit');
    Route::post('/products/add', 'store');
    Route::post('/products/edit/{id}', 'edit');
    Route::get('/products/details/{id}', 'show');
    Route::post('/products/update/{id}', 'update');
    Route::delete('/products/delete/{id}', 'delete');
    Route::get('/products/{id}', 'getProductById');
});
//producers
Route::controller(ProducerController::class)->group(function () {
    Route::get('/producers', 'index');
    Route::post('/producers/add', 'store');
    Route::post('/producers/edit/{id}', 'edit');
    Route::delete('/producers/delete/{id}', 'delete');
    Route::post('/producers/update/{id}', 'update');
});
//categories
Route::controller(CategoryController::class)->group(function () {
    Route::get('/categories', 'index');
    Route::post('/categories/add', 'store');
    Route::post('/categories/edit/{id}', 'edit');
    Route::delete('/categories/delete/{id}', 'delete');
    Route::post('/categories/update/{id}', 'update');
});
//cities
Route::controller(CitiesController::class)->group(function () {
    Route::get('/cities', 'index');
    Route::get('/cities/{id}', 'getCityById');
    Route::post('/cities/add', 'store');
    Route::post('/cities/edit/{id}', 'edit');
    Route::delete('/cities/delete/{id}', 'delete');
    Route::put('/cities/update/{id}', 'update');
});
Route::get('/products/{id}', [ProductController::class, 'show']);