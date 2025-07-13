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
            ->orderBy('id', 'asc')
            ->paginate(20);

        return view('module.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('module.clients.create');
    }

    public function store(Request $request)
    {
        $magasinId = session('magasin_actif_id');
        // $userId = Auth::id(); // Ligne commentée car l'utilisateur ne sera plus lié directement au client

        try {
            $validatedData = $request->validate([
                'nom' => 'required|string|max:255',
                'telephone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'adresse' => 'nullable|string|max:500',
            ]);

            // Assurez-vous que le client est lié au magasin actif
            $validatedData['magasin_id'] = $magasinId;
            // $validatedData['user_id'] = $userId; // Ligne commentée car l'utilisateur ne sera plus lié directement au client

            $client = Client::create($validatedData);

            // Si la requête est AJAX, renvoyer une réponse JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Client ajouté avec succès !',
                    'client' => $client // Retourne l'objet client créé
                ], 201); // 201 Created
            }

            // Si ce n'est pas une requête AJAX, rediriger normalement
            return redirect()->route('module.clients.index')->with('success', 'Client ajouté avec succès !');

        } catch (ValidationException $e) {
            // Si la requête est AJAX et qu'il y a des erreurs de validation
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreurs de validation.',
                    'errors' => $e->errors() // Les erreurs de validation
                ], 422); // 422 Unprocessable Entity
            }
            // Sinon, Laravel gérera la redirection par défaut avec les erreurs
            throw $e;
        } catch (\Exception $e) {
            // Gérer d'autres exceptions non-validation
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur inattendue est survenue : ' . $e->getMessage()
                ], 500); // 500 Internal Server Error
            }
            return back()->with('error', 'Une erreur inattendue est survenue : ' . $e->getMessage())->withInput();
        }
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
