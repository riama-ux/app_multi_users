<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\Models\Magasin;
use App\Models\Ajustement;
use App\Models\Commande; // Importez vos modèles pertinents
use App\Models\Vente;
use App\Models\RetourClient;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        Schema::defaultStringLength(191);

        View::composer('*', function ($view) {
            $magasinActif = Magasin::find(session('magasin_actif_id'));
            $view->with('magasinActif', $magasinActif);
        });


        // Définir les mappages polymorphiques
        Relation::morphMap([
            'commande' => 'App\Models\Commande', // Quand source_type est 'commande', utiliser App\Models\Commande
            'vente' => 'App\Models\Vente',       // Si vous avez un type 'vente' pour les ventes
            'transfert' => 'App\Models\Transfert', // Si vous avez un type 'transfert'
            'ajustement' => 'App\Models\Ajustement', // Si vous avez un type 'ajustement'
            'retour_client' => 'App\Models\RetourClient',
            // Ajoutez ici tous les autres types de source_type que vous utilisez
        ]);
    }
}
