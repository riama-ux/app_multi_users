<?php

namespace App\Http\Controllers\Module;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClientController extends Controller
{
    public function index()
    {
        $magasinId = session('magasin_id');

        if (!$magasinId) {
            return redirect()->back()->with('error', 'Veuillez sélectionner un magasin.');
            // ou abort(403, 'Magasin non sélectionné');
        }

        $clients = Client::where('magasin_id', $magasinId)
                        ->orderByDesc('id')
                        ->paginate(20);

        return view('module.clients.index', compact('clients'));
    }

    public function create()
    {
        $magasinId = session('magasin_id');

        if (!$magasinId) {
            return redirect()->back()->with('error', 'Veuillez sélectionner un magasin.');
        }

        return view('module.clients.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|unique:clients,nom',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string',
            'adresse' => 'nullable|string',
        ]);

        $magasinId = session('magasin_id');
        if (!$magasinId) {
            return redirect()->back()->with('error', 'Veuillez sélectionner un magasin.');
        }

        Client::create(array_merge(
            $request->only(['nom', 'email', 'telephone', 'adresse']),
            ['magasin_id' => session('magasin_id')]
        ));

        return redirect()->route('module.clients.index')->with('success', 'Client ajouté avec succès.');
    }

    public function edit(Client $client)
    {
        $this->authorizeAccessToClient($client);
        return view('module.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $this->authorizeAccessToClient($client);

        $request->validate([
            'nom' => 'required|string|unique:clients,nom,' . $client->id,
            'email' => 'nullable|email',
            'telephone' => 'nullable|string',
            'adresse' => 'nullable|string',
        ]);

        // Met à jour en forçant magasin_id depuis la session
        $client->update(array_merge(
            $request->only(['nom', 'email', 'telephone', 'adresse']),
            ['magasin_id' => session('magasin_id')]
        ));

        return redirect()->route('module.clients.index')->with('success', 'Client mis à jour avec succès.');
    }

    public function destroy(Client $client)
    {
        $this->authorizeAccessToClient($client);
        $client->delete();

        return redirect()->route('module.clients.index')->with('success', 'Client supprimé.');
    }

    private function authorizeAccessToClient(Client $client)
    {
        if ($client->magasin_id != session('magasin_id')) {
            abort(403, 'Accès interdit à ce client.');
        }
    }

}
