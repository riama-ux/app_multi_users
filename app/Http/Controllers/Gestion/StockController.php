<?php

namespace App\Http\Controllers\Gestion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Produit;
use App\Models\StockLot;

class StockController extends Controller
{
     public function index()
    {
        $magasinId = session('magasin_actif_id');

        $stocks = Stock::with('produit')
            ->where('magasin_id', $magasinId)
            ->get();

        return view('stocks.index', compact('stocks'));
    }
}
