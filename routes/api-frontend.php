<?php

use App\Http\Controllers\API\Frontend\ProductController;
use App\Http\Controllers\API\Frontend\UserController;
use App\Http\Controllers\API\Frontend\BrandController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['auth:api', 'user-role'],
    'prefix' => 'frontend',
    'as' => 'frontend.'
], function () {
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);

    Route::get('brands', [BrandController::class, 'index']);
    Route::get('brands/{id}', [BrandController::class, 'show']);

    Route::get('users', [UserController::class, 'showMyProfile']);
    Route::put('users', [UserController::class, 'updateMyProfile']);
});

Route::group([
    'middleware' => ['client'],
    'prefix' => 'company',
    'as' => 'company.'
], function () {
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);
});
