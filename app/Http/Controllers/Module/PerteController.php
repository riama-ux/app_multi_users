<?php

namespace App\Http\Controllers\Module;

use App\Http\Controllers\Controller;

use App\Models\Perte;
use App\Models\Produit;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PerteController extends Controller
{
    public function index()
    {
        $magasinId = session('magasin_actif_id');

        $pertes = Perte::with('produit')
            ->where('magasin_id', $magasinId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('module.pertes.index', compact('pertes'));
    }

    public function create()
    {
        $magasinId = session('magasin_actif_id');

        // On ne liste que les produits en stock dans le magasin actif
        $produits = Produit::whereHas('stocks', function ($query) use ($magasinId) {
            $query->where('magasin_id', $magasinId)
                  ->where('quantite', '>', 0);
        })->get();

        return view('module.pertes.create', compact('produits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'quantite' => 'required|integer|min:1',
            'motif' => 'nullable|string|max:255',
        ]);

        $magasinId = session('magasin_actif_id');

        $stock = Stock::where('produit_id', $request->produit_id)
                      ->where('magasin_id', $magasinId)
                      ->first();

        if (!$stock || $stock->quantite < $request->quantite) {
            return back()->with('error', 'Stock insuffisant pour cette perte.')->withInput();
        }

        DB::transaction(function () use ($request, $magasinId, $stock) {
            Perte::create([
                'produit_id' => $request->produit_id,
                'magasin_id' => $magasinId,
                'user_id' => Auth::id(),
                'quantite' => $request->quantite,
                'motif' => $request->motif,
            ]);

            $stock->decrement('quantite', $request->quantite);
        });

        return redirect()->route('module.pertes.index')->with('success', 'Perte enregistrée avec succès.');
    }

    public function show($id)
    {
        //
    }

    // Affiche le formulaire d'édition
    public function edit($id)
    {
        $magasinId = session('magasin_actif_id');

        $perte = Perte::where('magasin_id', $magasinId)->findOrFail($id);

        $produits = Produit::whereHas('stocks', function ($query) use ($magasinId) {
            $query->where('magasin_id', $magasinId)
                ->where('quantite', '>=', 0); // On peut modifier même si stock = 0
        })->get();

        return view('module.pertes.edit', compact('perte', 'produits'));
    }

    // Met à jour la perte
    public function update(Request $request, $id)
    {
        $magasinId = session('magasin_actif_id');
        $perte = Perte::where('magasin_id', $magasinId)->findOrFail($id);

        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'quantite' => 'required|integer|min:1',
            'motif' => 'nullable|string|max:255',
        ]);

        // Récupérer le stock lié à l'ancienne perte
        $stock = Stock::where('produit_id', $perte->produit_id)
                    ->where('magasin_id', $magasinId)
                    ->first();

        // Récupérer le stock lié au nouveau produit (peut être différent)
        $newStock = Stock::where('produit_id', $request->produit_id)
                        ->where('magasin_id', $magasinId)
                        ->first();

        if (!$newStock) {
            return back()->with('error', 'Stock inexistant pour le produit sélectionné.')->withInput();
        }

        // Calcule la différence de quantité entre ancienne et nouvelle perte
        $quantiteAncienne = $perte->quantite;
        $quantiteNouvelle = $request->quantite;
        $diff = $quantiteNouvelle - $quantiteAncienne;

        // On vérifie si la modification est possible en fonction du stock disponible
        if ($diff > 0 && $newStock->quantite < $diff) {
            return back()->with('error', 'Stock insuffisant pour augmenter la perte.')->withInput();
        }

        DB::transaction(function () use ($perte, $request, $magasinId, $stock, $newStock, $diff) {
            // Restaurer le stock de l'ancienne perte
            $stock->increment('quantite', $perte->quantite);

            // Décrémenter le stock du nouveau produit de la nouvelle perte
            $newStock->decrement('quantite', $request->quantite);

            // Mettre à jour la perte
            $perte->update([
                'produit_id' => $request->produit_id,
                'quantite' => $request->quantite,
                'motif' => $request->motif,
            ]);
        });

        return redirect()->route('module.pertes.index')->with('success', 'Perte modifiée avec succès.');
    }

    // Supprimer une perte
    public function destroy($id)
    {
        $magasinId = session('magasin_actif_id');
        $perte = Perte::where('magasin_id', $magasinId)->findOrFail($id);

        DB::transaction(function () use ($perte, $magasinId) {
            // Restaurer le stock
            $stock = Stock::where('produit_id', $perte->produit_id)
                        ->where('magasin_id', $magasinId)
                        ->first();

            if ($stock) {
                $stock->increment('quantite', $perte->quantite);
            }

            // Supprimer la perte
            $perte->delete();
        });

        return redirect()->route('module.pertes.index')->with('success', 'Perte supprimée avec succès.');
    }

}
