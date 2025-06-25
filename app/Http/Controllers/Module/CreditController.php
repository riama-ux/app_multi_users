<?php

namespace App\Http\Controllers\Module;

use App\Http\Controllers\Controller;

use App\Models\Credit;
use App\Models\Client;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    public function index()
    {
        $credits = Credit::with(['client', 'vente'])
            ->where('magasin_id', session('magasin_actif_id'))
            ->orderByDesc('id')
            ->paginate(20);

        return view('module.credits.index', compact('credits'));
    }

    public function show(Credit $credit)
    {
        if ($credit->magasin_id != session('magasin_actif_id')) {
            abort(403);
        }

        return view('module.credits.show', compact('credit'));
    }

    public function edit(Credit $credit)
    {
        if ($credit->magasin_id != session('magasin_actif_id')) {
            abort(403);
        }

        return view('module.credits.edit', compact('credit'));
    }

    public function update(Request $request, Credit $credit)
    {
        if ($credit->magasin_id != session('magasin_actif_id')) {
            abort(403);
        }

        $request->validate([
            'statut' => 'required|in:payé,non payé',
            'echeance' => 'nullable|date',
        ]);

        $credit->update([
            'statut' => $request->statut,
            'echeance' => $request->echeance,
        ]);

        return redirect()->route('module.credits.index')->with('success', 'Crédit mis à jour.');
    }

    public function destroy(Credit $credit)
    {
        if ($credit->magasin_id != session('magasin_actif_id')) {
            abort(403);
        }

        $credit->delete();

        return back()->with('success', 'Crédit supprimé.');
    }
}
