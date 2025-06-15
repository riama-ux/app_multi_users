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


/*
|--------------------------------------------------------------------------
| Module Produit
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'user-access:Admin,Supervisor'])->prefix('module')->name('module.')->group(function () {
    Route::resource('produits', App\Http\Controllers\Module\ProduitController::class);
});

// Accès lecture seule pour Manager
Route::middleware(['auth', 'user-access:Manager'])
    ->prefix('module/manager') // <- chemin URL différent
    ->name('manager.module.')  // <- nom de route différent
    ->group(function () {
        Route::resource('produits', App\Http\Controllers\Module\ProduitController::class)
            ->only(['index', 'show']);
    });


/*
|--------------------------------------------------------------------------
| Module Catégorie
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'user-access:Admin,Supervisor'])->prefix('module')->name('module.')->group(function () {
    Route::resource('categories', App\Http\Controllers\Module\CategorieController::class);
});


/*
|--------------------------------------------------------------------------
| Module Fournisseur
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'user-access:Admin,Supervisor'])->prefix('module')->name('module.')->group(function () {
    Route::resource('fournisseurs', App\Http\Controllers\Module\FournisseurController::class);
});

/*
|--------------------------------------------------------------------------
| Module Client
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'user-access:Admin,Supervisor,Manager'])->prefix('module')->name('module.')->group(function () {
    Route::resource('clients', App\Http\Controllers\Module\ClientController::class);
});

/*
|--------------------------------------------------------------------------
| Module Stock
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'user-access:Admin,Supervisor'])->prefix('module')->name('module.')->group(function () {
    Route::resource('stocks', App\Http\Controllers\Module\StockController::class)->except(['create', 'store', 'show', 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Module Vente
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'user-access:Admin,Supervisor,Manager'])->prefix('module')->name('module.')->group(function () {
    Route::resource('ventes', App\Http\Controllers\Module\VenteController::class);
});

/*
|--------------------------------------------------------------------------
| Module Crédit
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'user-access:Admin,Supervisor,Manager'])->prefix('module')->name('module.')->group(function () {
    Route::resource('credits', App\Http\Controllers\Module\CreditController::class)->only(['index', 'edit', 'update']);
});

/*
|--------------------------------------------------------------------------
| Module Pertes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'user-access:Admin,Supervisor'])->prefix('module')->name('module.')->group(function () {
    Route::resource('pertes', App\Http\Controllers\Module\PerteController::class)->only(['index', 'create', 'store', 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Module Commande
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'user-access:Admin,Supervisor'])->prefix('module')->name('module.')->group(function () {
    Route::resource('commandes', App\Http\Controllers\Module\CommandeController::class);
});

/*
|--------------------------------------------------------------------------
| Module Transfert
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'user-access:Admin,Supervisor'])->prefix('module')->name('module.')->group(function () {
    Route::resource('transferts', App\Http\Controllers\Module\TransfertController::class)->only(['index', 'create', 'store', 'destroy']);
});
