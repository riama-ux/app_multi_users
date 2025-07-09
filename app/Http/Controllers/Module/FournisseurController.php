<?php

namespace App\Http\Controllers\Module;

use App\Http\Controllers\Controller;

use App\Models\Fournisseur;
use App\Models\Magasin;
use Illuminate\Http\Request;

class FournisseurController extends Controller
{
    public function index()
    {
        $fournisseurs = Fournisseur::where('magasin_id', session('magasin_actif_id'))->latest()->get();
        return view('module.fournisseurs.index', compact('fournisseurs'));
    }

    public function create()
    {
        return view('module.fournisseurs.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'nom' => 'required|string|max:255',
        'telephone' => 'nullable|string',
        'email' => 'nullable|email',
        'adresse' => 'nullable|string',
    ]);

    $fournisseur = Fournisseur::create([
        'nom' => $request->nom,
        'telephone' => $request->telephone,
        'email' => $request->email,
        'adresse' => $request->adresse,
        'magasin_id' => session('magasin_actif_id'),
    ]);

    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'fournisseur' => [
                'id' => $fournisseur->id,
                'nom' => $fournisseur->nom,
            ],
        ]);
    }

    return redirect()->route('module.fournisseurs.index')->with('success', 'Fournisseur ajouté avec succès.');
}


    public function edit(Fournisseur $fournisseur)
    {
        $this->authorizeFournisseur($fournisseur);
        return view('module.fournisseurs.edit', compact('fournisseur'));
    }

    public function update(Request $request, Fournisseur $fournisseur)
    {
        $this->authorizeFournisseur($fournisseur);

        $request->validate([
            'nom' => 'required|string|max:255',
            'telephone' => 'nullable|string',
            'email' => 'nullable|email',
            'adresse' => 'nullable|string',
        ]);

        $fournisseur->update($request->all());

        return redirect()->route('module.fournisseurs.index')->with('success', 'Fournisseur mis à jour.');
    }

    public function show(Fournisseur $fournisseur)
    {
        if ($fournisseur->magasin_id !== session('magasin_actif_id')) {
            abort(403);
        }

        // Charger les commandes liées à ce fournisseur
        $fournisseur->load('commandes');

        return view('module.fournisseurs.show', compact('fournisseur'));
    }

    public function destroy(Fournisseur $fournisseur)
    {
        $this->authorizeFournisseur($fournisseur);
        $fournisseur->delete();

        return back()->with('success', 'Fournisseur supprimé.');
    }

    private function authorizeFournisseur(Fournisseur $fournisseur)
    {
        if ($fournisseur->magasin_id !== session('magasin_actif_id')) {
            abort(403);
        }
    }
}
