<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('auth.login');
})->name('app.home');

/*
|--------------------------------------------------------------------------
| Authentification Routes
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
    Route::post('postLogin', [App\Http\Controllers\AuthController::class, 'postLogin'])->name('postLogin');
    Route::get('logup', [App\Http\Controllers\AuthController::class, 'logup'])->name('logup');
    Route::post('postLogup', [App\Http\Controllers\AuthController::class, 'postLogup'])->name('postLogup');

    Route::post('logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
});
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'user-access:Admin'])->group(function () {
    Route::get('admin', [App\Http\Controllers\HomeController::class, 'adminHome'])->name('admin.home');
    Route::prefix('admin')->name('admin.')->group(function () {

        Route::resource('compte', App\Http\Controllers\Admin\CompteController::class)->except(['show']);
        Route::get('compte/search', [App\Http\Controllers\Admin\CompteController::class, 'search'])->name('compte.search');
        Route::get('admin/edit/{user}', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('user.edit');
        Route::post('admin/update/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('user.update');
    });
});
/*
|--------------------------------------------------------------------------
| Manager Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'user-access:Manager'])->group(function () {
    Route::get('manager', [App\Http\Controllers\HomeController::class, 'managerHome'])->name('manager.home');
    Route::prefix('manager')->name('manager.')->group(function () {

        Route::get('user/edit/{user}', [App\Http\Controllers\Manager\UserController::class, 'edit'])->name('user.edit');
        Route::post('user/update/{user}', [App\Http\Controllers\Manager\UserController::class, 'update'])->name('user.update');
    });
});
/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'user-access:User'])->group(function () {
    Route::get('user', [App\Http\Controllers\HomeController::class, 'userHome'])->name('user.home');
    Route::prefix('user')->name('user.')->group(function () {

        Route::get('user/edit/{user}', [App\Http\Controllers\User\UserController::class, 'edit'])->name('user.edit');
        Route::post('user/update/{user}', [App\Http\Controllers\User\UserController::class, 'update'])->name('user.update');
    });
});
/*
|--------------------------------------------------------------------------
| Supervisor Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'user-access:Supervisor'])->group(function () {
    Route::get('supervisor', [App\Http\Controllers\HomeController::class, 'supervisorHome'])->name('supervisor.home');
    Route::prefix('supervisor')->name('supervisor.')->group(function () {

        Route::get('user/edit/{user}', [App\Http\Controllers\Supervisor\UserController::class, 'edit'])->name('user.edit');
        Route::post('user/update/{user}', [App\Http\Controllers\Supervisor\UserController::class, 'update'])->name('user.update');
    });
});