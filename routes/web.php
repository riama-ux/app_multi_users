<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SwitchMagasinController;
use App\Http\Controllers\Gestion\ProduitController;
use App\Http\Controllers\Gestion\CommandeController;
use App\Http\Controllers\Gestion\MouvementStockController;
use App\Http\Controllers\Gestion\StockController;
use App\Http\Controllers\Gestion\VenteController;
use App\Http\Controllers\Gestion\PaiementController;
use App\Http\Controllers\Gestion\StockLotController;
use App\Http\Controllers\Gestion\TransfertController;
use App\Http\Controllers\Gestion\RetourClientController;
use App\Http\Controllers\Gestion\AjustementController;

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




Route::post('/switch-magasin', [SwitchMagasinController::class, 'set'])
->name('switch.magasin')
->middleware('auth');


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
        Route::resource('magasins', App\Http\Controllers\Admin\MagasinController::class)->names('magasins');
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





Route::middleware(['auth', 'check-magasin','user-access:Admin,Supervisor'])->group(function() {
    Route::resource('produits', ProduitController::class);

    // routes/web.php ou api.php selon préférence
    Route::get('/produit/info', [App\Http\Controllers\Gestion\ProduitController::class, 'getProduitInfo'])->name('produit.info');
    // Routes pour restauration et suppression définitive
    Route::post('produits/{id}/restore', [ProduitController::class, 'restore'])->name('produits.restore');
    Route::delete('produits/{id}/force-delete', [ProduitController::class, 'forceDelete'])->name('produits.forceDelete');
});



Route::middleware(['auth', 'check-magasin','user-access:Admin,Supervisor'])->group(function () {
    Route::resource('commandes', CommandeController::class);

    Route::post('/commandes/{commande}/reception', [CommandeController::class, 'reception'])
    ->name('commandes.reception');

    

    Route::prefix('ajustements')->name('ajustements.')->group(function () {
        Route::get('/', [AjustementController::class, 'index'])->name('index');
        Route::get('/create', [AjustementController::class, 'create'])->name('create');
        Route::post('/', [AjustementController::class, 'store'])->name('store');
        Route::get('/{ajustement}', [AjustementController::class, 'show'])->name('show');
        Route::get('/{ajustement}/edit', [AjustementController::class, 'edit'])->name('edit');
        Route::put('/{ajustement}', [AjustementController::class, 'update'])->name('update');
        Route::delete('/{ajustement}', [AjustementController::class, 'destroy'])->name('destroy');
    });
});



Route::middleware(['auth', 'check-magasin','user-access:Admin,Supervisor'])->group(function () {
    Route::get('stocks', [StockController::class, 'index'])->name('stocks.index');
    Route::get('produits/{produit}/lots', [StockLotController::class, 'index'])->name('stock_lots.index');
    Route::get('mouvements-stock', [MouvementStockController::class, 'index'])->name('mouvements_stock.index');
});



Route::middleware(['auth', 'check-magasin','user-access:Admin,Supervisor'])->group(function() {
    Route::resource('ventes', VenteController::class)->except(['destroy']);
    
    // Optionnel: route pour suppression via ajustement (retour client)
    /*Route::post('ventes/{vente}/retour-client', [VenteController::class, 'retourClient'])->name('ventes.retour-client');*/
});


Route::middleware(['auth', 'check-magasin','user-access:Admin,Supervisor'])->prefix('module')->name('module.')->group(function () {
    Route::resource('fournisseurs', App\Http\Controllers\Module\FournisseurController::class)->parameters(['fournisseurs' => 'fournisseur']);
});



Route::middleware(['auth', 'check-magasin','user-access:Admin,Supervisor'])->prefix('module')->name('module.')->group(function () {
    Route::resource('categories', App\Http\Controllers\Module\CategorieController::class) ->parameters(['categories' => 'categorie']);
    Route::post('/categories/store-ajax', [CategorieController::class, 'storeAjax'])->name('categories.store.ajax');
});


Route::middleware(['auth', 'check-magasin','user-access:Admin,Supervisor,Manager'])->prefix('module')->name('module.')->group(function () {
    Route::resource('clients', App\Http\Controllers\Module\ClientController::class);
});




Route::middleware(['auth', 'check-magasin','user-access:Admin,Supervisor,Manager'])->group(function() {
    Route::post('/ventes/{vente}/paiements', [PaiementController::class, 'store'])->name('paiements.store');
    Route::post('/paiements/{paiement}/annuler', [PaiementController::class, 'annulerPaiement'])->name('paiements.annuler');

});

