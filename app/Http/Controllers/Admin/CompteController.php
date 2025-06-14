<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class CompteController extends Controller
{
    public function index()
    {
        return view('pages/admin/users/index', [
            'users' => User::WhereNotIn('email', ['admin@app.local', 'manager@app.local', 'supervisor@app.local', 'johndoe@app.local'])->orderbyDesc('id')->paginate(20),
            'rows' =>  User::count() - 4,
            'roles' => ['Admin', 'Supervisor', 'Manager', 'User', 'Non Actif'],
        ]);
    }

    public function search(Request $request)
    {
        if ($request->name == '' && $request->role == '' && $request->email == '') {
            return redirect()->route('admin.compte.index');
        } else {
            if ($request->name == '') {
                $name = '%';
            } else $name = strtoupper($request->name);
            if ($request->role == '') {
                $role = '%';
            } else $role = $request->role;
            if ($request->email == '') {
                $email = '%';
            } else $email = $request->email;

            $user = User::Where('name', 'LIKE', '%' . $name . '%')
                ->Where('role', 'LIKE', '%' . $role . '%')
                ->Where('email', 'LIKE', '%' . $email . '%')
                ->WhereNotIn('email', ['admin@app.local', 'manager@app.local', 'supervisor@app.local', 'johndoe@app.local'])
                ->get();

            return view('pages/admin/users/index', [
                'users' => $user,
                'rows' => $user->count(),
                'roles' => ['Admin', 'Supervisor', 'Manager', 'User', 'Non Actif'], 
                'request' => $request
            ]);
        }
    }

    public function create()
    {
        $user = new User();
        $user->fill([
            'name' => '',
            'email' => '',
            'password' => '',
            'role' => '',
        ]);
        return view('pages/admin/users/form', [
            'user' => $user,
            'roles' => ['Admin', 'Supervisor', 'Manager', 'User', 'Non Actif'], ['Admin', 'Supervisor', 'Manager', 'User'],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'email' => 'required',
                'email' => 'string',
                'email' => 'unique:users',
                'password' => 'required',
                'password' => 'min:4',
            ],
            [
                'name.required' => 'Champ obligatoire !',
                'email.required' => 'Champ obligatoire !',
                'email.string' => 'Le champ doit contenir des chaînes de caractères !',
                'email.unique' => 'Cet Email existe déjà !',
                'password.required' => 'Champ obligatoire !',
                'password.min' => 'Le champ doit contenir au moins 4 caractères !',
            ]
        );

        User::create([
            'name' => strtoupper($request->get('name')),
            'email' => $request->get('email'),
            'password' => $request->get('password'),
            'role' => $request->get('role'),
        ]);

        return to_route('admin.compte.create')->with('success', "Vous avez créé un compte !");
    }

    public function edit($id)
    {
        return view('pages/admin/users/form', [
            'user' => User::find(decrypt($id)),
            'roles' => ['Admin', 'Supervisor', 'Manager', 'User', 'Non Actif'], ['Admin', 'Supervisor', 'Manager', 'User'],
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $request->validate(
            [
                'name' => 'required',
                'email' => 'required',
                'email' => 'string',
                'password' => 'required',
                'password' => 'min:4',
            ],
            [
                'name.required' => 'Champ obligatoire !',
                'email.required' => 'Champ obligatoire !',
                'email.string' => 'Le champ doit contenir des chaînes de caractères !',
                'password.required' => 'Champ obligatoire !',
                'password.min' => 'Le champ doit contenir au moins 4 caractères !',
            ]
        );

        if ($user->password != $request->password) {
            $user->update([
                'name' => strtoupper($request->get('name')),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
                'role' => $request->get('role'),
            ]);
        } else {
            $user->update([
                'name' => strtoupper($request->get('name')),
                'email' => $request->get('email'),
                'role' => $request->get('role'),
            ]);
        }

        return to_route('admin.compte.edit', encrypt($user->id))->with('success', "La modification a été effectuée !");
    }

    public function destroy($id)
    {
        User::destroy($id);
        return to_route('admin.compte.index')->with('success', "La suppression a été effectuée !");
    }
}
