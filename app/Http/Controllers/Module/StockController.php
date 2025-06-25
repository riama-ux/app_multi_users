<?php

namespace App\Http\Controllers\Module;

use App\Http\Controllers\Controller;

use App\Models\Stock;
use App\Models\Produit;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $magasinId = session('magasin_actif_id');

        $stocks = Stock::with('produit')
            ->where('magasin_id', $magasinId)
            ->orderByDesc('id')
            ->paginate(20);

        return view('module.stocks.index', compact('stocks'));
    }

    public function create()
    {
        $produits = Produit::where('magasin_id', session('magasin_actif_id'))->get();

        return view('module.stocks.create', compact('produits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'quantite' => 'required|integer|min:0',
        ]);

        $magasinId = session('magasin_actif_id');

        // Évite les doublons
        $existing = Stock::where('produit_id', $request->produit_id)
                         ->where('magasin_id', $magasinId)
                         ->first();

        if ($existing) {
            return back()->with('error', 'Ce produit a déjà un stock pour ce magasin.');
        }

        Stock::create([
            'produit_id' => $request->produit_id,
            'quantite' => $request->quantite,
            'magasin_id' => $magasinId,
        ]);

        return redirect()->route('module.stocks.index')->with('success', 'Stock ajouté.');
    }

    public function edit(Stock $stock)
    {
        // Vérification de sécurité
        if ($stock->magasin_id != session('magasin_actif_id')) {
            abort(403);
        }

        return view('stocks.edit', compact('stock'));
    }

    public function update(Request $request, Stock $stock)
    {
        if ($stock->magasin_id != session('magasin_actif_id')) {
            abort(403);
        }

        $request->validate([
            'quantite' => 'required|integer|min:0',
        ]);

        $stock->update([
            'quantite' => $request->quantite,
        ]);

        return redirect()->route('module.stocks.index')->with('success', 'Stock mis à jour.');
    }

    public function destroy(Stock $stock)
    {
        if ($stock->magasin_id != session('magasin_actif_id')) {
            abort(403);
        }

        $stock->delete();

        return back()->with('success', 'Stock supprimé.');
    }
}
