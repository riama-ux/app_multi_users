<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\Magasin;


class CompteController extends Controller
{
    public function index()
    {
        return view('pages/admin/users/index', [
        'users' => User::with('magasins') // ✅ charge la relation magasins
                    ->whereNotIn('email', [
                        'admin@app.local',
                        'manager@app.local',
                        'supervisor@app.local',
                        'johndoe@app.local'
                    ])
                    ->orderByDesc('id')
                    ->paginate(20),
        'rows' => User::count() - 4,
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
            'roles' => ['Admin', 'Supervisor', 'Manager', 'User', 'Non Actif'],
            'magasins' => Magasin::all(), // ✅ Liste des magasins pour le formulaire
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
                'magasins' => 'required|array|min:1',
            ],
            [
                'name.required' => 'Champ obligatoire !',
                'email.required' => 'Champ obligatoire !',
                'email.string' => 'Le champ doit contenir des chaînes de caractères !',
                'email.unique' => 'Cet Email existe déjà !',
                'password.required' => 'Champ obligatoire !',
                'password.min' => 'Le champ doit contenir au moins 4 caractères !',
                'magasins.required' => 'Champ obligatoire !',
            ]
        );

        $user = User::create([
            'name' => strtoupper($request->get('name')),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')), // 🔐 ne pas oublier de hasher le mot de passe !
            'role' => $request->get('role'),
        ]);

        $user->magasins()->attach($request->magasins); // ✅ maintenant $user existe bien


        return to_route('admin.compte.index')->with('success', "Vous avez créé un compte !");
    }

    public function edit($id)
    {
        return view('pages/admin/users/form', [
            'user' => User::find(decrypt($id)),
            'roles' => ['Admin', 'Supervisor', 'Manager', 'User', 'Non Actif'], ['Admin', 'Supervisor', 'Manager', 'User'],
            'magasins' => Magasin::all(), // ✅ Liste des magasins pour le formulaire
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id); // 🔐 plus sûr que find()

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'magasins' => 'required|array|min:1',
            'role' => 'required|in:Admin,Manager,Supervisor,User,Non Actif',
            'password' => 'nullable|min:4', // ✅ le mot de passe est facultatif
        ], [
            'name.required' => 'Champ obligatoire !',
            'email.required' => 'Champ obligatoire !',
            'email.email' => 'Format d’email invalide.',
            'magasins.required' => 'Attribuez au moins un magasin.',
            'role.required' => 'Le rôle est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 4 caractères.',
        ]);

        $updateData = [
            'name' => strtoupper($request->name),
            'email' => $request->email,
            'role' => $request->role,
        ];

        // ✅ Mise à jour du mot de passe seulement s’il est rempli
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // ✅ Mise à jour des magasins liés
        $user->magasins()->sync($request->magasins);

        return to_route('admin.compte.edit', encrypt($user->id))->with('success', "La modification a été effectuée !");
    }


    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return to_route('admin.compte.index')->with('error', "Utilisateur introuvable.");
        }

        // Supprimer les liens avec les magasins (table pivot)
        $user->magasins()->detach();

        $user->delete();

        return to_route('admin.compte.index')->with('success', "La suppression a été effectuée !");
    }

}
