<?php

namespace App\Http\Controllers\Module;

use App\Models\Vente;
use App\Models\Client;
use App\Models\Magasin;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\Credit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VenteController extends Controller
{
    public function index()
    {
        return view('module.ventes.index', [
            'ventes' => Vente::with(['produit', 'client', 'magasin'])->orderByDesc('id')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('module.ventes.create', [
            'produits' => Produit::all(),
            'clients' => Client::all(),
            'magasins' => Magasin::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'client_id' => 'required|exists:clients,id',
            'magasin_id' => 'required|exists:magasins,id',
            'quantite' => 'required|integer|min:1',
            'prix_unitaire' => 'required|integer|min:0',
            'mode_paiement' => 'required|in:espece,credit',
        ]);

        $total = $request->quantite * $request->prix_unitaire;

        // Vérifier le stock disponible
        $stock = Stock::where('produit_id', $request->produit_id)
                      ->where('magasin_id', $request->magasin_id)
                      ->first();

        if (!$stock || $stock->quantite < $request->quantite) {
            return back()->with('error', 'Stock insuffisant.')->withInput();
        }

        // Réduire le stock
        $stock->decrement('quantite', $request->quantite);

        $vente = Vente::create([
            'produit_id' => $request->produit_id,
            'client_id' => $request->client_id,
            'magasin_id' => $request->magasin_id,
            'quantite' => $request->quantite,
            'prix_unitaire' => $request->prix_unitaire,
            'total' => $total,
            'mode_paiement' => $request->mode_paiement,
        ]);

        // Créer un crédit si paiement par crédit
        if ($request->mode_paiement === 'credit') {
            Credit::create([
                'vente_id' => $vente->id,
                'montant_restant' => $total,
                'date_echeance' => now()->addDays(15), // exemple : 15 jours
            ]);
        }

        return redirect()->route('module.ventes.index')->with('success', 'Vente enregistrée.');
    }

    public function destroy(Vente $vente)
    {
        // Rétablir le stock
        $stock = Stock::where('produit_id', $vente->produit_id)
                      ->where('magasin_id', $vente->magasin_id)
                      ->first();

        if ($stock) {
            $stock->increment('quantite', $vente->quantite);
        }

        // Supprimer crédit lié
        if ($vente->credit) {
            $vente->credit->delete();
        }

        $vente->delete();

        return redirect()->route('module.ventes.index')->with('success', 'Vente supprimée.');
    }

    public function edit(Vente $vente)
    {
        // (Optionnel - pas toujours nécessaire)
        return view('module.ventes.edit', [
            'vente' => $vente,
            'produits' => Produit::all(),
            'clients' => Client::all(),
            'magasins' => Magasin::all(),
        ]);
    }

    public function update(Request $request, Vente $vente)
    {
        // (Optionnel - à activer si tu veux pouvoir modifier une vente après coup)
        // Nécessite de recalculer la différence de stock.
        abort(403, 'Modification de vente non autorisée.');
    }
}

