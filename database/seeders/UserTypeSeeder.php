<?php

namespace Database\Seeders;

use App\Models\Tipo_Usuario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tipo_Usuario::create([
            "id_usuario" => 1,
            "Nombre_Tipo_Usuario" => "cliente",
            "Etiqueta" => 1
        ]);
    }
}
