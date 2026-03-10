<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'email_verified_at' => now(),
            ]
        );

 
        User::updateOrCreate(
            ['email' => 'juan@test.com'],
            [
                'name' => 'Juan',
                'Apellido_Paterno' => 'Pérez',
                'Apellido_Materno' => 'Demo2',
                'Age' => '1990-01-15',
                'Telefono' => '912345678',
                'Direccion' => 'Calle Principal 123',
                'Ciudad' => 'Santiago',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'mariadmin@test.com'],
            [
                'name' => 'María',
                'Apellido_Paterno' => 'Admin',
                'Apellido_Materno' => 'Martínez',
                'Age' => '1992-03-20',
                'Telefono' => '923456789',
                'Direccion' => 'Avenida Central 456',
                'Ciudad' => 'Valparaíso',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Se crearon 3 usuarios de prueba exitosamente.');
    }
}

            

