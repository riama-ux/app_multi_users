<?php

namespace App\Http\Controllers\Gestion;

use App\Http\Controllers\Controller;
use App\Models\RetourClient;
use App\Models\LigneRetourClient;
use App\Models\Vente;
use App\Models\Produit;
use App\Models\Client;
use App\Models\Paiement; // Pour gérer les remboursements
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RetourClientController extends Controller
{
    public function index(Request $request)
    {
        $magasinId = session('magasin_actif_id');

        $query = RetourClient::with(['vente', 'client', 'user'])
            ->where('magasin_id', $magasinId)
            ->orderBy('date_retour', 'desc');

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('date')) {
            $query->whereDate('date_retour', $request->date);
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $retours = $query->paginate(15);
        $clients = Client::where('magasin_id', $magasinId)->get(); // Pour le filtre

        return view('retours_clients.index', compact('retours', 'clients'));
    }

    public function create(Request $request)
    {
        $magasinId = session('magasin_actif_id');
        $clients = Client::where('magasin_id', $magasinId)->get();
        $categories = \App\Models\Categorie::where('magasin_id', $magasinId)->get(); // Pour le modal de création de produit

        $vente = null;
        $lignesVente = collect();

        if ($request->has('vente_id')) {
            $vente = Vente::with('ligneVentes.produit')
                          ->where('magasin_id', $magasinId)
                          ->findOrFail($request->vente_id);
            $lignesVente = $vente->ligneVentes;
        }

        return view('retours_clients.create', compact('clients', 'categories', 'vente', 'lignesVente'));
    }

    public function store(Request $request)
    {
        $magasinId = session('magasin_actif_id');

        $validated = $request->validate([
            'vente_id' => 'nullable|exists:ventes,id',
            'client_id' => 'required|exists:clients,id',
            'date_retour' => 'required|date_format:Y-m-d\TH:i',
            'motif_global' => 'nullable|string|max:255',
            'montant_rembourse' => 'nullable|numeric|min:0',
            'lignes' => 'required|array|min:1',
            'lignes.*.produit_id' => 'required|exists:produits,id',
            'lignes.*.quantite_retournee' => 'required|numeric|min:0',
            'lignes.*.prix_unitaire_retour' => 'required|numeric|min:0',
            'lignes.*.motif_ligne' => 'nullable|string|max:255',
            'lignes.*.lot_id' => 'nullable|exists:stock_lots,id', // Lot d'origine
        ]);

        DB::beginTransaction();
        try {
            // CORRECTION ICI : Accès sécurisé à 'vente_id'
            // Utiliser $request->input() avec une valeur par défaut null
            $venteId = $request->input('vente_id'); // La validation 'nullable' garantit qu'il est null si non présent

            $retour = RetourClient::create([
                'vente_id' => $venteId, // Utiliser la variable $venteId sécurisée
                'client_id' => $validated['client_id'],
                'magasin_id' => $magasinId,
                'user_id' => Auth::id(),
                'date_retour' => $validated['date_retour'],
                'montant_rembourse' => $validated['montant_rembourse'] ?? 0,
                'motif_global' => $validated['motif_global'],
                'statut' => ($validated['montant_rembourse'] > 0) ? 'rembourse' : 'traite',
            ]);

            foreach ($validated['lignes'] as $ligneData) {
                $produit = Produit::where('id', $ligneData['produit_id'])
                                 ->where('magasin_id', $magasinId)
                                 ->first();

                if (!$produit) {
                    throw new \Exception("Produit avec l'ID {$ligneData['produit_id']} introuvable ou non disponible dans ce magasin.");
                }

                // Réintégrer le stock en utilisant la méthode reintegrerStockLot adaptée
                StockService::reintegrerStockLot(
                    $produit->id,
                    $ligneData['quantite_retournee'],
                    $magasinId,
                    Auth::id(),
                    "Retour client: " . ($ligneData['motif_ligne'] ?? $validated['motif_global']),
                    'retour_client', // sourceType
                    $retour->id,     // sourceId
                    $ligneData['lot_id'] ?? null, // lotId (peut être null)
                    $ligneData['prix_unitaire_retour'] // coutAchat pour le nouveau lot si besoin
                );

                LigneRetourClient::create([
                    'retour_client_id' => $retour->id,
                    'produit_id' => $produit->id,
                    'quantite_retournee' => $ligneData['quantite_retournee'],
                    'prix_unitaire_retour' => $ligneData['prix_unitaire_retour'],
                    'motif_ligne' => $ligneData['motif_ligne'],
                    'lot_id' => $ligneData['lot_id'] ?? null, // Enregistrer le lot d'origine si disponible
                ]);
            }

            // Enregistrer le remboursement si applicable
            if (($validated['montant_rembourse'] ?? 0) > 0) {

                $modePaiementRemboursement = 'autre'; 
                Paiement::create([
                    'vente_id' => $venteId, // Utiliser la variable $venteId sécurisée ici aussi
                    'retour_client_id' => $retour->id, // Lier au retour client
                    'montant' => -($validated['montant_rembourse']), // Montant négatif pour un remboursement
                    'mode_paiement' => $modePaiementRemboursement , // Ou un mode spécifique de remboursement
                    'user_id' => Auth::id(),
                    'date_paiement' => now(),
                    'motif' => 'Remboursement retour client #' . $retour->id,
                ]);
            }

            DB::commit();
            return redirect()->route('retours_clients.index')->with('success', 'Retour client enregistré avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de l\'enregistrement du retour: ' . $e->getMessage()])->withInput();
        }
    }


    public function show($id)
    {
        $magasinId = session('magasin_actif_id');
        $retour = RetourClient::with(['vente.ligneVentes.produit', 'client', 'user', 'lignesRetour.produit'])
                                ->where('magasin_id', $magasinId)
                                ->findOrFail($id);

        return view('retours_clients.show', compact('retour'));
    }

    public function edit($id)
    {
        $magasinId = session('magasin_actif_id');
        $retour = RetourClient::with(['lignesRetour.produit', 'vente.ligneVentes.produit'])
                                ->where('magasin_id', $magasinId)
                                ->findOrFail($id);
        $clients = Client::where('magasin_id', $magasinId)->get();
        $categories = \App\Models\Categorie::where('magasin_id', $magasinId)->get(); // Pour le modal de création de produit

        // Préparer les lignes de vente si la vente est liée
        $lignesVente = collect();
        if ($retour->vente) {
            $lignesVente = $retour->vente->ligneVentes;
        }

        return view('retours_clients.edit', compact('retour', 'clients', 'categories', 'lignesVente'));
    }

    public function update(Request $request, $id)
    {
        $magasinId = session('magasin_actif_id');
        $retour = RetourClient::with('lignesRetour')->where('magasin_id', $magasinId)->findOrFail($id);

        $validated = $request->validate([
            'vente_id' => 'nullable|exists:ventes,id',
            'client_id' => 'required|exists:clients,id',
            'date_retour' => 'required|date_format:Y-m-d\TH:i',
            'motif_global' => 'nullable|string|max:255',
            'montant_rembourse' => 'nullable|numeric|min:0',
            'lignes' => 'required|array|min:1',
            'lignes.*.id' => 'nullable|exists:ligne_retours_clients,id', // Pour les lignes existantes
            'lignes.*.produit_id' => 'required|exists:produits,id',
            'lignes.*.quantite_retournee' => 'required|numeric|min:0.01',
            'lignes.*.prix_unitaire_retour' => 'required|numeric|min:0',
            'lignes.*.motif_ligne' => 'nullable|string|max:255',
            'lignes.*.lot_id' => 'nullable|exists:stock_lots,id',
        ]);

        DB::beginTransaction();
        try {
            // Annuler les mouvements de stock des anciennes lignes de retour
            foreach ($retour->lignesRetour as $oldLigne) {
                // Pour annuler un retour (qui était une entrée de stock), on fait une sortie FIFO
                StockService::sortirFifo(
                    $oldLigne->produit_id,
                    $oldLigne->quantite_retournee,
                    'retour_client_annulation', // Type spécifique pour l'annulation
                    $retour->id,
                    $magasinId,
                    Auth::id(),
                    "Annulation retour client #{$retour->id} (modification)"
                );
            }

            // Supprimer toutes les anciennes lignes de retour
            $retour->lignesRetour()->delete();

            // Mettre à jour le retour principal
            $retour->update([
                'vente_id' => $validated['vente_id'],
                'client_id' => $validated['client_id'],
                'date_retour' => $validated['date_retour'],
                'montant_rembourse' => $validated['montant_rembourse'] ?? 0,
                'motif_global' => $validated['motif_global'],
                'statut' => ($validated['montant_rembourse'] > 0) ? 'rembourse' : 'traite',
            ]);

            // Créer les nouvelles lignes de retour et effectuer les mouvements de stock
            foreach ($validated['lignes'] as $ligneData) {
                $produit = Produit::where('id', $ligneData['produit_id'])
                                  ->where('magasin_id', $magasinId)
                                  ->first();

                if (!$produit) {
                    throw new \Exception("Produit avec l'ID {$ligneData['produit_id']} introuvable ou non disponible dans ce magasin.");
                }

                StockService::entrerStock(
                    $produit->id,
                    $ligneData['quantite_retournee'],
                    'retour_client',
                    $retour->id,
                    $magasinId,
                    Auth::id(),
                    "Retour client: " . ($ligneData['motif_ligne'] ?? $validated['motif_global']),
                    $ligneData['prix_unitaire_retour']
                );

                LigneRetourClient::create([
                    'retour_client_id' => $retour->id,
                    'produit_id' => $produit->id,
                    'quantite_retournee' => $ligneData['quantite_retournee'],
                    'prix_unitaire_retour' => $ligneData['prix_unitaire_retour'],
                    'motif_ligne' => $ligneData['motif_ligne'],
                    'lot_id' => $ligneData['lot_id'] ?? null,
                ]);
            }

            // Mettre à jour le remboursement si applicable
            // Supprimer l'ancien paiement de remboursement lié à ce retour si existant
            Paiement::where('retour_client_id', $retour->id)->delete();
                if (($validated['montant_rembourse'] ?? 0) > 0) {
                Paiement::create([
                    'vente_id' => $validated['vente_id'],
                    'retour_client_id' => $retour->id,
                    'montant' => -($validated['montant_rembourse']),
                    'mode_paiement' => 'remboursement',
                    'user_id' => Auth::id(),
                    'date_paiement' => now(),
                    'motif' => 'Remboursement retour client #' . $retour->id,
                ]);
            }


            DB::commit();
            return redirect()->route('retours_clients.index')->with('success', 'Retour client mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour du retour: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        $magasinId = session('magasin_actif_id');
        $retour = RetourClient::with('lignesRetour')->where('magasin_id', $magasinId)->findOrFail($id);

        DB::beginTransaction();
        try {
            // Annuler les mouvements de stock avant de supprimer le retour
            foreach ($retour->lignesRetour as $ligne) {
                // Pour annuler un retour (qui était une entrée de stock), on fait une sortie FIFO
                StockService::sortirFifo(
                    $ligne->produit_id,
                    $ligne->quantite_retournee,
                    'retour_client_annulation_suppression',
                    $retour->id,
                    $magasinId,
                    Auth::id(),
                    "Annulation retour client supprimé #{$retour->id}"
                );
            }

            // Supprimer les paiements de remboursement liés à ce retour
            Paiement::where('retour_client_id', $retour->id)->delete();

            $retour->delete(); // Supprime le retour et ses lignes grâce à onDelete('cascade')

            DB::commit();
            return redirect()->route('retours_clients.index')->with('success', 'Retour client supprimé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la suppression du retour: ' . $e->getMessage()]);
        }
    }
}
