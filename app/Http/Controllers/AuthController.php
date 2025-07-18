<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }


    public function logup()
    {
        return view('auth.logup');
    }

    public function postLogin(Request $request)
    {
        $request->validate(
            [
                'email' => 'required',
                'email' => 'email',
                'password' => 'required',
                'password' => 'min:4',
            ],
            [
                'email.required' => 'Champ obligatoire !',
                'email.email' => 'Le champ doit contenir un email valide !',
                'password.required' => 'Champ obligatoire !',
                'password.min' => 'Le champ doit contenir au moins 4 caractères !',
            ]
        );

        $input = $request->all();

        if (auth()->attempt(array('email' => $input['email'], 'password' => $input['password']))) {
            if (auth()->user()->role == 'Admin') {
                return redirect()->route('administrateur.home');
            } else if (auth()->user()->role == 'Vendeur') {
                return redirect()->route('vendeur.home');
            } else if (auth()->user()->role == 'Gestionnaire') {
                return redirect()->route('gestionnaire.home');
            } else {
                return redirect()->route('auth.login')
                    ->with('error', 'Ce compte n\'existe pas ou n\'est pas activé ! Veuillez contacter votre Administrateur !');
            }
        } else {
            return redirect()->route('auth.login')
                ->with('error', 'Email ou Mot de passe incorrect !');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('app.home');
    }

    protected function postLogup(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'name' => 'string',
                'email' => 'required',
                'email' => 'string',
                'email' => 'unique:users',
                'password' => 'required',
                'password' => 'min:4',
            ],
            [
                'name.required' => 'Champ obligatoire !',
                'name.string' => 'Le champ doit contenir des chaînes de caractères !',
                'email.required' => 'Champ obligatoire !',
                'email.string' => 'Le champ doit contenir des chaînes de caractères !',
                'email.unique' => 'Cet Email existe déjà !',
                'password.required' => 'Champ obligatoire !',
                'password.min' => 'Le champ doit contenir au moins 4 caractères !',
            ]
        );

        if ($request->input('password_confirmation') != $request->input('password')) {
            return redirect()->route('auth.logup')
                ->with('error', 'Les mots de passe ne sont pas identiques !');
        } else {
            User::create([
                'name' => strtoupper($request->input('name')),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
            ]);

            return redirect()->route('auth.login')
                ->with('success', 'Votre compte a été créé ! Maintenant veuillez contacter votre Administrateur pour activer le compte !');
        }
    }
}
