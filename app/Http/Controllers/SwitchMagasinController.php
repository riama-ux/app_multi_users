<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SwitchMagasinController extends Controller
{
    public function set(Request $request)
    {
        $request->validate([
        'magasin_id' => 'required|exists:magasins,id',
        ]);

        $user = Auth::user();

        if ($user->role !== 'Admin' && !$user->magasins->contains($request->magasin_id)) {
            abort(403, 'Magasin non autorisé.');
        }

        Session::put('magasin_actif_id', $request->magasin_id);

        return back()->with('success', 'Magasin actif mis à jour.');
    }

}