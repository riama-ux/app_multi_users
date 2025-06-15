<?php

namespace App\Http\Controllers\Module;

use App\Models\Categorie;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategorieController extends Controller
{
    public function index()
    {
        return view('module.categories.index', [
            'categories' => Categorie::orderBy('nom')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('module.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|unique:categories,nom',
        ]);

        Categorie::create([
            'nom' => $request->nom,
        ]);

        return redirect()->route('module.categories.index')->with('success', 'Catégorie créée.');
    }

    public function edit(Categorie $category)
    {
        return view('module.categories.edit', [
            'category' => $category,
        ]);
    }

    public function update(Request $request, Categorie $category)
    {
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
        $category->delete();

        return redirect()->route('module.categories.index')->with('success', 'Catégorie supprimée.');
    }
}

