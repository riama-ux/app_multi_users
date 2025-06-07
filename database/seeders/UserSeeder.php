<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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

        foreach ($users as $key => $user) {
            User::create($user);
        }
    }
}
