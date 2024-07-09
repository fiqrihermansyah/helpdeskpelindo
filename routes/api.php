<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApiController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'auth'], static function () {
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('auth.logout');
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/priorities', [ApiController::class, 'getPrioritas'])->name('priorities.index');
    Route::get('/divisions', [ApiController::class, 'getDivisi'])->name('divisions.index');
    Route::post('/tickets', [ApiController::class, 'storeTicket'])->name('tickets.store');
    Route::get('/tickets', [ApiController::class, 'getTickets'])->name('tickets.index');
});


