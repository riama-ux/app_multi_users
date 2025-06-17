<?php

namespace App\Http\Controllers\Module;

use App\Models\Credit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CreditController extends Controller
{
    public function index()
    {
        $magasinId = session('magasin_id');

        if (!$magasinId) {
            return redirect()->back()->with('error', 'Veuillez sélectionner un magasin.');
        }

        $credits = Credit::whereHas('vente', function ($query) use ($magasinId) {
                $query->where('magasin_id', $magasinId);
            })
            ->with('vente.client')
            ->orderBy('date_echeance')
            ->paginate(20);

        return view('module.credits.index', compact('credits'));
    }

    public function edit(Credit $credit)
    {
        $this->authorizeAccessToCredit($credit);

        return view('module.credits.edit', compact('credit'));
    }

    public function update(Request $request, Credit $credit)
    {
        $this->authorizeAccessToCredit($credit);

        $request->validate([
            'montant_rembourse' => 'required|integer|min:1',
        ]);

        $montant = (int) $request->montant_rembourse;

        if ($montant > $credit->montant_restant) {
            return back()->with('error', 'Le montant dépasse le crédit restant.');
        }

        $credit->montant_restant -= $montant;

        if ($credit->montant_restant <= 0) {
            $credit->montant_restant = 0;
            $credit->statut = 'payé';
        }

        $credit->save();

        return redirect()->route('module.credits.index')->with('success', 'Crédit mis à jour.');
    }

    private function authorizeAccessToCredit(Credit $credit)
    {
        $magasinId = session('magasin_id');

        if (!$magasinId || $credit->vente->magasin_id != $magasinId) {
            abort(403, 'Accès interdit à ce crédit.');
        }
    }

    // Méthodes non utilisées
    public function create() { abort(404); }
    public function store(Request $request) { abort(404); }
    public function show(Credit $credit) { abort(404); }
    public function destroy(Credit $credit) { abort(404); }
}
