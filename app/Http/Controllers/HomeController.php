<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Magasin;

class HomeController extends Controller
{
    public function adminHome(){
        $magasinActif = Magasin::find(session('magasin_actif_id'));
        return view('pages/admin/home', compact('magasinActif'));
    }

    public function managerHome(){
        return view('pages/manager/home');
    }

    public function supervisorHome(){
        return view('pages/supervisor/home');
    }

    public function userHome(){
        return view('pages/user/home');
    }
}
