<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Magasin;
use App\Models\Vente;
use App\Models\Produit;
use App\Models\RetourClient;
use App\Models\Ajustement;
use App\Models\Commande; // Assurez-vous d'avoir un modèle Commande si vous l'utilisez
use App\Models\Paiement; // Pour calculer les dettes ou les paiements
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon; //

class HomeController extends Controller
{
/*
    public function adminHome(){
        $magasinActif = Magasin::find(session('magasin_actif_id'));
        return view('pages/admin/home', compact('magasinActif'));
    }
*/

    public function adminHome(Request $request)
    {
        // Récupère l'ID du magasin actif depuis la session
        $magasinId = session('magasin_actif_id');

        // --- Calcul des statistiques pour les cartes du haut ---

        // 1. Ventes Totales (anciennement "Meilleures ventes" dans la vue)
        // Utilise le total_ttc (total toutes taxes comprises) des ventes pour le magasin actif
        $totalSales = Vente::where('magasin_id', $magasinId)->sum('total_ttc');
        $totalSalesCount = Vente::where('magasin_id', $magasinId)->count();

        // 2. Valeur Totale du Stock
        // Calcule la somme (quantité * coût d'achat) de tous les produits en stock pour le magasin actif
        $totalStockValue = Produit::where('magasin_id', $magasinId)
                                  ->select(DB::raw('SUM(quantite * cout_achat) as total_value'))
                                  ->first()->total_value ?? 0;
        $totalStockQuantity = Produit::where('magasin_id', $magasinId)->sum('quantite');

        // 3. Pertes (exemple, vous devrez définir comment les "pertes" sont calculées dans votre système)
        // Ceci est un placeholder. Vous devrez implémenter la logique réelle de calcul des pertes.
        // Par exemple, les pertes pourraient venir d'ajustements de stock de type 'sortie' avec un motif de perte,
        // ou de produits périmés, etc.

        // CORRECTION : Calcul des pertes à partir des lignes d'ajustement, car 'total_cout_ajustement' n'existe pas directement
        $totalLosses = Ajustement::where('magasin_id', $magasinId)
                                 ->where('type', 'sortie')
                                 ->where(function($query) {
                                     $query->where('motif_global', 'LIKE', '%perte%')
                                           ->orWhere('motif_global', 'LIKE', '%casse%');
                                 })
                                 ->with('lignesAjustement') // Charge la relation pour accéder aux détails des lignes
                                 ->get()
                                 ->sum(function($ajustement) {
                                     return $ajustement->lignesAjustement->sum(function($ligne) {
                                         return $ligne->quantite_ajustee * ($ligne->prix_unitaire_ajuste ?? 0);
                                     });
                                 });


        // NOUVEAU : Calcul du nombre de produits avec une marge faible
        // Définissez votre seuil de marge faible ici (par exemple, 10%)
        $lowMarginThreshold = 5; // En pourcentage
        $lowMarginProductsCount = Produit::where('magasin_id', $magasinId)
                                         ->where('marge', '<', $lowMarginThreshold)
                                         ->count();


        // 4. Alertes Stock (Produits en stock bas ou en rupture)
        $lowStockProducts = Produit::where('magasin_id', $magasinId)
                                    ->whereColumn('quantite', '<=', 'seuil_alerte')
                                    ->where('quantite', '>', 0)
                                    ->count();
        $outOfStockProducts = Produit::where('magasin_id', $magasinId)
                                      ->where('quantite', '<=', 0)
                                      ->count();
        $totalAlerts = $lowStockProducts + $outOfStockProducts;

        // --- Calcul des statistiques pour les cartes du bas ---

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // 5. Chiffre d'affaires du mois
        $monthlyRevenue = Vente::where('magasin_id', $magasinId)
                               ->whereBetween('date_vente', [$startOfMonth, $endOfMonth])
                               ->sum('total_ttc');

        // 6. Bénéfices du mois (simplifié : Chiffre d'affaires - Coût des marchandises vendues)
        // Nécessite de calculer le coût des produits vendus ce mois-ci
        $monthlyCostOfGoodsSold = DB::table('ligne_ventes')
            ->join('ventes', 'ligne_ventes.vente_id', '=', 'ventes.id')
            ->join('produits', 'ligne_ventes.produit_id', '=', 'produits.id')
            ->where('ventes.magasin_id', $magasinId)
            ->whereBetween('ventes.date_vente', [$startOfMonth, $endOfMonth])
            ->sum(DB::raw('ligne_ventes.quantite * produits.cout_achat'));

        $monthlyProfit = $monthlyRevenue - $monthlyCostOfGoodsSold;

        // 7. Dettes (Reste à payer sur les ventes)
        $totalDebts = Vente::where('magasin_id', $magasinId)
                            ->where('reste_a_payer', '>', 0)
                            ->sum('reste_a_payer');

        // 8. Commandes en cours (si vous avez un système de commandes séparé des ventes)
        // Adaptez le statut 'en_cours' selon vos conventions.
        $pendingOrdersCount = 0;
        if (class_exists(Commande::class)) { // Vérifie si le modèle Commande existe
            $pendingOrdersCount = Commande::where('magasin_id', $magasinId)
                                          ->where('statut', 'en_attente') // Exemple de statut
                                          ->count();
        }



        // NOUVEAU : Top 5 des produits les plus vendus du mois
        $topSoldProducts = DB::table('ligne_ventes')
            ->join('ventes', 'ligne_ventes.vente_id', '=', 'ventes.id')
            ->join('produits', 'ligne_ventes.produit_id', '=', 'produits.id')
            ->select(
                'produits.nom as product_name',
                'produits.description as product_description', // Ajouté la description
                DB::raw('SUM(ligne_ventes.quantite) as total_quantity_sold'),
                DB::raw('SUM(ligne_ventes.prix_total) as total_revenue_from_product')
            )
            ->where('ventes.magasin_id', $magasinId)
            ->whereBetween('ventes.date_vente', [$startOfMonth, $endOfMonth])
            ->groupBy('produits.id', 'produits.nom', 'produits.description') // Ajouté la description au groupement
            ->orderByDesc('total_quantity_sold')
            ->limit(5)
            ->get();





        // --- Récupération des activités récentes pour les tableaux ---

        // Ventes Récentes (5 dernières)
        $recentSales = Vente::with('client')
                            ->where('magasin_id', $magasinId)
                            ->orderBy('date_vente', 'desc')
                            ->limit(5)
                            ->get();

        // Retours Récents (5 derniers)
        $recentReturns = RetourClient::with('client')
                                    ->where('magasin_id', $magasinId)
                                    ->orderBy('date_retour', 'desc')
                                    ->limit(5)
                                    ->get();

        // Ajustements Récents (5 derniers)
        $recentAjustements = Ajustement::with('user')
                                    ->where('magasin_id', $magasinId)
                                    ->orderBy('date_ajustement', 'desc')
                                    ->limit(5)
                                    ->get();

        // Passe toutes les données à la vue
        return view('pages.admin.home', compact(
            'totalSales',
            'totalSalesCount',
            'totalStockValue',
            'totalStockQuantity',
            'totalLosses',
            'lowStockProducts', // Pass these two for the alert card
            'outOfStockProducts', // Pass these two for the alert card
            'totalAlerts',
            'monthlyRevenue',
            'monthlyProfit',
            'totalDebts',
            'pendingOrdersCount',
            'recentSales',
            'recentReturns',
            'recentAjustements',
            'lowMarginProductsCount',
            'topSoldProducts'
        ));
    }


