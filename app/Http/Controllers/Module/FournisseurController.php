<?php

namespace App\Http\Controllers\Module;

use App\Models\Fournisseur;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FournisseurController extends Controller
{
    public function index()
    {
        $magasinId = session('magasin_id');

        if (!$magasinId) {
            return redirect()->back()->with('error', 'Veuillez sélectionner un magasin.');
        }

        $fournisseurs = Fournisseur::where('magasin_id', $magasinId)
            ->orderByDesc('id')
            ->paginate(20);

        return view('module.fournisseurs.index', compact('fournisseurs'));
    }

    public function create()
    {
        if (!session('magasin_id')) {
            return redirect()->back()->with('error', 'Veuillez sélectionner un magasin.');
        }

        return view('module.fournisseurs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|unique:fournisseurs,nom',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string',
            'adresse' => 'nullable|string',
        ]);

        $magasinId = session('magasin_id');

        if (!$magasinId) {
            return redirect()->back()->with('error', 'Veuillez sélectionner un magasin.');
        }

        Fournisseur::create(array_merge(
            $request->only(['nom', 'email', 'telephone', 'adresse']),
            ['magasin_id' => $magasinId]
        ));

        return redirect()->route('module.fournisseurs.index')->with('success', 'Fournisseur ajouté avec succès.');
    }

    public function edit(Fournisseur $fournisseur)
    {
        $this->authorizeAccessToFournisseur($fournisseur);

        return view('module.fournisseurs.edit', compact('fournisseur'));
    }

    public function update(Request $request, Fournisseur $fournisseur)
    {
        $this->authorizeAccessToFournisseur($fournisseur);

        $request->validate([
            'nom' => 'required|string|unique:fournisseurs,nom,' . $fournisseur->id,
            'email' => 'nullable|email',
            'telephone' => 'nullable|string',
            'adresse' => 'nullable|string',
        ]);

        $fournisseur->update($request->only(['nom', 'email', 'telephone', 'adresse']));

        return redirect()->route('module.fournisseurs.index')->with('success', 'Fournisseur mis à jour avec succès.');
    }

    public function destroy(Fournisseur $fournisseur)
    {
        $this->authorizeAccessToFournisseur($fournisseur);

        $fournisseur->delete();

        return redirect()->route('module.fournisseurs.index')->with('success', 'Fournisseur supprimé.');
    }

    private function authorizeAccessToFournisseur(Fournisseur $fournisseur)
    {
        $magasinId = session('magasin_id');

        if (!$magasinId || $fournisseur->magasin_id != $magasinId) {
            abort(403, 'Accès interdit à ce fournisseur.');
        }
    }
}
