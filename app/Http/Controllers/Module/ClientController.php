<?php

namespace App\Http\Controllers\Module;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClientController extends Controller
{
    public function index()
    {
        return view('module.clients.index', [
            'clients' => Client::orderByDesc('id')->paginate(20),
        ]);
    }

    public function create()
    {
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

        Client::create($request->all());

        return redirect()->route('module.clients.index')->with('success', 'Client ajouté avec succès.');
    }

    public function edit(Client $client)
    {
        return view('module.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'nom' => 'required|string|unique:clients,nom,' . $client->id,
            'email' => 'nullable|email',
            'telephone' => 'nullable|string',
            'adresse' => 'nullable|string',
        ]);

        $client->update($request->all());

        return redirect()->route('module.clients.index')->with('success', 'Client mis à jour avec succès.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('module.clients.index')->with('success', 'Client supprimé.');
    }
}
