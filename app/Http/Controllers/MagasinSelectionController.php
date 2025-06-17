<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Magasin;

class MagasinSelectionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'magasin_id' => 'required|exists:magasins,id',
        ]);

        $magasin = Magasin::findOrFail($request->magasin_id);

        session([
            'magasin_id' => $magasin->id,
            'magasin_nom' => $magasin->nom,
        ]);

        return redirect()->back()->with('success', 'Magasin sélectionné avec succès.');
    }
}
