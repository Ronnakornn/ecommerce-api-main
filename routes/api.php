<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

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

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::post('getToken', [AuthController::class, 'getToken']);

Route::get('/login', function () {
    return response()->json([
        'success' => false,
        'message' => 'Unauthorized',
    ], 401);
})->name('login');

Route::middleware('auth:api')->group(function () {
    Route::post('create-secret-key', [AuthController::class, 'generateKey']);
    Route::post('logout', [AuthController::class, 'logout']);
});

require __DIR__ . '/api-backend.php';
require __DIR__ . '/api-frontend.php';
