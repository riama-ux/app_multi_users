<?php

namespace App\Http\Controllers\Module;

use App\Models\Commande;
use App\Models\Produit;
use App\Models\Magasin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommandeController extends Controller
{
    public function index()
    {
        return view('module.commandes.index', [
            'commandes' => Commande::with(['produit', 'magasin'])->orderByDesc('created_at')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('module.commandes.create', [
            'produits' => Produit::all(),
            'magasins' => Magasin::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'magasin_id' => 'required|exists:magasins,id',
            'quantite' => 'required|integer|min:1',
        ]);

        Commande::create([
            'produit_id' => $request->produit_id,
            'magasin_id' => $request->magasin_id,
            'quantite' => $request->quantite,
            'statut' => 'en attente',
        ]);

        return redirect()->route('module.commandes.index')->with('success', 'Commande enregistrée.');
    }

    public function edit(Commande $commande)
    {
        return view('module.commandes.edit', compact('commande'));
    }

    public function update(Request $request, Commande $commande)
    {
        $request->validate([
            'statut' => 'required|in:en attente,validée,refusée',
        ]);

        $commande->update([
            'statut' => $request->statut,
        ]);

        return redirect()->route('module.commandes.index')->with('success', 'Statut mis à jour.');
    }

    public function destroy(Commande $commande)
    {
        $commande->delete();

        return redirect()->route('module.commandes.index')->with('success', 'Commande supprimée.');
    }

    // Les méthodes inutilisées ici
    public function show(Commande $commande) { abort(404); }
}
