<?php

namespace App\Http\Controllers\Gestion;
use App\Http\Controllers\Controller;

use App\Models\Produit;
use App\Models\Categorie;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    public function index()
    {
        $magasinId = session('magasin_actif_id');

        // Affiche produits actifs et supprimés
        $produits = Produit::withTrashed()
            ->where('magasin_id', $magasinId)
            ->paginate(15);

        return view('produits.index', compact('produits'));
    }

    public function create()
    {
        $magasinId = session('magasin_actif_id');
        $categories = Categorie::where('magasin_id', $magasinId)->get();

        return view('produits.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $magasinId = session('magasin_actif_id');

        // --- GESTION DES ERREURS DE VALIDATION POUR AJAX ---
        try {
            $validated = $request->validate([
                'nom' => 'required|string|max:255',
                'categorie_id' => 'required|exists:categories,id',
                'cout_achat' => 'required|numeric|min:0',
                'prix_vente' => 'required|numeric|min:0',
                'marge' => 'required|numeric|min:0',
                'seuil_alerte' => 'nullable|numeric|min:0',
                'code' => 'nullable|string|max:255',
                'reference' => 'nullable|string|max:255',
                'description' => 'required|string|max:1000',
                'marque' => 'required|string|max:255',
                'unite' => 'required|in:pièce,kg,litre,mètre,paquet',
            ]);
        } catch (ValidationException $e) {
            // Si la requête est AJAX et qu'il y a des erreurs de validation, on retourne du JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreurs de validation.',
                    'errors' => $e->errors()
                ], 422); // Le statut 422 est standard pour les erreurs de validation
            }
            // Sinon, Laravel gérera la redirection par défaut pour les requêtes non-AJAX
            throw $e;
        }

        $validated['magasin_id'] = $magasinId;

        // ➤ Code simple automatique si vide
        if (empty($validated['code'])) {
            $validated['code'] = 'C' . strtoupper(substr(uniqid(), -6)); // ex: C0925X7
        } else {
            // Vérifier l’unicité du code dans ce magasin
            $exists = Produit::where('magasin_id', $magasinId)
                ->where('code', $validated['code'])
                ->exists();

            if ($exists) {
                // --- MODIFICATION CLÉ ICI : Retourner une réponse JSON pour AJAX ---
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Le code fourni existe déjà dans ce magasin.',
                        'errors' => ['code' => ['Le code est déjà utilisé.']] // Format d'erreur cohérent pour le frontend
                    ], 409); // Le statut 409 Conflict est approprié pour un conflit de ressource
                }
                // Pour les requêtes non-AJAX, on garde la redirection standard
                return back()->withInput()->with('error', 'Le code fourni existe déjà dans ce magasin.');
            }
        }

        // Initialiser la quantité à 0 lors de la création du produit
        $validated['quantite'] = 0; 

        // Création du produit
        $produit = Produit::create($validated);

        // --- Réponse JSON pour AJAX en cas de succès ---
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'produit' => [
                    'id' => $produit->id,
                    'nom' => $produit->nom,
                    'code' => $produit->code,        
                    'reference' => $produit->reference, 
                    // ajoute d’autres infos utiles si besoin
                ]
            ], 201); // Le statut 201 Created est idéal pour une nouvelle ressource
        }

        // --- Redirection pour les requêtes non-AJAX en cas de succès ---
        return redirect()->route('commandes.create')->with('success', 'Produit créé avec succès.');
    }

    public function show($id)
    {
        $magasinId = session('magasin_actif_id');

        // Eager load the stockLots relationship and order them by reception date for FIFO
        $produit = Produit::withTrashed()
            ->where('magasin_id', $magasinId)
            ->with(['stockLots' => function ($query) {
                $query->orderBy('date_reception', 'asc'); // Order by oldest lots first
            }])
            ->findOrFail($id);

        return view('produits.show', compact('produit'));
    }

    public function edit($id)
    {
        $magasinId = session('magasin_actif_id');

        $produit = Produit::where('magasin_id', $magasinId)->findOrFail($id);
        $categories = Categorie::where('magasin_id', $magasinId)->get();

        return view('produits.edit', compact('produit', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $magasinId = session('magasin_actif_id');

        $produit = Produit::where('magasin_id', $magasinId)->findOrFail($id);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'reference' => 'nullable|string|max:255',
            'categorie_id' => 'required|exists:categories,id',
            'cout_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'marge' => 'required|numeric|min:0',
            'seuil_alerte' => 'nullable|integer|min:0',
            'code' => 'nullable|string|max:255',
            'description' => 'required|string|max:1000',
            'marque' => 'required|string|max:255',
            'unite' => 'required|in:pièce,kg,litre,mètre,paquet',
        ]);

        $produit->update($validated);

        return redirect()->route('produits.index')->with('success', 'Produit mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $magasinId = session('magasin_actif_id');

        $produit = Produit::where('magasin_id', $magasinId)->findOrFail($id);

        $produit->delete();

        return redirect()->route('produits.index')->with('success', 'Produit supprimé.');
    }

    // Restauration d'un produit soft deleted
    public function restore($id)
    {
        $magasinId = session('magasin_actif_id');

        $produit = Produit::onlyTrashed()->where('magasin_id', $magasinId)->findOrFail($id);

        $produit->restore();

        return redirect()->route('produits.index')->with('success', 'Produit restauré.');
    }

    // Suppression définitive
    public function forceDelete($id)
    {
        $magasinId = session('magasin_actif_id');

        $produit = Produit::onlyTrashed()->where('magasin_id', $magasinId)->findOrFail($id);

        $produit->forceDelete();

        return redirect()->route('produits.index')->with('success', 'Produit supprimé définitivement.');
    }

    public function getProduitInfo(Request $request)
    {
        $magasinId = session('magasin_actif_id');

        $produit = Produit::where('magasin_id', $magasinId)
            ->when($request->id, fn($q) => $q->orWhere('id', $request->id))
            ->when($request->code, fn($q) => $q->orWhere('code', $request->code))
            ->when($request->reference, fn($q) => $q->orWhere('reference', $request->reference))
            ->first();

        if (!$produit) {
            return response()->json(['error' => 'Produit introuvable'], 404);
        }

        return response()->json([
            'id' => $produit->id,
            'nom' => $produit->nom,
            'code' => $produit->code,
            'reference' => $produit->reference,
        ]);
    }

}