    public function managerHome(Request $request)
    {
        // Get the active store ID from the session, which should be set
        // by the store switcher or based on the manager's default store.
        $magasinId = session('magasin_actif_id');

        // Ensure a magasin ID is available, otherwise, handle the error or redirect.
        if (!$magasinId) {
            // If the manager hasn't selected a store or no store is active,
            // redirect or return an appropriate view/message.
            // For this example, we'll return an empty view with a message or handle it gracefully.
            return view('pages.manager.home', [
                'topSoldProducts' => collect(),
                'recentSales' => collect(),
                'totalDebts' => 0, // Initialize to 0
                'recentReturns' => collect(), // Initialize to empty collection
                'magasin_error' => true
            ]);
        }

        // Define the date range for the current month
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // 1. Top 5 Best Sellers (Top 5 Meilleurs ventes du mois)
        $topSoldProducts = DB::table('ligne_ventes')
            ->join('ventes', 'ligne_ventes.vente_id', '=', 'ventes.id')
            ->join('produits', 'ligne_ventes.produit_id', '=', 'produits.id')
            ->select(
                'produits.nom as product_name',
                'produits.description as product_description',
                DB::raw('SUM(ligne_ventes.quantite) as total_quantity_sold'),
                DB::raw('SUM(ligne_ventes.prix_total) as total_revenue_from_product')
            )
            ->where('ventes.magasin_id', $magasinId)
            ->whereBetween('ventes.date_vente', [$startOfMonth, $endOfMonth])
            ->groupBy('produits.id', 'produits.nom', 'produits.description')
            ->orderByDesc('total_quantity_sold')
            ->limit(5)
            ->get();

        // 2. Recent Sales (Ventes Récentes)
        $recentSales = Vente::with('client')
            ->where('magasin_id', $magasinId)
            ->orderBy('date_vente', 'desc')
            ->limit(5)
            ->get();

        // 3. Debts (Dettes - Reste à payer sur les ventes)
        $totalDebts = Vente::where('magasin_id', $magasinId)
                            ->where('reste_a_payer', '>', 0)
                            ->sum('reste_a_payer');

        // 4. Recent Returns (Retours Récents - 5 derniers)
        $recentReturns = RetourClient::with('client')
                                    ->where('magasin_id', $magasinId)
                                    ->orderBy('date_retour', 'desc')
                                    ->limit(5)
                                    ->get();

        // Pass data to the view
        return view('pages.manager.home', compact(
            'topSoldProducts',
            'recentSales',
            'totalDebts', // Pass totalDebts to the view
            'recentReturns' // Pass recentReturns to the view
        ));
    
    }

