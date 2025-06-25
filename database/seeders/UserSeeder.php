<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Magasin;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        // Création de quelques magasins si besoin
        $magasins = [
            ['nom' => 'Boutique A', 'adresse' => 'Quartier A'],
            ['nom' => 'Boutique B', 'adresse' => 'Quartier A'],
            ['nom' => 'Boutique C', 'adresse' => 'Quartier A'],
        ];

        foreach ($magasins as $data) {
            Magasin::firstOrCreate($data);
        }
        
        $users = [
            [
                'name' => 'ADMINISTRATEUR',
                'email' => 'admin@app.local',
                'password' => Hash::make('1234'),
                'role' => 'Admin',
            ],
            [
                'name' => 'GESTIONNAIRE',
                'email' => 'manager@app.local',
                'password' => Hash::make('1234'),
                'role' => 'Manager',
            ],
            [
                'name' => 'SUPERVISEUR',
                'email' => 'supervisor@app.local',
                'password' => Hash::make('1234'),
                'role' => 'Supervisor',
            ],
            [
                'name' => 'JOHN DOE',
                'email' => 'johndoe@app.local',
                'password' => Hash::make('1234'),
                'role' => 'User',
            ],
        ];

        foreach ($users as $data) {
            $user = User::create($data);

            // Associer un ou plusieurs magasins à chaque utilisateur
            $magasinsIds = Magasin::inRandomOrder()->take(2)->pluck('id');
            $user->magasins()->attach($magasinsIds);
        }
        
    }
}
