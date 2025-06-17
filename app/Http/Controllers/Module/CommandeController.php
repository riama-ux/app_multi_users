<?php


namespace App\Http\Controllers\Module;

use App\Models\Commande;
use App\Models\Produit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommandeController extends Controller
{
    public function index()
    {
        $magasinId = session('magasin_id');

        if (!$magasinId) {
            return redirect()->back()->with('error', 'Veuillez sélectionner un magasin.');
        }

        $commandes = Commande::with(['produit'])
            ->where('magasin_id', $magasinId)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('module.commandes.index', compact('commandes'));
    }

    public function create()
    {
        $magasinId = session('magasin_id');

        if (!$magasinId) {
            return redirect()->back()->with('error', 'Veuillez sélectionner un magasin.');
        }

        return view('module.commandes.create', [
            'produits' => Produit::where('magasin_id', $magasinId)->get(), // produits du magasin uniquement
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
            'quantite' => 'required|integer|min:1',
        ]);

        // Vérification que le produit appartient au magasin actif
        $produit = Produit::where('id', $request->produit_id)
                          ->where('magasin_id', $magasinId)
                          ->first();

        if (!$produit) {
            return redirect()->back()->with('error', 'Produit non autorisé pour ce magasin.');
        }

        Commande::create([
            'produit_id' => $request->produit_id,
            'magasin_id' => $magasinId,
            'quantite' => $request->quantite,
            'statut' => 'en attente',
        ]);

        return redirect()->route('module.commandes.index')->with('success', 'Commande enregistrée.');
    }

    public function edit(Commande $commande)
    {
        if ($commande->magasin_id != session('magasin_id')) {
            abort(403, 'Accès interdit à cette commande.');
        }

        return view('module.commandes.edit', compact('commande'));
    }

    public function update(Request $request, Commande $commande)
    {
        if ($commande->magasin_id != session('magasin_id')) {
            abort(403, 'Accès interdit à cette commande.');
        }

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
        if ($commande->magasin_id != session('magasin_id')) {
            abort(403, 'Accès interdit à cette commande.');
        }

        $commande->delete();

        return redirect()->route('module.commandes.index')->with('success', 'Commande supprimée.');
    }

    public function show(Commande $commande)
    {
        abort(404);
    }
}
