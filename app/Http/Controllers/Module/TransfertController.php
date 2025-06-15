<?php

namespace App\Http\Controllers\Module;

use App\Models\Transfert;
use App\Models\Produit;
use App\Models\Magasin;
use App\Models\Stock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransfertController extends Controller
{
    public function index()
    {
        return view('module.transferts.index', [
            'transferts' => Transfert::with(['produit', 'source', 'destination'])
                            ->orderByDesc('created_at')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('module.transferts.create', [
            'produits' => Produit::all(),
            'magasins' => Magasin::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'source_id' => 'required|exists:magasins,id|different:destination_id',
            'destination_id' => 'required|exists:magasins,id|different:source_id',
            'quantite' => 'required|integer|min:1',
            'commentaire' => 'nullable|string|max:255',
        ]);

        // Vérification du stock dans le magasin source
        $stockSource = Stock::where('produit_id', $request->produit_id)
                            ->where('magasin_id', $request->source_id)
                            ->first();

        if (!$stockSource || $stockSource->quantite < $request->quantite) {
            return back()->with('error', 'Stock insuffisant dans le magasin source.')->withInput();
        }

        // Décrémenter dans le magasin source
        $stockSource->decrement('quantite', $request->quantite);

        // Incrémenter dans le magasin destination
        $stockDestination = Stock::firstOrCreate([
            'produit_id' => $request->produit_id,
            'magasin_id' => $request->destination_id,
        ], ['quantite' => 0]);

        $stockDestination->increment('quantite', $request->quantite);

        // Enregistrer le transfert
        Transfert::create([
            'produit_id' => $request->produit_id,
            'source_id' => $request->source_id,
            'destination_id' => $request->destination_id,
            'quantite' => $request->quantite,
            'commentaire' => $request->commentaire,
        ]);

        return redirect()->route('module.transferts.index')->with('success', 'Transfert enregistré avec succès.');
    }

    public function destroy(Transfert $transfert)
    {
        // Optionnel : restauration des stocks (non recommandé sauf erreur)
        $transfert->delete();

        return redirect()->route('module.transferts.index')->with('success', 'Transfert supprimé.');
    }

    // Méthodes non utilisées
    public function show(Transfert $transfert) { abort(404); }
    public function edit(Transfert $transfert) { abort(404); }
    public function update(Request $request, Transfert $transfert) { abort(404); }
}

