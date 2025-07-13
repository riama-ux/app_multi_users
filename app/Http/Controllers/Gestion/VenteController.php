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
    public function index(Request $request)
    {
        $magasinId = session('magasin_actif_id');

        $query = Vente::with('client')
            ->where('magasin_id', $magasinId);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $ventes = $query->orderBy('date_vente', 'asc')->paginate(15);

        return view('ventes.index', compact('ventes'));
    }

    // Formulaire création
    public function create()
    {
        $magasinId = session('magasin_actif_id');

        // Nous n'avons plus besoin de passer tous les produits ici, Livewire s'en chargera
        // $produits = Produit::where('magasin_id', $magasinId)->get();
        $clients = Client::where('magasin_id', $magasinId)->get();

        // Assurez-vous que les catégories sont passées si le modal de création de produit en a besoin
        // (Comme dans le module de commande)
        $categories = \App\Models\Categorie::where('magasin_id', $magasinId)->get();


        return view('ventes.create', compact('clients', 'categories'));
    }

    // Enregistrer nouvelle vente
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'produits' => 'required|array|min:1',
            'produits.*.produit_id' => 'required|exists:produits,id',
            'produits.*.quantite' => 'required|numeric|min:1',
            'produits.*.prix_unitaire' => 'required|numeric|min:0',
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
                'total_ttc' => 0, // Sera calculé après les lignes
                'montant_paye' => $request->montant_paye,
                'reste_a_payer' => 0, // Sera calculé
                'mode_paiement' => $request->mode_paiement,
                'date_vente' => now(),
                'statut' => 'payee', // Statut initial, sera ajusté
            ]);

            $totalLignes = 0;

            // Stock FIFO et lignes vente
            foreach ($request->produits as $ligne) {
                $produitId = $ligne['produit_id'];
                $quantite = $ligne['quantite'];
                $prixUnitaire = $ligne['prix_unitaire'];

                // Vérifier la disponibilité du stock avant de tenter la sortie FIFO
                $produit = Produit::where('id', $produitId)->where('magasin_id', $magasinId)->first();

                // CHANGEMENT ICI : Séparation des messages d'erreur pour plus de clarté et robustesse
                if (!$produit) {
                    throw new \Exception("Produit avec l'ID {$produitId} introuvable ou non disponible dans ce magasin.");
                }

                if ($produit->quantite < $quantite) {
                    throw new \Exception("Stock insuffisant pour le produit {$produit->nom}. Quantité disponible: {$produit->quantite}. Quantité demandée: {$quantite}.");
                }


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
                        'prix_total' => $prixTotalLigne,
                        'lot_id' => $lotId,
                    ]);
                    $totalLignes += $prixTotalLigne;
                }
            }

            $totalTTC = max($totalLignes - ($request->remise ?? 0), 0);
            $reste = max($totalTTC - $request->montant_paye, 0);

            // Mise à jour de la vente avec les totaux calculés
            $vente->update([
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
            return back()->withErrors(['error' => 'Erreur : ' . $e->getMessage()])->withInput();
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

        $vente = Vente::with('ligneVentes.produit')->where('magasin_id', $magasinId)->findOrFail($id);
        // Les produits ne sont plus passés directement, Livewire les gérera
        // $produits = Produit::where('magasin_id', $magasinId)->get();
        $clients = Client::where('magasin_id', $magasinId)->get();
        $categories = \App\Models\Categorie::where('magasin_id', $magasinId)->get(); // Pour le modal de création de produit

        return view('ventes.edit', compact('vente', 'clients', 'categories'));
    }

    // Mettre à jour la vente
    public function update(Request $request, $id)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'produits' => 'required|array|min:1',
            'produits.*.produit_id' => 'required|exists:produits,id',
            'produits.*.quantite' => 'required|numeric|min:1',
            'produits.*.prix_unitaire' => 'required|numeric|min:0',
            'mode_paiement' => 'required|string',
            'montant_paye' => 'required|numeric|min:0',
            'remise' => 'nullable|numeric|min:0',
        ]);

        $magasinId = session('magasin_actif_id');

        DB::beginTransaction();
        try {
            $vente = Vente::with('ligneVentes')->where('magasin_id', $magasinId)->findOrFail($id);

            // RESTAURATION STOCK des anciens lots (ajouter les quantités précédentes)
            foreach ($vente->ligneVentes as $ligne) {
                StockService::reintegrerStockLot(
                    $ligne->lot_id,
                    $ligne->quantite,
                    $magasinId,
                    Auth::id(),
                    "Annulation vente #{$vente->id} (modification)"
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

                // CHANGEMENT ICI : Séparation des messages d'erreur pour plus de clarté et robustesse
                $produit = Produit::where('id', $produitId)->where('magasin_id', $magasinId)->first();
                if (!$produit) {
                    throw new \Exception("Produit avec l'ID {$produitId} introuvable ou non disponible dans ce magasin.");
                }

                if ($produit->quantite < $quantite) {
                    throw new \Exception("Stock insuffisant pour le produit {$produit->nom}. Quantité disponible: {$produit->quantite}. Quantité demandée: {$quantite}.");
                }

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
                        'prix_total' => $prixTotalLigne,
                        'lot_id' => $lotId,
                    ]);
                    $totalLignes += $prixTotalLigne;
                }
            }

            $remise = $request->remise ?? 0;
            $totalTTC = max($totalLignes - $remise, 0);
            $reste = max($totalTTC - $request->montant_paye, 0);

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

            // TODO: gérer mise à jour des paiements (ici on peut complexifier selon besoin)
            // Pour l'instant, on suppose que le montant_paye est le total des paiements pour cette vente.
            // Si vous avez plusieurs paiements enregistrés, il faudra ajuster cette logique.
            // Par exemple, supprimer les anciens paiements liés à cette vente et en créer un nouveau
            // Ou mettre à jour le paiement existant si un seul est attendu.
            $vente->paiements()->delete(); // Supprime les anciens paiements
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

            return redirect()->route('ventes.index')->with('success', 'Vente modifiée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur : ' . $e->getMessage()])->withInput();
        }
    }

    // Suppression (soft delete)
    public function destroy($id)
    {
        abort(403, 'La suppression directe d\'une vente est interdite. Utilisez un ajustement de type retour client.');
    }
}
