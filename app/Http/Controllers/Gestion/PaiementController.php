<?php

namespace App\Http\Controllers\Gestion;
use App\Http\Controllers\Controller;

use App\Models\Paiement;
use App\Models\Vente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaiementController extends Controller
{
    public function store(Request $request, Vente $vente)
    {
        $magasinId = session('magasin_actif_id');
        // Vérification de la logique magasin
        if ($vente->magasin_id != $magasinId) {
            abort(403, 'Accès refusé : vente non liée au magasin actif.');
        }

        $request->validate([
            'montant' => 'required|numeric|min:1|max:' . $vente->reste_a_payer,
            'mode_paiement' => 'required|in:especes,mobile_money,virement,cheque,autre',
        ]);

        Paiement::create([
            'vente_id' => $vente->id,
            'montant' => $request->montant,
            'mode_paiement' => $request->mode_paiement,
            'date_paiement' => now(),
            'user_id' => Auth::id(),
        ]);

        // Mise à jour de la vente
        $totalPaye = $vente->paiements()->sum('montant');
        $reste = $vente->total_ttc - $totalPaye;

        $vente->update([
            'montant_paye' => $totalPaye,
            'reste_a_payer' => $reste,
            'statut' => $reste <= 0 ? 'payee' : ($totalPaye > 0 ? 'partielle' : 'credit'),
        ]);

        return redirect()->back()->with('success', 'Paiement ajouté avec succès.');
    }

    public function annulerPaiement($paiementId)
    {
        $paiement = Paiement::findOrFail($paiementId);
        $vente = $paiement->vente;

        $magasinId = session('magasin_actif_id');

        // Vérifie que la vente est bien liée au magasin actif
        if ($vente->magasin_id != $magasinId) {
            abort(403, 'Accès refusé : vente non liée au magasin actif.');
        }

        /* Vérifie que l'utilisateur a le droit d'annuler (à adapter)
        if (!auth()->user()->can('annuler-paiement')) {
            abort(403, "Non autorisé");
        }*/

        if ($paiement->annule) {
            return back()->with('error', 'Ce paiement est déjà annulé.');
        }

        \DB::transaction(function () use ($paiement, $vente) {
            // Marquer le paiement comme annulé
            $paiement->update(['annule' => true]);

            // Recalculer le montant payé
            $montantTotalPaye = $vente->paiements()->where('annule', false)->sum('montant');

            $reste = $vente->total_ttc - $montantTotalPaye;

            // Met à jour la vente
            $vente->update([
                'montant_paye' => $montantTotalPaye,
                'reste_a_payer' => $reste,
                'statut' => $reste <= 0 ? 'payee' : ($montantTotalPaye > 0 ? 'partielle' : 'credit'),
            ]);
        });

        return back()->with('success', 'Paiement annulé avec succès.');
    }

}
