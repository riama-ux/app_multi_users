<?php

namespace App\Http\Controllers\Gestion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MouvementStock;


class MouvementStockController extends Controller
{
    public function index(Request $request)
    {
        $magasinId = session('magasin_actif_id');

        $query = MouvementStock::with(['produit', 'user', 'lot'])
            ->where('magasin_id', $magasinId)
            ->latest('date');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('produit')) {
            $query->whereHas('produit', function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->produit . '%');
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $mouvements = $query->paginate(15);

        return view('mouvements_stock.index', compact('mouvements'));
    }

}
