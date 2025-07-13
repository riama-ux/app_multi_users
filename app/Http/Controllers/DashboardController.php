<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vente;
use App\Models\Produit;
use App\Models\RetourClient;
use App\Models\Ajustement;
use App\Models\Commande; // Assurez-vous d'avoir un modèle Commande si vous l'utilisez
use App\Models\Paiement; // Pour calculer les dettes
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord avec les statistiques clés et les activités récentes.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Récupère l'ID du magasin actif depuis la session
        $magasinId = session('magasin_actif_id');

        // --- Calcul des statistiques pour les cartes du haut ---

        // 1. Meilleures ventes (Total des ventes)
        // Utilise le total_ttc (total toutes taxes comprises) des ventes
        $totalSales = Vente::where('magasin_id', $magasinId)->sum('total_ttc');
        $totalSalesCount = Vente::where('magasin_id', $magasinId)->count();

        // 2. Valeur Totale du Stock
        // Calcule la somme (quantité * coût d'achat) de tous les produits en stock
        $totalStockValue = Produit::where('magasin_id', $magasinId)
                                  ->select(DB::raw('SUM(quantite * cout_achat) as total_value'))
                                  ->first()->total_value ?? 0;
        $totalStockQuantity = Produit::where('magasin_id', $magasinId)->sum('quantite');

        // 3. Alertes Stock (Produits en stock bas ou en rupture)
        $lowStockProducts = Produit::where('magasin_id', $magasinId)
                                    ->whereColumn('quantite', '<=', 'seuil_alerte')
                                    ->where('quantite', '>', 0)
                                    ->count();
        $outOfStockProducts = Produit::where('magasin_id', $magasinId)
                                      ->where('quantite', '<=', 0)
                                      ->count();
        $totalAlerts = $lowStockProducts + $outOfStockProducts;

        // --- Calcul des statistiques pour les cartes du bas ---

        // 4. Chiffre d'affaires du mois
        $monthlyRevenue = Vente::where('magasin_id', $magasinId)
                               ->whereMonth('date_vente', now()->month)
                               ->whereYear('date_vente', now()->year)
                               ->sum('total_ttc');

        // 5. Bénéfices du mois (simplifié : Chiffre d'affaires - Coût des marchandises vendues)
        // Nécessite de calculer le coût des produits vendus ce mois-ci
        $monthlyCostOfGoodsSold = DB::table('ligne_ventes')
            ->join('ventes', 'ligne_ventes.vente_id', '=', 'ventes.id')
            ->join('produits', 'ligne_ventes.produit_id', '=', 'produits.id')
            ->where('ventes.magasin_id', $magasinId)
            ->whereMonth('ventes.date_vente', now()->month)
            ->whereYear('ventes.date_vente', now()->year)
            ->sum(DB::raw('ligne_ventes.quantite * produits.cout_achat'));

        $monthlyProfit = $monthlyRevenue - $monthlyCostOfGoodsSold;

        // 6. Dettes (Reste à payer sur les ventes)
        $totalDebts = Vente::where('magasin_id', $magasinId)
                            ->where('reste_a_payer', '>', 0)
                            ->sum('reste_a_payer');

        // 7. Commandes en cours (si vous avez un système de commandes séparé des ventes)
        // Adaptez le statut 'en_cours' selon vos conventions
        $pendingOrdersCount = 0;
        if (class_exists(Commande::class)) { // Vérifie si le modèle Commande existe
            $pendingOrdersCount = Commande::where('magasin_id', $magasinId)
                                          ->where('statut', 'en_cours') // Exemple de statut
                                          ->count();
        }


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
        return view('dashboard.index', compact(
            'totalSales',
            'totalSalesCount',
            'totalStockValue',
            'totalStockQuantity',
            'lowStockProducts',
            'outOfStockProducts',
            'totalAlerts',
            'monthlyRevenue',
            'monthlyProfit',
            'totalDebts',
            'pendingOrdersCount',
            'recentSales',
            'recentReturns',
            'recentAjustements'
        ));
    }
}
