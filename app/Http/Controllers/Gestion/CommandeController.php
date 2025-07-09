<?php

namespace App\Http\Controllers\Gestion;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Fournisseur;
use App\Models\LigneCommande;
use App\Models\Produit;
use Illuminate\Http\Request;
use App\Models\StockLot;
use Carbon\Carbon;
use App\Models\Categorie;
use App\Models\MouvementStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class CommandeController extends Controller
{
    public function index()
    {
        $magasinId = session('magasin_actif_id');

        $commandes = Commande::where('magasin_id', $magasinId)
            ->with('fournisseur')
            ->orderByDesc('date_commande')
            ->paginate(15);

        return view('commandes.index', compact('commandes'));
    }


    public function create()
    {
        $magasinId = session('magasin_actif_id');

        $fournisseurs = Fournisseur::all(); // tu peux filtrer selon besoin
        $produits = Produit::where('magasin_id', $magasinId)->get();
        $categories = Categorie::where('magasin_id', $magasinId)->get(); 

        return view('commandes.create', compact('fournisseurs', 'produits', 'categories'));
    }

    public function store(Request $request)
    {
        $magasinId = session('magasin_actif_id');

        $validated = $request->validate([
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'date_commande' => 'required|date',
            'date_prevue_livraison' => 'required|date|after_or_equal:date_commande',
            'lignes' => 'required|array|min:1',
            'lignes.*.produit_id' => 'required|exists:produits,id',
            'lignes.*.quantite' => 'required|integer|min:1',
            'lignes.*.prix_unitaire' => 'required|numeric|min:0',
        ]);

        $commande = Commande::create([
            'fournisseur_id' => $validated['fournisseur_id'],
            'magasin_id' => $magasinId,
            'statut' => 'en_attente',
            'user_id' => Auth::id(), // ✅ Ajoute l'utilisateur connecté
            'date_commande' => $validated['date_commande'],
            'date_prevue_livraison' => $validated['date_prevue_livraison'],
        ]);

        foreach ($validated['lignes'] as $ligne) {
            LigneCommande::create([
                'commande_id' => $commande->id,
                'produit_id' => $ligne['produit_id'],
                'quantite' => $ligne['quantite'],
                'prix_unitaire' => $ligne['prix_unitaire'],
            ]);
        }

        return redirect()->route('commandes.index')->with('success', 'Commande créée avec succès.');
    }

    public function show($id)
    {
        $magasinId = session('magasin_actif_id');

        $commande = Commande::where('magasin_id', $magasinId)
            ->with(['fournisseur', 'lignesCommande.produit'])
            ->findOrFail($id);

        return view('commandes.show', compact('commande'));
    }

    public function edit($id)
    {
        $magasinId = session('magasin_actif_id');

        $commande = Commande::where('magasin_id', $magasinId)
            ->with('lignesCommande')
            ->findOrFail($id);

        if ($commande->statut === 'livré') {
            return redirect()->route('commandes.index')->with('error', 'Impossible de modifier une commande livrée.');
        }

        $fournisseurs = Fournisseur::all();
        $produits = Produit::where('magasin_id', $magasinId)->get();

        return view('commandes.edit', compact('commande', 'fournisseurs', 'produits'));
    }

    public function update(Request $request, $id)
    {
        $magasinId = session('magasin_actif_id');

        $commande = Commande::where('magasin_id', $magasinId)->findOrFail($id);

        if ($commande->statut === 'livree') {
            return redirect()->route('commandes.index')->with('error', 'Impossible de modifier une commande livrée.');
        }

        $validated = $request->validate([
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'date_commande' => 'required|date',
            'lignes' => 'required|array|min:1',
            'lignes.*.id' => 'nullable|exists:ligne_commandes,id',
            'lignes.*.produit_id' => 'required|exists:produits,id',
            'lignes.*.quantite' => 'required|integer|min:1',
            'lignes.*.prix_unitaire' => 'required|numeric|min:0',
        ]);

        $commande->update([
            'fournisseur_id' => $validated['fournisseur_id'],
            'date_commande' => $validated['date_commande'],
        ]);

        // Suppression des lignes existantes non soumises dans la MAJ
        $idsEnvoyes = collect($validated['lignes'])->pluck('id')->filter()->all();
        $commande->lignesCommande()->whereNotIn('id', $idsEnvoyes)->delete();

        // MAJ ou création des lignes
        foreach ($validated['lignes'] as $ligne) {
            if (!empty($ligne['id'])) {
                // MAJ ligne existante
                $ligneCommande = LigneCommande::find($ligne['id']);
                $ligneCommande->update([
                    'produit_id' => $ligne['produit_id'],
                    'quantite' => $ligne['quantite'],
                    'prix_unitaire' => $ligne['prix_unitaire'],
                ]);
            } else {
                // Nouvelle ligne
                LigneCommande::create([
                    'commande_id' => $commande->id,
                    'produit_id' => $ligne['produit_id'],
                    'quantite' => $ligne['quantite'],
                    'prix_unitaire' => $ligne['prix_unitaire'],
                ]);
            }
        }

        return redirect()->route('commandes.index')->with('success', 'Commande mise à jour.');
    }

    public function destroy($id)
    {
        $magasinId = session('magasin_actif_id');

        $commande = Commande::where('magasin_id', $magasinId)->findOrFail($id);

        if ($commande->statut === 'livree') {
            return redirect()->route('commandes.index')->with('error', 'Impossible de supprimer une commande livrée.');
        }

        $commande->delete();

        return redirect()->route('commandes.index')->with('success', 'Commande supprimée.');
    }

    public function reception(Request $request, Commande $commande)
    {
        $magasinId = session('magasin_actif_id');

        if ($commande->magasin_id != $magasinId) {
            abort(403, "Commande non autorisée.");
        }

        if ($commande->statut === 'livree') {
            return back()->with('error', 'Commande déjà livrée.');
        }

        $validated = $request->validate([
            'cout_transport' => 'required|numeric|min:0',
            'frais_suppl' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($commande, $validated, $magasinId) {

                $dateReception = Carbon::now();

                // Calcul du statut de retard et du nombre de jours de retard
                $isLate = false;
                $daysLate = 0;

                if ($commande->date_prevue_livraison) {
                    // Comparer les dates au début de la journée pour un calcul de jours complets
                    $datePrevueStartOfDay = $commande->date_prevue_livraison->startOfDay();
                    $dateReceptionStartOfDay = $dateReception->startOfDay();

                    if ($dateReceptionStartOfDay->greaterThan($datePrevueStartOfDay)) {
                        $isLate = true;
                        $daysLate = $dateReceptionStartOfDay->diffInDays($datePrevueStartOfDay);
                    }
                }

                $commande->update([
                    'cout_transport' => $validated['cout_transport'],
                    'frais_suppl' => $validated['frais_suppl'],
                    'statut' => 'livree',
                    'date_reception' => now(),
                    'is_late' => $isLate,              // Enregistre si la commande est en retard
                    'days_late' => $daysLate,
                ]);

                // Calcul coût total
                $totalLignes = $commande->lignesCommande->sum(function($ligne) {
                    return $ligne->quantite * $ligne->prix_unitaire;
                });

                if ($totalLignes == 0) {
                    // Gérer le cas d'erreur ou retourner une réponse adéquate
                    throw new \Exception('Le total des produits est nul, impossible de calculer la répartition des frais.');
                }

                $commande->update([
                    'cout_total' => $totalLignes + $validated['cout_transport'] + $validated['frais_suppl']
                ]);

                foreach ($commande->lignesCommande as $ligne) {
                    try {
                        \Log::info("Traitement produit ID {$ligne->produit_id}");
                        // Calcul part des frais pour ce produit
                        $poids = ($ligne->quantite * $ligne->prix_unitaire) / $totalLignes;
                        $fraisTransportPart = $validated['cout_transport'] * $poids;
                        $fraisSupplPart = $validated['frais_suppl'] * $poids;

                        $coutReelUnitaire = $ligne->prix_unitaire + ($fraisTransportPart + $fraisSupplPart) / $ligne->quantite;

                        // Créer un nouveau lot FIFO
                        $lot = StockLot::create([
                            'produit_id' => $ligne->produit_id,
                            'magasin_id' => $magasinId,
                            'quantite' => $ligne->quantite,
                            'quantite_restante' => $ligne->quantite,
                            'cout_achat' => round($coutReelUnitaire, 2),
                            'date_reception' => now(),
                        ]);

                        // MISE À JOUR DE LA QUANTITÉ TOTALE DU PRODUIT
                        $produit = $ligne->produit; // Récupère le modèle Produit lié à la ligne
                        $produit->updateQuantiteFromLots();

                        // MOUVEMENT STOCK (entrée)
                        MouvementStock::create([
                            'produit_id' => $ligne->produit_id,
                            'type' => 'entree',
                            'quantite' => $ligne->quantite,
                            'lot_id' => $lot->id,
                            'source_type' => 'commande',
                            'source_id' => $commande->id,
                            'magasin_id' => $magasinId,
                            'user_id' => auth()->id(),
                            'motif' => 'Réception commande fournisseur - Lot FIFO',
                            'date' => now(),
                        ]);

                        // MISE À JOUR PRIX PAR DÉFAUT DU PRODUIT ET RECALCUL DE LA MARGE
                        $produit = $ligne->produit;
                        $ancienPrixVente = $produit->prix_vente; // Récupère le prix de vente actuel (fixe)

                        $nouvelleMarge = 0;
                        if ($lot->cout_achat > 0) {
                            $nouvelleMarge = round((($ancienPrixVente - $lot->cout_achat) / $lot->cout_achat) * 100, 2);
                        }

                        // MISE À JOUR PRIX PAR DÉFAUT DU PRODUIT
                        $produit = $ligne->produit;
                        $produit->update([
                            'cout_achat' => $lot->cout_achat,
                            'marge' => $nouvelleMarge, // Met à jour la marge
                            /*'prix_vente' => round($lot->cout_achat * (1 + ($produit->marge / 100)), 2),*/
                        ]);
                        \Log::info("Produit ID {$ligne->produit_id} traité avec succès.");
                    } catch (\Exception $e) {
                        \Log::error("Erreur produit ID {$ligne->produit_id} : " . $e->getMessage());
                        throw $e; // pour que la transaction échoue comme prévu
                    }
                }
            });
        } catch (\Exception $e) {
            \Log::error('Erreur réception commande : ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la réception : ' . $e->getMessage());
        }

        return redirect()->route('commandes.index')->with('success', 'Commande réceptionnée avec succès.');
    }
}