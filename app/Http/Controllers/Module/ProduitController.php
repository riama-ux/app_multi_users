<?php

namespace App\Http\Controllers\Module;

use App\Models\Produit;
use App\Models\Categorie;
use App\Models\Stock;
use App\Models\Magasin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProduitController extends Controller
{
    public function index()
    {
        return view('module.produits.index', [
            'produits' => Produit::with('categorie')->orderBy('id', 'desc')->paginate(20),
        ]);
    }

    public function create()
    {
        if (auth()->user()->role === 'Manager') {
            abort(403, 'Accès interdit.');
        }

        return view('module.produits.create', [
            'categories' => Categorie::all(),
            'magasins' => Magasin::all(),
        ]);
    }

    public function store(Request $request)
    {
        if (auth()->user()->role === 'Manager') {
            abort(403, 'Accès interdit.');
        }

        $request->validate([
            'nom' => 'required',
            'code' => 'required|unique:produits',
            'categorie_id' => 'required|exists:categories,id',
            'prix_achat' => 'required|integer',
            'cout_achat' => 'required|integer',
            'prix_vente' => 'required|integer',
            'magasins' => 'required|array|min:1',
        ]);

        $produit = Produit::create([
            'nom' => $request->nom,
            'code' => $request->code,
            'categorie_id' => $request->categorie_id,
            'prix_achat' => $request->prix_achat,
            'cout_achat' => $request->cout_achat,
            'prix_vente' => $request->prix_vente,
            'description' => $request->description,
        ]);

        foreach ($request->magasins as $magasinId) {
            Stock::create([
                'produit_id' => $produit->id,
                'magasin_id' => $magasinId,
                'quantite' => 0,
            ]);
        }

        return redirect()->route('module.produits.index')->with('success', 'Produit créé avec succès.');
    }

    public function edit(Produit $produit)
    {
        if (auth()->user()->role === 'Manager') {
            abort(403, 'Accès interdit.');
        }

        return view('module.produits.edit', [
            'produit' => $produit,
            'categories' => Categorie::all(),
            'magasins' => Magasin::all(),
        ]);
    }

    public function update(Request $request, Produit $produit)
    {
        if (auth()->user()->role === 'Manager') {
            abort(403, 'Accès interdit.');
        }

        $request->validate([
            'nom' => 'required',
            'code' => 'required|unique:produits,code,' . $produit->id,
            'categorie_id' => 'required|exists:categories,id',
            'prix_achat' => 'required|integer',
            'cout_achat' => 'required|integer',
            'prix_vente' => 'required|integer',
        ]);

        $produit->update([
            'nom' => $request->nom,
            'code' => $request->code,
            'categorie_id' => $request->categorie_id,
            'prix_achat' => $request->prix_achat,
            'cout_achat' => $request->cout_achat,
            'prix_vente' => $request->prix_vente,
            'description' => $request->description,
        ]);

        return redirect()->route('module.produits.index')->with('success', 'Produit mis à jour avec succès.');
    }

    public function destroy(Produit $produit)
    {
        if (auth()->user()->role === 'Manager') {
            abort(403, 'Accès interdit.');
        }

        $produit->stocks()->delete();
        $produit->delete();

        return redirect()->route('module.produits.index')->with('success', 'Produit supprimé.');
    }
}
