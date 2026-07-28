<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Advertisement;
use App\Models\User;

class AdvertisementSeeder extends Seeder
{
    public function run(): void
    {
        $marcelo = User::where('email', 'marcelo@test.com')->first();
        $jose = User::where('email', 'jose@test.com')->first();

        if (!$marcelo || !$jose) {
            return;
        }

        // Se cambió user_id por id_usuario porque el modelo Advertisement
        // ahora apunta a la tabla publicacions que usa id_usuario
        // Se agregaron Nombre, Descripcion, Tipo_Publicacion y Tipo_Beneficio
        // que son campos obligatorios de publicacions

        Advertisement::updateOrCreate(
            ['title' => 'Reparación básica de computadores', 'id_usuario' => $marcelo->id],
            [
                'Nombre' => 'Reparación básica de computadores',
                'Descripcion' => 'Ofrezco limpieza, revisión y ayuda con problemas simples de software.',
                'Tipo_Publicacion' => 0, // Oferta
                'Tipo_Beneficio' => 0,   // Servicio
                'description' => 'Ofrezco limpieza, revisión y ayuda con problemas simples de software.',
                'hours' => 2,
                'type' => 'Servicio',
                'mode' => 'Oferta',
                'status' => 'active',
                'author' => $marcelo->name,
                'image_urls' => ['https://picsum.photos/405'],
                'location_lat' => -33.5227,
                'location_lng' => -70.5984,
                'location_address' => 'La Florida, Santiago, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Clases de matemáticas', 'id_usuario' => $marcelo->id],
            [
                'Nombre' => 'Clases de matemáticas',
                'Descripcion' => 'Apoyo para estudiantes de enseñanza básica y media.',
                'Tipo_Publicacion' => 0, // Oferta
                'Tipo_Beneficio' => 0,   // Servicio
                'description' => 'Apoyo para estudiantes de enseñanza básica y media.',
                'hours' => 3,
                'type' => 'Servicio',
                'mode' => 'Oferta',
                'status' => 'active',
                'author' => $marcelo->name,
                'image_urls' => ['https://picsum.photos/406'],
                'location_lat' => -33.4263,
                'location_lng' => -70.6112,
                'location_address' => 'Providencia, Santiago, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Busco ayuda para mudanza', 'id_usuario' => $marcelo->id],
            [
                'Nombre' => 'Busco ayuda para mudanza',
                'Descripcion' => 'Necesito ayuda para mover cajas y muebles pequeños.',
                'Tipo_Publicacion' => 1, // Demanda
                'Tipo_Beneficio' => 0,   // Servicio
                'description' => 'Necesito ayuda para mover cajas y muebles pequeños.',
                'hours' => 4,
                'type' => 'Servicio',
                'mode' => 'Demanda',
                'status' => 'active',
                'author' => $marcelo->name,
                'image_urls' => ['https://picsum.photos/407'],
                'location_lat' => -33.4489,
                'location_lng' => -70.6693,
                'location_address' => 'Santiago Centro, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Vendo escritorio usado', 'id_usuario' => $jose->id],
            [
                'Nombre' => 'Vendo escritorio usado',
                'Descripcion' => 'Escritorio en buen estado, ideal para estudiar.',
                'Tipo_Publicacion' => 0, // Oferta
                'Tipo_Beneficio' => 1,   // Producto
                'description' => 'Escritorio en buen estado, ideal para estudiar.',
                'hours' => 1,
                'type' => 'Producto',
                'mode' => 'Oferta',
                'status' => 'active',
                'author' => $jose->name,
                'image_urls' => ['https://picsum.photos/408'],
                'location_lat' => -33.4569,
                'location_lng' => -70.5979,
                'location_address' => 'Ñuñoa, Santiago, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Busco bicicleta usada', 'id_usuario' => $jose->id],
            [
                'Nombre' => 'Busco bicicleta usada',
                'Descripcion' => 'Estoy buscando una bicicleta usada en buen estado.',
                'Tipo_Publicacion' => 1, // Demanda
                'Tipo_Beneficio' => 1,   // Producto
                'description' => 'Estoy buscando una bicicleta usada en buen estado.',
                'hours' => 2,
                'type' => 'Producto',
                'mode' => 'Demanda',
                'status' => 'active',
                'author' => $jose->name,
                'image_urls' => ['https://picsum.photos/409'],
                'location_lat' => -33.4372,
                'location_lng' => -70.6506,
                'location_address' => 'Recoleta, Santiago, Chile',
            ]
        );
    }
}