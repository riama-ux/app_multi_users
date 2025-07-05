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

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            
            'categorie_id' => 'required|exists:categories,id',
            'cout_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'marge' => 'required|numeric|min:0',
            'seuil_alerte' => 'nullable|integer|min:0',
        ]);

        $validated['magasin_id'] = $magasinId;

        Produit::create($validated);

        return redirect()->route('produits.index')->with('success', 'Produit créé avec succès.');
    }

    public function show($id)
    {
        $magasinId = session('magasin_actif_id');

        $produit = Produit::withTrashed()
            ->where('magasin_id', $magasinId)
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
}
