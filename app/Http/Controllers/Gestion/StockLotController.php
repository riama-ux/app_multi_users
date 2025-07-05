<?php

namespace App\Http\Controllers\Gestion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\StockLot;

class StockLotController extends Controller
{
    public function index($produitId)
    {
        $magasinId = session('magasin_actif_id');

        $produit = Produit::findOrFail($produitId);

        $lots = StockLot::where('produit_id', $produitId)
            ->where('magasin_id', $magasinId)
            ->orderBy('date_reception', 'asc')
            ->get();

        return view('stock_lots.index', compact('produit', 'lots'));
    }
}
