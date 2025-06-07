<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function adminHome(){
        return view('pages/admin/home');
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
