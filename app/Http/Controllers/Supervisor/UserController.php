<?php

namespace App\Http\Controllers\Supervisor;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function edit($id){
        return view('pages/supervisor/user', [
            'user' => User::find(decrypt($id)),
        ]);
    }

    public function update(Request $request, $id){
        $user = User::find($id);
        $request->validate(
            [
                'name' => 'required',
                'new_password' => 'required',
                'new_password' => 'min:4',
                'password_confirmation' => 'required',
            ],
            [
                'name.required' => 'Champ obligatoire !',
                'new_password.required' => 'Champ obligatoire !',
                'new_password.min' => 'Le champ doit contenir au moins 4 caractères !',
                'password_confirmation.required' => 'Champ obligatoire !',
            ]
        );

        if ($request->new_password != $request->password_confirmation) {
            return to_route('supervisor.user.edit', encrypt($user->id))->with('error', "Les mots de passe ne sont pas identiques !");
        } 
        else {
            $user->update([
                'name' => strtoupper($request->get('name')),
                'password' => Hash::make($request->get('new_password')),
            ]);
        }

        return to_route('gestionnaire.user.edit', encrypt($user->id))->with('success', "La modification a été effectuée !");
    }
}
