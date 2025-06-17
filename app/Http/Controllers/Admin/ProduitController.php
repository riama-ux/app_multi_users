<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\Categorie;
use App\Models\Magasin;
use Illuminate\Support\Facades\Auth;

class ProduitController extends Controller
{
    public function index(Request $request)
    {
        $magasins = Auth::user()->role === 'Admin' ? Magasin::all() : Auth::user()->magasins;
        $magasin_id = $request->get('magasin_id', $magasins->first()?->id);

        $produits = Produit::where('magasin_id', $magasin_id)->with('categorie')->latest()->paginate(10);

        return view('admin.produits.index', compact('produits', 'magasins', 'magasin_id'));
    }

    public function create()
    {
        $magasins = Auth::user()->role === 'Admin' ? Magasin::all() : Auth::user()->magasins;
        $categories = Categorie::whereIn('magasin_id', $magasins->pluck('id'))->get();

        return view('admin.produits.create', compact('categories', 'magasins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'code' => 'required|unique:produits,code',
            'categorie_id' => 'required|exists:categories,id',
            'magasin_id' => 'required|exists:magasins,id',
            'prix_achat' => 'required|integer|min:0',
            'prix_vente' => 'required|integer|min:0',
        ]);

        Produit::create($request->only([
            'nom', 'code', 'categorie_id', 'magasin_id', 'prix_achat', 'prix_vente', 'description'
        ]));

        return redirect()->route('admin.produits.index')->with('success', 'Produit créé avec succès.');
    }

    public function edit(Produit $produit)
    {
        $this->authorizeAccess($produit);

        $magasins = Auth::user()->role === 'Admin' ? Magasin::all() : Auth::user()->magasins;
        $categories = Categorie::whereIn('magasin_id', $magasins->pluck('id'))->get();

        return view('admin.produits.edit', compact('produit', 'categories', 'magasins'));
    }

    public function update(Request $request, Produit $produit)
    {
        $this->authorizeAccess($produit);

        $request->validate([
            'nom' => 'required|string',
            'code' => 'required|unique:produits,code,' . $produit->id,
            'categorie_id' => 'required|exists:categories,id',
            'magasin_id' => 'required|exists:magasins,id',
            'prix_achat' => 'required|integer|min:0',
            'prix_vente' => 'required|integer|min:0',
        ]);

        $produit->update($request->only([
            'nom', 'code', 'categorie_id', 'magasin_id', 'prix_achat', 'prix_vente', 'description'
        ]));

        return redirect()->route('admin.produits.index')->with('success', 'Produit mis à jour.');
    }

    public function destroy(Produit $produit)
    {
        $this->authorizeAccess($produit);

        $produit->delete();
        return redirect()->route('admin.produits.index')->with('success', 'Produit supprimé.');
    }

    private function authorizeAccess(Produit $produit)
    {
        if (Auth::user()->role !== 'Admin' && !Auth::user()->magasins->contains($produit->magasin_id)) {
            abort(403, 'Accès refusé.');
        }
    }
}
