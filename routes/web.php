<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TiketController;
use App\Http\Controllers\Users\TiketsController;
use App\Http\Controllers\Users\UsersController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Users\DivisiController;
use App\Http\Controllers\Users\SubmissionController;
use Illuminate\Support\Facades\Auth;

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
Auth::routes();
Route::middleware(['throttle:60,1'])->group(function () {
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});


Route::get('/', function () {
    return view('welcome');
})->name('welcome');


Route::middleware(['auth', 'user-role:2', 'throttle:60,1'])->group(function () {
    Route::get('/ajukan/user', [TiketsController::class, 'create'])->name('user.ajukan');
    Route::post('/store/user', [TiketsController::class, 'store'])->name('user.store');
    Route::get('/tiket/user/{id}', [TiketsController::class, 'tampilkan'])->name('user.tampilkan');
    Route::post('/tiket/user/reply', [TiketsController::class, 'reply'])->name('user.reply');
    Route::post('/tiket/user/storeppkb', [SubmissionController::class, 'storeppkb'])->name('user.storeppkb');
    Route::get('/submission/user', [SubmissionController::class, 'createppkb'])->name('user.submission');
    Route::get('/daftar/submission', [SubmissionController::class, 'daftarsubmission'])->name('user.daftarsubmission');
    Route::get('/submission/user/{id}', [SubmissionController::class, 'tampilkansubmission'])->name('user.tampilkansubmission');
    Route::put('/submission/status-update/{id}', [SubmissionController::class, 'updatesubmission'])->name('submission.update');
    Route::get('/submission/user/{id}', [SubmissionController::class, 'tampilkanSubmissionUser'])->name('user.tampilkanSubmissionUser');
    Route::get('/submission', [SubmissionController::class, 'submissionsaya'])->name('user.submissionsaya');
    Route::get('submissions/export/{user_id}', [SubmissionController::class, 'export']);
    Route::get('/tiketsaya', [TiketsController::class, 'tiketsaya'])->name('user.tiketsaya');
}); 

Route::middleware(['auth', 'user-role:1', 'throttle:60,1'])->group(function () {
    Route::get('/users', [UsersController::class, 'index'])->name('users.index');
    Route::get('/divisi', [DivisiController::class, 'index'])->name('divisi.index');
    Route::put('/tiket/{tiket}/update-prioritas', [TiketController::class, 'updatePrioritas'])->name('tiket.updatePrioritas');
    Route::get('/ajukan', [TiketController::class, 'create'])->name('tiket.ajukan');
    Route::post('/store', [TiketController::class, 'store'])->name('tiket.store');
    Route::get('/daftar', [TiketController::class, 'index'])->name('tiket.daftar');
    Route::post('/tiket/close/{id}', [TiketController::class, 'close'])->name('tiket.close');
    Route::get('/tiket/{tiket}', [TiketController::class, 'show'])->name('tiket.show');
    Route::post('/tiket/reply', [TiketController::class, 'reply'])->name('tiket.reply');
    Route::post('/attach-roles', [UsersController::class, 'updateRole'])->name('attach.roles');
    Route::get('/admin/daftar/submission/', [SubmissionController::class, 'daftarsubmissionAdmin'])->name('admin.daftarsubmission');
    Route::post('/tiket/reopen/{id}', [TiketController::class, 'reopen'])->name('tiket.reopen');
});

Route::middleware(['auth', 'user-role:3'])->group(function () {
    Route::get('/submission/planner/{id}', [SubmissionController::class, 'tampilkansubmission'])->name('user.tampilkansubmission');
    Route::post('/submission/close/{id}', [SubmissionController::class, 'selesai'])->name('submission.close');
    Route::post('/submission/reopen/{id}', [SubmissionController::class, 'reopen'])->name('submission.reopen');
});

Route::resource('tiket', TiketController::class);


// Route::get('/attach-roles', [TiketController::class, 'showAttachRolesForm'])->name('attach.roles.form');


