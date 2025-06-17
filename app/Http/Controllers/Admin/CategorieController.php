<?php

namespace App\Http\Controllers\Admin;

use App\Models\Categorie;
use App\Models\Magasin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CategorieController extends Controller
{
    public function index(Request $request)
    {
        $magasins = Auth::user()->role === 'Admin'
            ? Magasin::all()
            : Auth::user()->magasins;

        $magasin_id = $request->get('magasin_id', $magasins->first()?->id);

        $categories = Categorie::where('magasin_id', $magasin_id)->latest()->paginate(10);

        return view('admin.categories.index', compact('categories', 'magasins', 'magasin_id'));
    }

    public function create()
    {
        $magasins = Auth::user()->role === 'Admin'
            ? Magasin::all()
            : Auth::user()->magasins;

        return view('admin.categories.create', compact('magasins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'magasin_id' => 'required|exists:magasins,id',
        ]);

        Categorie::create($request->only(['nom', 'magasin_id']));

        return redirect()->route('admin.categories.index')->with('success', 'Catégorie créée avec succès.');
    }

    public function edit(Categorie $category)
    {
        $this->authorizeAccess($category);

        $magasins = Auth::user()->role === 'Admin'
            ? Magasin::all()
            : Auth::user()->magasins;

        return view('admin.categories.edit', [
            'category' => $category,
            'magasins' => $magasins
        ]);
    }

    public function update(Request $request, Categorie $category)
    {
        $this->authorizeAccess($category);

        $request->validate([
            'nom' => 'required|string',
            'magasin_id' => 'required|exists:magasins,id',
        ]);

        $category->update($request->only(['nom', 'magasin_id']));

        return redirect()->route('admin.categories.index')->with('success', 'Catégorie modifiée.');
    }

    public function destroy(Categorie $category)
    {
        $this->authorizeAccess($category);
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Catégorie supprimée.');
    }

    private function authorizeAccess(Categorie $category)
    {
        if (Auth::user()->role !== 'Admin' && !Auth::user()->magasins->contains($category->magasin_id)) {
            abort(403, 'Accès refusé.');
        }
    }
}
