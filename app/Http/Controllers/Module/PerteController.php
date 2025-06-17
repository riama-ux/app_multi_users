<?php

namespace App\Http\Controllers\Module;

use App\Models\Perte;
use App\Models\Produit;
use App\Models\Stock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PerteController extends Controller
{
    public function index()
    {
        $magasinId = session('magasin_id');

        if (!$magasinId) {
            return redirect()->back()->with('error', 'Veuillez sélectionner un magasin.');
        }

        $pertes = Perte::with(['produit', 'magasin'])
            ->where('magasin_id', $magasinId)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('module.pertes.index', compact('pertes'));
    }

    public function create()
    {
        $magasinId = session('magasin_id');

        if (!$magasinId) {
            return redirect()->back()->with('error', 'Veuillez sélectionner un magasin.');
        }

        return view('module.pertes.create', [
            'produits' => Produit::all(),
        ]);
    }

    public function store(Request $request)
    {
        $magasinId = session('magasin_id');

        if (!$magasinId) {
            return redirect()->back()->with('error', 'Veuillez sélectionner un magasin.');
        }

        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'quantite'   => 'required|integer|min:1',
            'motif'      => 'nullable|string|max:255',
        ]);

        // Vérification du stock
        $stock = Stock::where('produit_id', $request->produit_id)
                      ->where('magasin_id', $magasinId)
                      ->first();

        if (!$stock || $stock->quantite < $request->quantite) {
            return back()->with('error', 'Stock insuffisant pour enregistrer cette perte.')->withInput();
        }

        // Enregistrement de la perte
        Perte::create([
            'produit_id' => $request->produit_id,
            'magasin_id' => $magasinId,
            'quantite'   => $request->quantite,
            'motif'      => $request->motif,
        ]);

        // Mise à jour du stock
        $stock->decrement('quantite', $request->quantite);

        return redirect()->route('module.pertes.index')->with('success', 'Perte enregistrée avec succès.');
    }

    public function destroy(Perte $perte)
    {
        $magasinId = session('magasin_id');

        if (!$magasinId || $perte->magasin_id != $magasinId) {
            abort(403, 'Accès interdit à cette perte.');
        }

        $perte->delete();

        return redirect()->route('module.pertes.index')->with('success', 'Perte supprimée.');
    }

    // Méthodes inutilisées
    public function show(Perte $perte) { abort(404); }
    public function edit(Perte $perte) { abort(404); }
    public function update(Request $request, Perte $perte) { abort(404); }
}
