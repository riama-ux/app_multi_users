<?php

namespace App\Http\Controllers\Module;

use App\Models\Fournisseur;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FournisseurController extends Controller
{
    public function index()
    {
        return view('module.fournisseurs.index', [
            'fournisseurs' => Fournisseur::orderByDesc('id')->paginate(20),
        ]);
    }

    public function create()
    {
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

        Fournisseur::create($request->all());

        return redirect()->route('module.fournisseurs.index')->with('success', 'Fournisseur ajouté avec succès.');
    }

    public function edit(Fournisseur $fournisseur)
    {
        return view('module.fournisseurs.edit', compact('fournisseur'));
    }

    public function update(Request $request, Fournisseur $fournisseur)
    {
        $request->validate([
            'nom' => 'required|string|unique:fournisseurs,nom,' . $fournisseur->id,
            'email' => 'nullable|email',
            'telephone' => 'nullable|string',
            'adresse' => 'nullable|string',
        ]);

        $fournisseur->update($request->all());

        return redirect()->route('module.fournisseurs.index')->with('success', 'Fournisseur mis à jour avec succès.');
    }

    public function destroy(Fournisseur $fournisseur)
    {
        $fournisseur->delete();

        return redirect()->route('module.fournisseurs.index')->with('success', 'Fournisseur supprimé.');
    }
}
