<?php

namespace App\Http\Controllers\Module;

use App\Http\Controllers\Controller;

use App\Models\Commande;
use App\Models\Fournisseur;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\LigneCommande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommandeController extends Controller
{
    public function index()
    {
        $commandes = Commande::with(['fournisseur', 'user'])
            ->where('magasin_id', session('magasin_actif_id'))
            ->orderByDesc('id')
            ->paginate(20);

        return view('module.commandes.index', compact('commandes'));
    }

    public function create()
    {
        $fournisseurs = Fournisseur::where('magasin_id', session('magasin_actif_id'))->get();
        $produits = Produit::where('magasin_id', session('magasin_actif_id'))->get();

        return view('module.commandes.create', compact('fournisseurs', 'produits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'date_commande' => 'required|date',
            'produits' => 'required|array|min:1',
            'quantites' => 'required|array|min:1',
            'prix_unitaires' => 'required|array|min:1',
        ]);

        $commande = Commande::create([
            'fournisseur_id' => $request->fournisseur_id,
            'user_id' => auth()->id(),
            'magasin_id' => session('magasin_actif_id'),
            'date_commande' => $request->date_commande,
            'statut' => 'en attente',
        ]);

        foreach ($request->produits as $index => $produit_id) {
            LigneCommande::create([
                'commande_id' => $commande->id,
                'produit_id' => $produit_id,
                'quantite' => $request->quantites[$index],
                'prix_unitaire' => $request->prix_unitaires[$index],
            ]);
        }

        return redirect()->route('module.commandes.index')->with('success', 'Commande enregistrée.');
    }

    public function show(Commande $commande)
    {
        if ($commande->magasin_id != session('magasin_actif_id')) abort(403);

        $commande->load('lignes.produit', 'fournisseur');

        return view('module.commandes.show', compact('commande'));
    }

    public function edit(Commande $commande)
    {
        if ($commande->magasin_id != session('magasin_actif_id')) abort(403);

        $fournisseurs = Fournisseur::where('magasin_id', session('magasin_actif_id'))->get();
        $produits = Produit::where('magasin_id', session('magasin_actif_id'))->get();
        $commande->load('lignes');

        return view('module.commandes.edit', compact('commande', 'fournisseurs', 'produits'));
    }

    public function update(Request $request, Commande $commande)
    {
        if ($commande->magasin_id != session('magasin_actif_id')) abort(403);

        $request->validate([
            'fournisseur_id' => 'required',
            'date_commande' => 'required|date',
            'produits' => 'required|array',
            'quantites' => 'required|array',
            'prix_unitaires' => 'required|array',
        ]);

        $commande->update([
            'fournisseur_id' => $request->fournisseur_id,
            'date_commande' => $request->date_commande,
        ]);

        $commande->lignes()->delete();

        foreach ($request->produits as $i => $produit_id) {
            LigneCommande::create([
                'commande_id' => $commande->id,
                'produit_id' => $produit_id,
                'quantite' => $request->quantites[$i],
                'prix_unitaire' => $request->prix_unitaires[$i],
            ]);
        }

        return redirect()->route('module.commandes.index')->with('success', 'Commande modifiée.');
    }

    public function destroy(Commande $commande)
    {
        if ($commande->magasin_id != session('magasin_actif_id')) abort(403);

        $commande->lignes()->delete();
        $commande->delete();

        return back()->with('success', 'Commande supprimée.');
    }

    /**
     * Méthode pour réceptionner la commande (changer statut + mise à jour stock)
     */
    public function recevoir($id)
    {
        $magasinId = session('magasin_actif_id');

        $commande = Commande::with('lignes')
            ->where('magasin_id', $magasinId)
            ->findOrFail($id);

        if ($commande->statut === 'livrée') {
            return redirect()->back()->with('info', 'La commande est déjà livrée.');
        }

        DB::transaction(function () use ($commande, $magasinId) {
            foreach ($commande->lignes as $ligne) {
                $stock = Stock::firstOrCreate(
                    ['produit_id' => $ligne->produit_id, 'magasin_id' => $magasinId],
                    ['quantite' => 0]
                );
                $stock->increment('quantite', $ligne->quantite);
            }

            $commande->update(['statut' => 'livrée']);
        });

        return redirect()->route('module.commandes.index')->with('success', 'Commande réceptionnée et stock mis à jour.');
    }
}
