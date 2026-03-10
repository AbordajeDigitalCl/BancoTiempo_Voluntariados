<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'marcelo@test.com'],
            [
                'name' => 'Marcelo',
                'Apellido_Paterno' => 'Urrutia',
                'Apellido_Materno' => 'Test',
                'Age' => '2002-05-10',
                'Telefono' => '912345678',
                'Direccion' => 'La Florida',
                'Ciudad' => 'Santiago',
                'password' => Hash::make('12345678'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'jose@test.com'],
            [
                'name' => 'Jose',
                'Apellido_Paterno' => 'Perez',
                'Apellido_Materno' => 'Demo',
                'Age' => '2000-04-12',
                'Telefono' => '987654321',
                'Direccion' => 'Providencia',
                'Ciudad' => 'Santiago',
                'password' => Hash::make('12345678'),
            ]
        );
    }
}