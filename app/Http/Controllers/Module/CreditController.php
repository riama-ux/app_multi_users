<?php

namespace App\Http\Controllers\Module;

use App\Models\Credit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CreditController extends Controller
{
    public function index()
    {
        return view('module.credits.index', [
            'credits' => Credit::with('vente.client')->orderBy('date_echeance')->paginate(20),
        ]);
    }

    public function edit(Credit $credit)
    {
        return view('module.credits.edit', compact('credit'));
    }

    public function update(Request $request, Credit $credit)
    {
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

    // Ces méthodes ne sont pas utilisées
    public function create() { abort(404); }
    public function store(Request $request) { abort(404); }
    public function show(Credit $credit) { abort(404); }
    public function destroy(Credit $credit) { abort(404); }
}