Route::middleware(['auth', 'check-magasin','user-access:Admin,Supervisor,Manager'])->prefix('transferts')->name('transferts.')->group(function() {
    Route::get('/', [TransfertController::class, 'index'])->name('index');
    Route::get('/create', [TransfertController::class, 'create'])->name('create');
    Route::post('/', [TransfertController::class, 'store'])->name('store');
    Route::get('/{transfert}', [TransfertController::class, 'show'])->name('show');
    Route::get('/{transfert}/edit', [TransfertController::class, 'edit'])->name('edit');
    Route::put('/{transfert}', [TransfertController::class, 'update'])->name('update');
    Route::delete('/{transfert}', [TransfertController::class, 'destroy'])->name('destroy');
     Route::get('/api/produits/recherche', [TransfertController::class, 'searchProducts'])->name('api.produits.recherche');

    // Validation
    Route::post('/{transfert}/valider', [TransfertController::class, 'valider'])->name('valider');
});


Route::middleware(['auth', 'check-magasin', 'user-access:Admin,Supervisor,Manager'])->group(function () {
    // Routes pour les Retours Clients
    Route::resource('retours_clients', RetourClientController::class);

    // Ajout d'une route pour initier un retour depuis une vente spécifique
    Route::get('ventes/{vente}/retour', [RetourClientController::class, 'create'])->name('ventes.retour.create');

    // Routes pour les Ajustements de Stock
    Route::resource('ajustements', AjustementController::class);

    // ... vos autres routes (commandes, ventes, etc.)
});


/*

Route::middleware(['auth', 'check-magasin','user-access:Admin,Supervisor'])->prefix('module')->name('module.')->group(function () {
    Route::resource('produits', App\Http\Controllers\Module\ProduitController::class);

    Route::post('/produits/{id}/restore', [App\Http\Controllers\Module\ProduitController::class, 'restore'])->name('produits.restore');
});

// Accès lecture seule pour Manager
Route::middleware(['auth', 'check-magasin','user-access:Manager'])
    ->prefix('module/manager') // <- chemin URL différent
    ->name('manager.module.')  // <- nom de route différent
    ->group(function () {
        Route::resource('produits', App\Http\Controllers\Module\ProduitController::class)->only(['index', 'show']);
    });



Route::middleware(['auth', 'check-magasin','user-access:Admin,Supervisor'])->prefix('module')->name('module.')->group(function () {
    Route::resource('categories', App\Http\Controllers\Module\CategorieController::class) ->parameters(['categories' => 'categorie']);
});



Route::middleware(['auth', 'check-magasin','user-access:Admin,Supervisor'])->prefix('module')->name('module.')->group(function () {
    Route::resource('fournisseurs', App\Http\Controllers\Module\FournisseurController::class)->parameters(['fournisseurs' => 'fournisseur']);
});


Route::middleware(['auth', 'check-magasin','user-access:Admin,Supervisor,Manager'])->prefix('module')->name('module.')->group(function () {
    Route::resource('clients', App\Http\Controllers\Module\ClientController::class);
});


Route::middleware(['auth', 'check-magasin','user-access:Admin,Supervisor'])->prefix('module')->name('module.')->group(function () {
    Route::resource('stocks', App\Http\Controllers\Module\StockController::class);
});


Route::middleware(['auth', 'check-magasin','user-access:Admin,Supervisor,Manager'])->prefix('module')->name('module.')->group(function () {
    Route::resource('ventes', App\Http\Controllers\Module\VenteController::class);

    Route::get('ventes/{vente}/recu', [App\Http\Controllers\Module\VenteController::class, 'imprimer'])->name('ventes.recu');
});



Route::middleware(['auth', 'check-magasin','user-access:Admin,Supervisor,Manager'])->prefix('module')->name('module.')->group(function () {
    Route::resource('credits', App\Http\Controllers\Module\CreditController::class);
});

/
Route::middleware(['auth', 'check-magasin','user-access:Admin,Supervisor'])->prefix('module')->name('module.')->group(function () {
    Route::resource('pertes', App\Http\Controllers\Module\PerteController::class);
});


Route::middleware(['auth', 'check-magasin','user-access:Admin,Supervisor'])->prefix('module')->name('module.')->group(function () {
    Route::resource('commandes', App\Http\Controllers\Module\CommandeController::class);

    Route::post('commandes/{id}/recevoir', [CommandeController::class, 'recevoir'])->name('commandes.recevoir');
});


Route::middleware(['auth', 'check-magasin', 'user-access:Admin,Supervisor'])->prefix('module')->name('module.')->group(function () {
    Route::resource('transferts', App\Http\Controllers\Module\TransfertController::class)->parameters(['transferts' => 'transfert']);

    // Transferts : réception (valider le transfert)
    Route::post('transferts/{transfert}/valider', [TransfertController::class, 'valider'])
        ->name('transferts.valider');
});
*/
