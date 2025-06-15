<?php

namespace App\Http\Controllers\Module;

use App\Models\Stock;
use App\Models\Magasin;
use App\Models\Produit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StockController extends Controller
{
    public function index()
    {
        return view('module.stocks.index', [
            'magasins' => Magasin::with(['stocks.produit'])->get(),
        ]);
    }

    public function edit(Stock $stock)
    {
        return view('module.stocks.edit', [
            'stock' => $stock,
        ]);
    }

    public function update(Request $request, Stock $stock)
    {
        $request->validate([
            'quantite' => 'required|integer|min:0',
        ]);

        $stock->update([
            'quantite' => $request->quantite,
        ]);

        return redirect()->route('module.stocks.index')->with('success', 'Quantité mise à jour avec succès.');
    }

    // Les méthodes suivantes ne sont pas utiles ici
    public function create() { abort(404); }
    public function store(Request $request) { abort(404); }
    public function show(Stock $stock) { abort(404); }
    public function destroy(Stock $stock) { abort(404); }
}

