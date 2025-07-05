<?php

namespace App\Http\Controllers\Gestion;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use App\Models\Client;
use App\Models\Vente;
use App\Models\LigneVente;
use App\Models\Paiement;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VenteController extends Controller
{
    // Liste des ventes du magasin actif
    public function index()
    {
        $magasinId = session('magasin_actif_id');

        $ventes = Vente::with('client')
            ->where('magasin_id', $magasinId)
            ->orderBy('date_vente', 'desc')
            ->get();

        return view('ventes.index', compact('ventes'));
    }

    // Formulaire création
    public function create()
    {
        $magasinId = session('magasin_actif_id');

        $produits = Produit::where('magasin_id', $magasinId)->get();
        $clients = Client::where('magasin_id', $magasinId)->get();

        return view('ventes.create', compact('produits', 'clients'));
    }

    // Enregistrer nouvelle vente
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'produits' => 'required|array|min:1',
            'mode_paiement' => 'required|string',
            'montant_paye' => 'required|numeric|min:0',
            'remise' => 'nullable|numeric|min:0',
        ]);

        $magasinId = session('magasin_actif_id');

        DB::beginTransaction();
        try {
            // Création vente (statut temporaire)
            $vente = Vente::create([
                'client_id' => $request->client_id,
                'magasin_id' => $magasinId,
                'user_id' => Auth::id(),
                'remise' => $request->remise ?? 0,
                //'total_ht' => 0, // sera calculé
                'total_ttc' => 0,
                'montant_paye' => $request->montant_paye,
                'reste_a_payer' => 0,
                'mode_paiement' => $request->mode_paiement,
                'date_vente' => now(),
                'statut' => 'payee',
            ]);

            $totalLignes = 0;

            // Stock FIFO et lignes vente
            foreach ($request->produits as $ligne) {
                $produitId = $ligne['produit_id'];
                $quantite = $ligne['quantite'];
                $prixUnitaire = $ligne['prix_unitaire'];

                // Sortie FIFO
                $lotsUtilises = StockService::sortirFifo(
                    $produitId,
                    $quantite,
                    'vente',
                    $vente->id,
                    $magasinId,
                    Auth::id(),
                    'Sortie vente'
                );

                foreach ($lotsUtilises as $lotId => $qte) {
                    $prixTotalLigne = $qte * $prixUnitaire;
                    LigneVente::create([
                        'vente_id' => $vente->id,
                        'produit_id' => $produitId,
                        'quantite' => $qte,
                        'prix_unitaire' => $prixUnitaire,
                        'prix_total' => $qte * $prixUnitaire,
                        'lot_id' => $lotId,
                    ]);
                    $totalLignes += $prixTotalLigne;
                }
            }

            $totalTTC = max($totalLignes - ($request->remise ?? 0), 0);
            $reste = max($totalTTC - $request->montant_paye, 0);

            // Mise à jour de la vente
            $vente->update([
                //'total_ht' => $totalHT,
                'total_ttc' => $totalTTC,
                'reste_a_payer' => $reste,
                'statut' => $reste <= 0 ? 'payee' : ($request->montant_paye > 0 ? 'partielle' : 'credit'),
            ]);

            // Paiement initial si montant payé > 0
            if ($request->montant_paye > 0) {
                Paiement::create([
                    'vente_id' => $vente->id,
                    'montant' => $request->montant_paye,
                    'mode_paiement' => $request->mode_paiement,
                    'user_id' => Auth::id(),
                    'date_paiement' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('ventes.index')->with('success', 'Vente enregistrée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur : ' . $e->getMessage()]);
        }
    }

    // Afficher détail vente
    public function show($id)
    {
        $magasinId = session('magasin_actif_id');

        $vente = Vente::with(['client', 'ligneVentes.produit', 'paiements.user'])
            ->where('magasin_id', $magasinId)
            ->findOrFail($id);

        return view('ventes.show', compact('vente'));
    }

    // Formulaire modification vente
    public function edit($id)
    {
        $magasinId = session('magasin_actif_id');

        $vente = Vente::with('ligneVentes')->where('magasin_id', $magasinId)->findOrFail($id);
        $produits = Produit::where('magasin_id', $magasinId)->get();
        $clients = Client::where('magasin_id', $magasinId)->get();

        return view('ventes.edit', compact('vente', 'produits', 'clients'));
    }

    // Mettre à jour la vente
    public function update(Request $request, $id)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'produits' => 'required|array|min:1',
            'mode_paiement' => 'required|string',
            'montant_paye' => 'required|numeric|min:0',
            'remise' => 'nullable|numeric|min:0',
        ]);

        $magasinId = session('magasin_actif_id');

        DB::beginTransaction();
        try {
            $vente = Vente::with('ligneVentes')->where('magasin_id', $magasinId)->findOrFail($id);

            $modifProduits = false;
            if ($request->has('produits')) {
                $modifProduits = true;
            }

            // Si les produits ont été modifiés, on annule les anciennes lignes
            if ($modifProduits) {
                // RESTAURATION STOCK des anciens lots (ajouter les quantités précédentes)
                foreach ($vente->ligneVentes as $ligne) {
                    StockService::reintegrerStockLot(
                        $ligne->lot_id,
                        $ligne->quantite,
                        $magasinId,
                        Auth::id(),
                        "Annulation vente #{$vente->id}"
                    );
                }

                // Supprimer anciennes lignes
                $vente->ligneVentes()->delete();

                $totalLignes = 0;

                // Sortie FIFO avec nouvelles lignes
                foreach ($request->produits as $ligne) {
                    $produitId = $ligne['produit_id'];
                    $quantite = $ligne['quantite'];
                    $prixUnitaire = $ligne['prix_unitaire'];

                    $lotsUtilises = StockService::sortirFifo(
                        $produitId,
                        $quantite,
                        'vente',
                        $vente->id,
                        $magasinId,
                        Auth::id(),
                        'Mise à jour vente'
                    );

                    foreach ($lotsUtilises as $lotId => $qte) {
                        $prixTotalLigne = $qte * $prixUnitaire;
                        LigneVente::create([
                            'vente_id' => $vente->id,
                            'produit_id' => $produitId,
                            'quantite' => $qte,
                            'prix_unitaire' => $prixUnitaire,
                            'prix_total' => $qte * $prixUnitaire,
                            'lot_id' => $lotId,
                        ]);
                        $totalLignes += $prixTotalLigne;
                    }
                }
            }else {
                // Si pas de modification produit, garder l'ancien total TTC
                $totalLignes = $vente->total_ttc + $vente->remise;
            }

            $remise = $request->remise ?? 0;
            $totalTTC = $totalLignes - $remise;
            $reste = $totalTTC - $request->montant_paye;

            // Mise à jour des infos vente
            $vente->update([
                'client_id' => $request->client_id,
                'remise' => $remise,
                'mode_paiement' => $request->mode_paiement,
                'montant_paye' => $request->montant_paye,
                'total_ttc' => $totalTTC,
                'reste_a_payer' => $reste,
                'statut' => $reste <= 0 ? 'payee' : ($request->montant_paye > 0 ? 'partielle' : 'credit'),
            ]);

            /*$vente->update([
                //'total_ht' => $totalHT,
                'total_ttc' => $totalTTC,
                'reste_a_payer' => $reste,
                'statut' => $reste <= 0 ? 'payee' : ($request->montant_paye > 0 ? 'partielle' : 'credit'),
            ]);*/

            // TODO: gérer mise à jour des paiements (ici on peut complexifier selon besoin)

            DB::commit();

            return redirect()->route('ventes.index')->with('success', 'Vente modifiée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur : ' . $e->getMessage()]);
        }
    }

    // Suppression (soft delete)
    public function destroy($id)
    {
        abort(403, 'La suppression directe d\'une vente est interdite. Utilisez un ajustement de type retour client.');
    }
}
