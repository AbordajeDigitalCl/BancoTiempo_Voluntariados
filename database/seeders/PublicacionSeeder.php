<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Publicacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PublicacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Crea publicaciones de prueba (Ofertas y Demandas de Servicios y Productos)
     */
    public function run(): void
    {
        $jose = User::where('email', 'jose@test.com')->first();
        $juan = User::where('email', 'juan@test.com')->first();
        $maria = User::where('email', 'mariadmin@test.com')->first();

        if (!$jose || !$juan || !$maria) {
            $this->command->error('No se encontraron los usuarios. Asegúrate de ejecutar UserSeeder primero.');
            return;
        }


        $publicaciones = [
            [
                'id_usuario' => $jose->id,
                'Nombre' => 'Reparación de computadoras',
                'Descripcion' => 'Ofrezco servicio de reparación, mantenimiento e instalación de software',
                'Tipo_Publicacion' => 0, 
                'Tipo_Beneficio' => 0,  
                'visible' => 1,
                'Precio' => 30,
            ],
            [
                'id_usuario' => $jose->id,
                'Nombre' => 'Necesito Soporte técnico y mantenimiento de PC',
                'Descripcion' => 'Mi PC anda sufriendo de lentitudes y sobrecalentamientos frecuentes.',
                'Tipo_Publicacion' => 1, 
                'Tipo_Beneficio' => 0,   
                'visible' => 1,
                'Presupuesto' => 35,
            ],

            [
                'id_usuario' => $jose->id,
                'Nombre' => 'Pollo de plástico',
                'Descripcion' => 'Estrujalo y verás como se pone a hacer ruidos fuertes. Experto en molestar a la gente de cerca.',
                'Tipo_Publicacion' => 0, 
                'Tipo_Beneficio' => 1,  
                'visible' => 1,
                'Precio' => 20,
            ],

        
            [
                'id_usuario' => $juan->id,
                'Nombre' => 'Reparación de computadoras',
                'Descripcion' => 'Ofrezco servicio de reparación, mantenimiento e instalación de software',
                'Tipo_Publicacion' => 0,
                'Tipo_Beneficio' => 0,   
                'visible' => 1,
                'Precio' => 30,
            ],
            [
                'id_usuario' => $juan->id,
                'Nombre' => 'Reparación de grifo',
                'Descripcion' => 'Necesito un servicio de reparación para mi grifo del baño.',
                'Tipo_Publicacion' => 1, 
                'Tipo_Beneficio' => 0,  
                'visible' => 1,
                'Presupuesto' => 5,
            ],

            [
                'id_usuario' => $juan->id,
                'Nombre' => 'Bicicleta de montaña',
                'Descripcion' => 'Necesito una bicicleta en buen estado, para poder subir montañas.',
                'Tipo_Publicacion' => 1,
                'Tipo_Beneficio' => 1,   
                'visible' => 1,
                'Presupuesto' => 15,
            ],
        ];


        foreach ($publicaciones as $pub) {
            $publicacionId = DB::table('publicacions')->insertGetId([
                'id_usuario' => $pub['id_usuario'],
                'Nombre' => $pub['Nombre'],
                'Descripcion' => $pub['Descripcion'],
                'Tipo_Publicacion' => $pub['Tipo_Publicacion'],
                'Tipo_Beneficio' => $pub['Tipo_Beneficio'],
                'visible' => $pub['visible'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $tipoPublicacion = $pub['Tipo_Publicacion'];
            
            if ($tipoPublicacion === 0) {
                DB::table('ofertas')->insert([
                    'id_publicacion' => $publicacionId,
                    'Precio' => $pub['Precio'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('demandas')->insert([
                    'id_publicacion' => $publicacionId,
                    'Presupuesto' => $pub['Presupuesto'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Se crearon ' . count($publicaciones) . ' publicaciones de prueba exitosamente 3 para José.');
    }
}
