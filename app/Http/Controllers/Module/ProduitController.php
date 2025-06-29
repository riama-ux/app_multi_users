<?php

namespace App\Http\Controllers\Module;

use App\Http\Controllers\Controller;

use App\Models\Produit;
use App\Models\Categorie;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    public function index(Request $request)
    {
        $magasinId = session('magasin_actif_id');

        if ($request->has('deleted')) {
        $produits = Produit::onlyTrashed()
            ->with('categorie')
            ->where('magasin_id', $magasinId)
            ->orderBy('nom')
            ->paginate(20);
        } else {
            $produits = Produit::with('categorie')
                ->where('magasin_id', $magasinId)
                ->orderBy('nom')
                ->paginate(20);
        }

        return view('module.produits.index', compact('produits'));
    }

    public function create()
    {
        $categories = Categorie::where('magasin_id', session('magasin_actif_id'))->get();

        return view('module.produits.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'prix_achat' => 'required|numeric|min:0',
            'cout_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'categorie_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
        ]);

        Produit::create([
            'nom' => $request->nom,
            'prix_achat' => $request->prix_achat,
            'cout_achat' => $request->cout_achat,
            'prix_vente' => $request->prix_vente,
            'categorie_id' => $request->categorie_id,
            'description' => $request->description,
            'magasin_id' => session('magasin_actif_id'),
        ]);

        return redirect()->route('module.produits.index')->with('success', 'Produit ajouté.');
    }

    public function edit(Produit $produit)
    {
        if ($produit->magasin_id != session('magasin_actif_id')) {
            abort(403, 'Accès interdit');
        }

        $this->authorize('view', $produit); // à créer si besoin

        if ($produit->trashed()) {
            return redirect()->route('module.produits.index')->with('error', 'Ce produit est supprimé.');
        }

        $categories = Categorie::where('magasin_id', session('magasin_actif_id'))->get();

        return view('module.produits.edit', compact('produit', 'categories'));
    }

    public function update(Request $request, Produit $produit)
    {
        if ($produit->magasin_id != session('magasin_actif_id')) {
            abort(403, 'Accès interdit');
        }

        $request->validate([
            'nom' => 'required|string',
            'prix_achat' => 'required|numeric|min:0',
            'cout_achat' => 'required|numeric|min:0', // ✅ Ajouté
            'prix_vente' => 'required|numeric|min:0',
            'categorie_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
        ]);

        $produit->update([
            'nom' => $request->nom,
            'prix_achat' => $request->prix_achat,
            'cout_achat' => $request->cout_achat, // ✅ Ajouté
            'prix_vente' => $request->prix_vente,
            'categorie_id' => $request->categorie_id,
            'description' => $request->description,
        ]);

        return redirect()->route('module.produits.index')->with('success', 'Produit modifié.');
    }

    public function destroy(Produit $produit)
    {
        $produit->delete();

        return redirect()->route('module.produits.index')->with('success', 'Produit supprimé.');
    }

    public function restore($id)
    {
        $magasinActif = session('magasin_actif_id'); // ou auth()->user()->magasin_actif_id selon ton cas

        $produit = Produit::onlyTrashed()
            ->where('magasin_id', $magasinActif)
            ->findOrFail($id);

        $produit->restore();

        return redirect()->route('module.produits.index')->with('success', 'Produit restauré.');
    }


}
