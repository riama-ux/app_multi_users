<?php

namespace App\Http\Controllers\Gestion;

use App\Http\Controllers\Controller;
use App\Models\Ajustement;
use App\Models\LigneAjustement;
use App\Models\Produit;
use App\Models\StockLot; // Assurez-vous que le modèle StockLot est bien défini
use App\Services\StockService; // Votre service de gestion de stock
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AjustementController extends Controller
{
    /**
     * Affiche une liste des ajustements de stock.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $magasinId = session('magasin_actif_id');

        $query = Ajustement::with(['user'])
            ->where('magasin_id', $magasinId)
            ->orderBy('date_ajustement', 'desc');

        // Filtrage par type d'ajustement
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtrage par date
        if ($request->filled('date')) {
            $query->whereDate('date_ajustement', $request->date);
        }

        $ajustements = $query->paginate(15);

        return view('ajustements.index', compact('ajustements'));
    }

    /**
     * Affiche le formulaire de création d'un nouvel ajustement.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $magasinId = session('magasin_actif_id');
        // Les produits seront recherchés via Livewire, pas besoin de les passer tous ici
        return view('ajustements.create');
    }

    /**
     * Enregistre un nouvel ajustement de stock dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $magasinId = session('magasin_actif_id');

        try {
            $validated = $request->validate([
                'date_ajustement' => 'required|date_format:Y-m-d\TH:i',
                'type' => 'required|in:entree,sortie', // Type d'ajustement: entrée ou sortie
                'motif_global' => 'nullable|string|max:255',
                'lignes' => 'required|array|min:1',
                'lignes.*.produit_id' => 'required|exists:produits,id',
                'lignes.*.quantite_ajustee' => 'required|numeric|min:1',
                'lignes.*.prix_unitaire_ajuste' => 'nullable|numeric|min:1', // Prix unitaire pour l'ajustement (peut être 0)
                'lignes.*.motif_ligne' => 'nullable|string|max:255',
            ]);
        } catch (ValidationException $e) {
            // Si c'est une requête AJAX, renvoyer les erreurs en JSON
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            // Sinon, laisser Laravel gérer la redirection avec les erreurs pour les formulaires HTML standard
            throw $e;
        }

        DB::beginTransaction();
        try {
            $ajustement = Ajustement::create([
                'date_ajustement' => $validated['date_ajustement'],
                'type' => $validated['type'],
                'motif_global' => $validated['motif_global'],
                'magasin_id' => $magasinId,
                'user_id' => Auth::id(),
            ]);

            foreach ($validated['lignes'] as $ligneData) {
                $produit = Produit::where('id', $ligneData['produit_id'])
                                  ->where('magasin_id', $magasinId)
                                  ->first();

                if (!$produit) {
                    throw new \Exception("Produit avec l'ID {$ligneData['produit_id']} introuvable ou non disponible dans ce magasin.");
                }

                $quantite = $ligneData['quantite_ajustee'];
                $prixUnitaire = $ligneData['prix_unitaire_ajuste'] ?? 0;
                $description = "Ajustement de stock: " . ($ligneData['motif_ligne'] ?? $validated['motif_global']);

                if ($validated['type'] === 'entree') {
                    StockService::entrerStock(
                        $produit->id,
                        $quantite,
                        'ajustement',
                        $ajustement->id,
                        $magasinId,
                        Auth::id(),
                        $description,
                        $prixUnitaire // Utiliser le prix unitaire ajusté comme coût d'entrée
                    );
                } elseif ($validated['type'] === 'sortie') {
                    // Pour une sortie d'ajustement, on utilise la méthode FIFO
                    StockService::sortirFifo(
                        $produit->id,
                        $quantite,
                        'ajustement',
                        $ajustement->id,
                        $magasinId,
                        Auth::id(),
                        $description
                    );
                }

                LigneAjustement::create([
                    'ajustement_id' => $ajustement->id,
                    'produit_id' => $produit->id,
                    'quantite_ajustee' => $quantite,
                    'prix_unitaire_ajuste' => $prixUnitaire,
                    'motif_ligne' => $ligneData['motif_ligne'],
                ]);
            }

            DB::commit();
            return redirect()->route('ajustements.index')->with('success', 'Ajustement de stock enregistré avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Pour les erreurs inattendues, rediriger avec un message d'erreur
            return back()->withInput()->with('error', 'Erreur lors de l\'enregistrement de l\'ajustement: ' . $e->getMessage());
        }
    }

    /**
     * Affiche les détails d'un ajustement de stock spécifique.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $magasinId = session('magasin_actif_id');
        $ajustement = Ajustement::with(['lignesAjustement.produit', 'user'])
                                ->where('magasin_id', $magasinId)
                                ->findOrFail($id);

        return view('ajustements.show', compact('ajustement'));
    }

    /**
     * Affiche le formulaire d'édition d'un ajustement de stock.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $magasinId = session('magasin_actif_id');
        $ajustement = Ajustement::with('lignesAjustement.produit')
                                ->where('magasin_id', $magasinId)
                                ->findOrFail($id);
        // Les produits seront recherchés via Livewire, pas besoin de les passer tous ici
        return view('ajustements.edit', compact('ajustement'));
    }

    /**
     * Met à jour un ajustement de stock existant dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $magasinId = session('magasin_actif_id');
        $ajustement = Ajustement::with('lignesAjustement')->where('magasin_id', $magasinId)->findOrFail($id);

        $validated = $request->validate([
            'date_ajustement' => 'required|date_format:Y-m-d\TH:i',
            'type' => 'required|in:entree,sortie',
            'motif_global' => 'nullable|string|max:255',
            'lignes' => 'required|array|min:1',
            'lignes.*.id' => 'nullable|exists:ligne_ajustements,id', // Pour les lignes existantes
            'lignes.*.produit_id' => 'required|exists:produits,id',
            'lignes.*.quantite_ajustee' => 'required|numeric|min:0.01',
            'lignes.*.prix_unitaire_ajuste' => 'nullable|numeric|min:0',
            'lignes.*.motif_ligne' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Annuler les mouvements de stock des anciennes lignes d'ajustement
            foreach ($ajustement->lignesAjustement as $oldLigne) {
                $produit = Produit::find($oldLigne->produit_id);
                if (!$produit) {
                    throw new \Exception("Produit (ancien) avec l'ID {$oldLigne->produit_id} introuvable.");
                }

                if ($ajustement->type === 'entree') {
                    // Si l'ancien ajustement était une entrée, on fait une sortie pour l'annuler
                    StockService::sortirFifo(
                        $produit->id,
                        $oldLigne->quantite_ajustee,
                        'ajustement',
                        $ajustement->id,
                        $magasinId,
                        Auth::id(),
                        "Annulation ajustement entrée #{$ajustement->id} (modification)"
                    );
                } elseif ($ajustement->type === 'sortie') {
                    // Si l'ancien ajustement était une sortie, on fait une entrée pour l'annuler
                    StockService::entrerStock(
                        $produit->id,
                        $oldLigne->quantite_ajustee,
                        'ajustement',
                        $ajustement->id,
                        $magasinId,
                        Auth::id(),
                        "Annulation ajustement sortie #{$ajustement->id} (modification)",
                        $oldLigne->prix_unitaire_ajuste // Utiliser le prix d'ajustement d'origine
                    );
                }
            }

            // Supprimer toutes les anciennes lignes d'ajustement
            $ajustement->lignesAjustement()->delete();

            // Mettre à jour l'ajustement principal
            $ajustement->update([
                'date_ajustement' => $validated['date_ajustement'],
                'type' => $validated['type'],
                'motif_global' => $validated['motif_global'],
                'user_id' => Auth::id(), // Mettre à jour l'utilisateur qui a modifié
            ]);

            // Créer les nouvelles lignes d'ajustement et effectuer les mouvements de stock
            foreach ($validated['lignes'] as $ligneData) {
                $produit = Produit::where('id', $ligneData['produit_id'])
                                  ->where('magasin_id', $magasinId)
                                  ->first();

                if (!$produit) {
                    throw new \Exception("Produit avec l'ID {$ligneData['produit_id']} introuvable ou non disponible dans ce magasin.");
                }

                $quantite = $ligneData['quantite_ajustee'];
                $prixUnitaire = $ligneData['prix_unitaire_ajuste'] ?? 0;
                $description = "Ajustement de stock: " . ($ligneData['motif_ligne'] ?? $validated['motif_global']);

                if ($validated['type'] === 'entree') {
                    StockService::entrerStock(
                        $produit->id,
                        $quantite,
                        'ajustement',
                        $ajustement->id,
                        $magasinId,
                        Auth::id(),
                        $description,
                        $prixUnitaire
                    );
                } elseif ($validated['type'] === 'sortie') {
                    StockService::sortirFifo(
                        $produit->id,
                        $quantite,
                        'ajustement',
                        $ajustement->id,
                        $magasinId,
                        Auth::id(),
                        $description
                    );
                }

                LigneAjustement::create([
                    'ajustement_id' => $ajustement->id,
                    'produit_id' => $produit->id,
                    'quantite_ajustee' => $quantite,
                    'prix_unitaire_ajuste' => $prixUnitaire,
                    'motif_ligne' => $ligneData['motif_ligne'],
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'redirect' => route('ajustements.index')], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erreur lors de la mise à jour de l\'ajustement: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Supprime un ajustement de stock de la base de données.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $magasinId = session('magasin_actif_id');
        $ajustement = Ajustement::with('lignesAjustement')->where('magasin_id', $magasinId)->findOrFail($id);

        DB::beginTransaction();
        try {
            // Annuler les mouvements de stock avant de supprimer l'ajustement
            foreach ($ajustement->lignesAjustement as $ligne) {
                $produit = Produit::find($ligne->produit_id);
                if (!$produit) {
                    throw new \Exception("Produit (ancien) avec l'ID {$ligne->produit_id} introuvable.");
                }

                if ($ajustement->type === 'entree') {
                    // Si l'ajustement était une entrée, on fait une sortie pour l'annuler
                    StockService::sortirFifo(
                        $produit->id,
                        $ligne->quantite_ajustee,
                        'ajustement',
                        $ajustement->id,
                        $magasinId,
                        Auth::id(),
                        "Annulation ajustement entrée supprimé #{$ajustement->id}"
                    );
                } elseif ($ajustement->type === 'sortie') {
                    // Si l'ajustement était une sortie, on fait une entrée pour l'annuler
                    StockService::entrerStock(
                        $produit->id,
                        $ligne->quantite_ajustee,
                        'ajustement',
                        $ajustement->id,
                        $magasinId,
                        Auth::id(),
                        "Annulation ajustement sortie supprimé #{$ajustement->id}",
                        $ligne->prix_unitaire_ajuste // Utiliser le prix d'ajustement d'origine
                    );
                }
            }

            $ajustement->delete(); // Supprime l'ajustement et ses lignes grâce à onDelete('cascade')

            DB::commit();
            return redirect()->route('ajustements.index')->with('success', 'Ajustement de stock supprimé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la suppression de l\'ajustement: ' . $e->getMessage()]);
        }
    }
}
