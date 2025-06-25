<?php

namespace App\Http\Controllers\Module;

use App\Http\Controllers\Controller;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::where('magasin_id', session('magasin_actif_id'))
            ->orderBy('nom')
            ->paginate(20);

        return view('module.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('module.clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'telephone' => 'nullable|string',
            'email' => 'nullable|email',
            'adresse' => 'nullable|string',
        ]);

        Client::create([
            'nom' => $request->nom,
            'telephone' => $request->telephone,
            'email' => $request->email,
            'adresse' => $request->adresse,
            'magasin_id' => session('magasin_actif_id'),
        ]);

        return redirect()->route('module.clients.index')->with('success', 'Client ajouté avec succès.');
    }

    public function edit(Client $client)
    {
        if ($client->magasin_id != session('magasin_actif_id')) {
            abort(403);
        }

        return view('module.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        if ($client->magasin_id != session('magasin_actif_id')) {
            abort(403);
        }

        $request->validate([
            'nom' => 'required|string',
            'telephone' => 'nullable|string',
            'email' => 'nullable|email',
            'adresse' => 'nullable|string',
        ]);

        $client->update($request->only(['nom', 'telephone', 'email', 'adresse']));

        return redirect()->route('module.clients.index')->with('success', 'Client modifié avec succès.');
    }

    public function destroy(Client $client)
    {
        if ($client->magasin_id != session('magasin_actif_id')) {
            abort(403);
        }

        $client->delete();

        return back()->with('success', 'Client supprimé.');
    }
}
