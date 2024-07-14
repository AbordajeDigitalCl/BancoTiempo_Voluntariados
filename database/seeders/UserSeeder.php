<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        User::create([
            'name' => $faker->name,
            'Apellido_Paterno' => $faker->lastName,
            'Apellido_Materno' => $faker->lastName,
            'Age' => $faker->date,
            'Telefono' => $faker->phoneNumber,
            'Direccion' => $faker->address,
            'Ciudad' => $faker->city,
            'email' => $faker->email,
            'password' => $faker->password
        ]);
    }
}
