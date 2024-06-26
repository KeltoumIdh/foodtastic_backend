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


// Route::get('/products', function () {
//     $products = Product::get();
//     return response()->json($products ?? []);
// });
Route::get('/users', [UserController::class, 'index']);
Route::get('/user/{id}', [UserController::class, 'show']);
Route::put('/user/{id}', [UserController::class, 'update']);
Route::post('/user/register', [UserController::class, 'store']);
Route::post('/user/login', [UserController::class, 'login']);
Route::delete('/user/{id}', [UserController::class, 'destroy']);
// admins
Route::get('/admins', [AdminController::class, 'index']);
Route::get('/admins/{id}', [AdminController::class, 'show']);
Route::put('/admins/{id}', [AdminController::class, 'update']);
Route::post('/admins/add', [AdminController::class, 'store']);
Route::get('/admins/edit/{id}', [AdminController::class, 'edit']);
Route::delete('/admins/delete/{id}', [AdminController::class, 'delete']);

//dashboard
Route::controller(DashboardController::class)->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
    });
    Route::get('/total/product', 'totalProducts');
    Route::get('/total/client', 'totalClients');
    Route::get('/total/user', 'totalUsers');
    Route::get('/total/order', 'totalOrders');
    Route::get('/top/product', 'getTopSellingProducts');
    Route::get('/top/product/categ', 'getQuantitySoldByCategory');
    Route::get('/quantity/sold/producer',  'getQuantitySoldByProducer');
    Route::get('/stock/product', 'getAvailableProducts');
    Route::get('/credit/clients', 'getClientCredit');
    Route::get('/order/check', 'getPaymentOrders');
    Route::get('/order/facture', 'getFactureOrders');
});
//PRODUCTS
Route::controller(ProductController::class)->group(function () {
    Route::get('/products', 'index');
    Route::get('/productsByCity', 'GetByCity');
    Route::get('/productsByCateg', 'GetByCateg');
    Route::get('/products/get/{id}', 'edit');
    Route::post('/products/add', 'store');
    Route::post('/products/edit/{id}', 'edit');
    Route::get('/products/details/{id}', 'show');
    Route::post('/products/update/{id}', 'update');
    Route::delete('/products/delete/{id}', 'delete');
    Route::get('/products/{id}', 'getProductById');
    Route::get('/products/filter', 'getFiltredProducts');
});
//producers
Route::controller(ProducerController::class)->group(function () {
    Route::get('/producers', 'index');
    Route::post('/producers/add', 'store');
    Route::get('/producers/edit/{id}', 'edit');
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
//orders
Route::controller(OrderController::class)->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
    });
    Route::post('/new-order', 'multipleOrders');
});