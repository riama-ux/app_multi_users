<?php

namespace App\Http\Controllers\Module;

use App\Models\Categorie;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategorieController extends Controller
{
    public function index()
    {
        $magasinId = session('magasin_id');
        if (!$magasinId) {
            return redirect()->back()->with('error', 'Veuillez sélectionner un magasin.');
        }

        $categories = Categorie::where('magasin_id', $magasinId)
                               ->orderBy('nom')
                               ->paginate(20);

        return view('module.categories.index', compact('categories'));
    }

    public function create()
    {
        if (!session('magasin_id')) {
            return redirect()->back()->with('error', 'Veuillez sélectionner un magasin.');
        }

        return view('module.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|unique:categories,nom',
        ]);

        $magasinId = session('magasin_id');
        if (!$magasinId) {
            return redirect()->back()->with('error', 'Veuillez sélectionner un magasin.');
        }

        Categorie::create([
            'nom' => $request->nom,
            'magasin_id' => $magasinId,
        ]);

        return redirect()->route('module.categories.index')->with('success', 'Catégorie créée.');
    }

    public function edit(Categorie $category)
    {
        $this->authorizeAccessToCategorie($category);

        return view('module.categories.edit', compact('category'));
    }

    public function update(Request $request, Categorie $category)
    {
        $this->authorizeAccessToCategorie($category);

        $request->validate([
            'nom' => 'required|unique:categories,nom,' . $category->id,
        ]);

        $category->update([
            'nom' => $request->nom,
        ]);

        return redirect()->route('module.categories.index')->with('success', 'Catégorie modifiée.');
    }

    public function destroy(Categorie $category)
    {
        $this->authorizeAccessToCategorie($category);

        $category->delete();

        return redirect()->route('module.categories.index')->with('success', 'Catégorie supprimée.');
    }

    private function authorizeAccessToCategorie(Categorie $category)
    {
        if ($category->magasin_id !== session('magasin_id')) {
            abort(403, 'Accès non autorisé à cette catégorie.');
        }
    }
}