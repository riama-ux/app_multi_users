<?php

namespace App\Http\Controllers\Admin;

use App\Models\Magasin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MagasinController extends Controller
{
    public function index()
    {
        $magasins = Magasin::all();
        return view('pages.admin.magasins.index', compact('magasins'));
    }

    public function create()
    {
        return view('pages.admin.magasins.create');
    }

    public function store(Request $request)
    {
        $request->validate(['nom' => 'required']);
        Magasin::create($request->only(['nom', 'adresse']));
        return redirect()->route('admin.magasins.index')->with('success', 'Magasin créé.');
    }

    public function edit(Magasin $magasin)
    {
        return view('pages.admin.magasins.edit', compact('magasin'));
    }

    public function update(Request $request, Magasin $magasin)
    {
        $request->validate(['nom' => 'required']);
        $magasin->update($request->only(['nom', 'adresse']));
        return redirect()->route('admin.magasins.index')->with('success', 'Magasin mis à jour.');
    }

    public function destroy(Magasin $magasin)
    {
        $magasin->delete();
        return back()->with('success', 'Magasin supprimé.');
    }

}