    public function supervisorHome(){
        
        // Récupère l'ID du magasin actif depuis la session
        $magasinId = session('magasin_actif_id');

        // --- Calcul des statistiques pour les cartes du haut ---

        // 1. Ventes Totales (anciennement "Meilleures ventes" dans la vue)
        // Utilise le total_ttc (total toutes taxes comprises) des ventes pour le magasin actif
        $totalSales = Vente::where('magasin_id', $magasinId)->sum('total_ttc');
        $totalSalesCount = Vente::where('magasin_id', $magasinId)->count();

        // 2. Valeur Totale du Stock
        // Calcule la somme (quantité * coût d'achat) de tous les produits en stock pour le magasin actif
        $totalStockValue = Produit::where('magasin_id', $magasinId)
                                  ->select(DB::raw('SUM(quantite * cout_achat) as total_value'))
                                  ->first()->total_value ?? 0;
        $totalStockQuantity = Produit::where('magasin_id', $magasinId)->sum('quantite');

        // 3. Pertes (exemple, vous devrez définir comment les "pertes" sont calculées dans votre système)
        // Ceci est un placeholder. Vous devrez implémenter la logique réelle de calcul des pertes.
        // Par exemple, les pertes pourraient venir d'ajustements de stock de type 'sortie' avec un motif de perte,
        // ou de produits périmés, etc.

        // CORRECTION : Calcul des pertes à partir des lignes d'ajustement, car 'total_cout_ajustement' n'existe pas directement
        $totalLosses = Ajustement::where('magasin_id', $magasinId)
                                 ->where('type', 'sortie')
                                 ->where(function($query) {
                                     $query->where('motif_global', 'LIKE', '%perte%')
                                           ->orWhere('motif_global', 'LIKE', '%casse%');
                                 })
                                 ->with('lignesAjustement') // Charge la relation pour accéder aux détails des lignes
                                 ->get()
                                 ->sum(function($ajustement) {
                                     return $ajustement->lignesAjustement->sum(function($ligne) {
                                         return $ligne->quantite_ajustee * ($ligne->prix_unitaire_ajuste ?? 0);
                                     });
                                 });


        // NOUVEAU : Calcul du nombre de produits avec une marge faible
        // Définissez votre seuil de marge faible ici (par exemple, 10%)
        $lowMarginThreshold = 5; // En pourcentage
        $lowMarginProductsCount = Produit::where('magasin_id', $magasinId)
                                         ->where('marge', '<', $lowMarginThreshold)
                                         ->count();


        // 4. Alertes Stock (Produits en stock bas ou en rupture)
        $lowStockProducts = Produit::where('magasin_id', $magasinId)
                                    ->whereColumn('quantite', '<=', 'seuil_alerte')
                                    ->where('quantite', '>', 0)
                                    ->count();
        $outOfStockProducts = Produit::where('magasin_id', $magasinId)
                                      ->where('quantite', '<=', 0)
                                      ->count();
        $totalAlerts = $lowStockProducts + $outOfStockProducts;

        // --- Calcul des statistiques pour les cartes du bas ---

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // 5. Chiffre d'affaires du mois
        $monthlyRevenue = Vente::where('magasin_id', $magasinId)
                               ->whereBetween('date_vente', [$startOfMonth, $endOfMonth])
                               ->sum('total_ttc');

        // 6. Bénéfices du mois (simplifié : Chiffre d'affaires - Coût des marchandises vendues)
        // Nécessite de calculer le coût des produits vendus ce mois-ci
        $monthlyCostOfGoodsSold = DB::table('ligne_ventes')
            ->join('ventes', 'ligne_ventes.vente_id', '=', 'ventes.id')
            ->join('produits', 'ligne_ventes.produit_id', '=', 'produits.id')
            ->where('ventes.magasin_id', $magasinId)
            ->whereBetween('ventes.date_vente', [$startOfMonth, $endOfMonth])
            ->sum(DB::raw('ligne_ventes.quantite * produits.cout_achat'));

        $monthlyProfit = $monthlyRevenue - $monthlyCostOfGoodsSold;

        // 7. Dettes (Reste à payer sur les ventes)
        $totalDebts = Vente::where('magasin_id', $magasinId)
                            ->where('reste_a_payer', '>', 0)
                            ->sum('reste_a_payer');

        // 8. Commandes en cours (si vous avez un système de commandes séparé des ventes)
        // Adaptez le statut 'en_cours' selon vos conventions.
        $pendingOrdersCount = 0;
        if (class_exists(Commande::class)) { // Vérifie si le modèle Commande existe
            $pendingOrdersCount = Commande::where('magasin_id', $magasinId)
                                          ->where('statut', 'en_attente') // Exemple de statut
                                          ->count();
        }



        // NOUVEAU : Top 5 des produits les plus vendus du mois
        $topSoldProducts = DB::table('ligne_ventes')
            ->join('ventes', 'ligne_ventes.vente_id', '=', 'ventes.id')
            ->join('produits', 'ligne_ventes.produit_id', '=', 'produits.id')
            ->select(
                'produits.nom as product_name',
                'produits.description as product_description', // Ajouté la description
                DB::raw('SUM(ligne_ventes.quantite) as total_quantity_sold'),
                DB::raw('SUM(ligne_ventes.prix_total) as total_revenue_from_product')
            )
            ->where('ventes.magasin_id', $magasinId)
            ->whereBetween('ventes.date_vente', [$startOfMonth, $endOfMonth])
            ->groupBy('produits.id', 'produits.nom', 'produits.description') // Ajouté la description au groupement
            ->orderByDesc('total_quantity_sold')
            ->limit(5)
            ->get();





        // --- Récupération des activités récentes pour les tableaux ---

        // Ventes Récentes (5 dernières)
        $recentSales = Vente::with('client')
                            ->where('magasin_id', $magasinId)
                            ->orderBy('date_vente', 'desc')
                            ->limit(5)
                            ->get();

        // Retours Récents (5 derniers)
        $recentReturns = RetourClient::with('client')
                                    ->where('magasin_id', $magasinId)
                                    ->orderBy('date_retour', 'desc')
                                    ->limit(5)
                                    ->get();

        // Ajustements Récents (5 derniers)
        $recentAjustements = Ajustement::with('user')
                                    ->where('magasin_id', $magasinId)
                                    ->orderBy('date_ajustement', 'desc')
                                    ->limit(5)
                                    ->get();

        // Passe toutes les données à la vue
        return view('pages.supervisor.home', compact(
            'totalSales',
            'totalSalesCount',
            'totalStockValue',
            'totalStockQuantity',
            'totalLosses',
            'lowStockProducts', // Pass these two for the alert card
            'outOfStockProducts', // Pass these two for the alert card
            'totalAlerts',
            'monthlyRevenue',
            'monthlyProfit',
            'totalDebts',
            'pendingOrdersCount',
            'recentSales',
            'recentReturns',
            'recentAjustements',
            'lowMarginProductsCount',
            'topSoldProducts'
        ));
    }

    public function userHome(){
        return view('pages/user/home');
    }
}
