<?php

namespace App\Http\Controllers\Module;

use App\Http\Controllers\Controller;

use App\Models\Categorie;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    public function index()
    {
        $categories = Categorie::where('magasin_id', session('magasin_actif_id'))
                               ->orderBy('nom')
                               ->paginate(20);

        return view('module.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('module.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        Categorie::create([
            'nom' => $request->nom,
            'magasin_id' => session('magasin_actif_id'),
        ]);

        return redirect()->route('module.categories.index')->with('success', 'Catégorie ajoutée.');
    }

    public function edit(Categorie $categorie)
    {
        if ($categorie->magasin_id != session('magasin_actif_id')) {
            abort(403);
        }

        return view('module.categories.edit', compact('categorie'));
    }

    public function update(Request $request, Categorie $categorie)
    {
        if ($categorie->magasin_id != session('magasin_actif_id')) {
            abort(403);
        }

        $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        $categorie->update([
            'nom' => $request->nom,
        ]);

        return redirect()->route('module.categories.index')->with('success', 'Catégorie mise à jour.');
    }

    public function destroy(Categorie $categorie)
    {
        if ($categorie->magasin_id != session('magasin_actif_id')) {
            abort(403);
        }

        $categorie->delete();

        return back()->with('success', 'Catégorie supprimée.');
    }
}
