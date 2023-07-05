<?php

use App\Http\Controllers\API\Backend\ProductController;
use App\Http\Controllers\API\Backend\CategoryController;
use App\Http\Controllers\API\Backend\StockController;
use App\Http\Controllers\API\Backend\UserController;
use App\Http\Controllers\API\Backend\BrandController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['auth:api', 'admin-role'],
    'prefix' => 'backend',
    'as' => 'backend.',
], function () {
    Route::apiResource('products', ProductController::class);
    Route::post('products/upload-file', [ProductController::class, 'storeExcel']);
    Route::apiResource('categories', CategoryController::class);;
    Route::apiResource('users', UserController::class);
    Route::apiResource('stocks', StockController::class);
    Route::apiResource('brands', BrandController::class);
});
