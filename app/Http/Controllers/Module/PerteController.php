<?php

namespace App\Http\Controllers\Module;

use App\Models\Perte;
use App\Models\Produit;
use App\Models\Magasin;
use App\Models\Stock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PerteController extends Controller
{
    public function index()
    {
        return view('module.pertes.index', [
            'pertes' => Perte::with(['produit', 'magasin'])->orderByDesc('created_at')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('module.pertes.create', [
            'produits' => Produit::all(),
            'magasins' => Magasin::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'magasin_id' => 'required|exists:magasins,id',
            'quantite'   => 'required|integer|min:1',
            'motif'      => 'nullable|string|max:255',
        ]);

        // Vérification stock
        $stock = Stock::where('produit_id', $request->produit_id)
                      ->where('magasin_id', $request->magasin_id)
                      ->first();

        if (!$stock || $stock->quantite < $request->quantite) {
            return back()->with('error', 'Stock insuffisant pour enregistrer cette perte.')->withInput();
        }

        // Enregistrer la perte
        Perte::create([
            'produit_id' => $request->produit_id,
            'magasin_id' => $request->magasin_id,
            'quantite'   => $request->quantite,
            'motif'      => $request->motif,
        ]);

        // Mettre à jour le stock
        $stock->decrement('quantite', $request->quantite);

        return redirect()->route('module.pertes.index')->with('success', 'Perte enregistrée avec succès.');
    }

    public function destroy(Perte $perte)
    {
        // Optionnel : restituer la quantité perdue dans le stock ?
        // Ici on choisit de ne pas le faire (irréversible).

        $perte->delete();

        return redirect()->route('module.pertes.index')->with('success', 'Perte supprimée.');
    }

    // Méthodes inutilisées
    public function show(Perte $perte) { abort(404); }
    public function edit(Perte $perte) { abort(404); }
    public function update(Request $request, Perte $perte) { abort(404); }
}
