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

        Advertisement::updateOrCreate(
            ['title' => 'Reparación básica de computadores', 'user_id' => $marcelo->id],
            [
                'description' => 'Ofrezco limpieza, revisión y ayuda con problemas simples de software.',
                'hours' => 2,
                'type' => 'Servicio',
                'mode' => 'Oferta',
                'status' => 'active',
                'author' => $marcelo->name,
                'image_urls' => ['https://picsum.photos/405'],
                'location_id' => null,
                'location_lat' => -33.5227,
                'location_lng' => -70.5984,
                'location_address' => 'La Florida, Santiago, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Clases de matemáticas', 'user_id' => $marcelo->id],
            [
                'description' => 'Apoyo para estudiantes de enseñanza básica y media. Ayuda con tareas, ejercicios y preparación para pruebas.',
                'hours' => 3,
                'type' => 'Servicio',
                'mode' => 'Oferta',
                'status' => 'active',
                'author' => $marcelo->name,
                'image_urls' => ['https://picsum.photos/406'],
                'location_id' => null,
                'location_lat' => -33.4263,
                'location_lng' => -70.6112,
                'location_address' => 'Providencia, Santiago, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Busco ayuda para mudanza', 'user_id' => $marcelo->id],
            [
                'description' => 'Necesito ayuda para mover cajas y muebles pequeños durante una mudanza dentro de Santiago.',
                'hours' => 4,
                'type' => 'Servicio',
                'mode' => 'Demanda',
                'status' => 'active',
                'author' => $marcelo->name,
                'image_urls' => ['https://picsum.photos/407'],
                'location_id' => null,
                'location_lat' => -33.4489,
                'location_lng' => -70.6693,
                'location_address' => 'Santiago Centro, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Vendo escritorio usado', 'user_id' => $jose->id],
            [
                'description' => 'Escritorio en buen estado, ideal para estudiar o trabajar desde casa.',
                'hours' => 1,
                'type' => 'Producto',
                'mode' => 'Oferta',
                'status' => 'active',
                'author' => $jose->name,
                'image_urls' => ['https://picsum.photos/408'],
                'location_id' => null,
                'location_lat' => -33.4569,
                'location_lng' => -70.5979,
                'location_address' => 'Ñuñoa, Santiago, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Busco bicicleta usada', 'user_id' => $jose->id],
            [
                'description' => 'Estoy buscando una bicicleta usada en buen estado para moverme por la ciudad.',
                'hours' => 2,
                'type' => 'Producto',
                'mode' => 'Demanda',
                'status' => 'active',
                'author' => $jose->name,
                'image_urls' => ['https://picsum.photos/409'],
                'location_id' => null,
                'location_lat' => -33.4372,
                'location_lng' => -70.6506,
                'location_address' => 'Recoleta, Santiago, Chile',
            ]
        );
    }
}