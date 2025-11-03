<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),

            ]
        );
        $admin->assignRole('Administrador');


        $usuario = User::firstOrCreate(
            ['email' => 'usuario@example.com'],
            [
                'name' => 'usuario ejemplo',
                'password' => Hash::make('password'),

            ]
        );
        $usuario->assignRole('Usuario');
    }
}
