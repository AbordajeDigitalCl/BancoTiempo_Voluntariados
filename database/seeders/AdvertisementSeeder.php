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
        $juan = User::where('email', 'juan@test.com')->first();
        $gloria = User::where('email', 'gloria@test.com')->first();

        if (!$marcelo || !$jose || !$juan || !$gloria) {
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

        //Datos agregados por Eduardo


        Advertisement::updateOrCreate(
            ['title' => 'Busco bicicleta usada', 'id_usuario' => $jose->id],
            [
                'Nombre' => 'Busco bicicleta usada',
                'Descripcion' => 'Estoy buscando una bicicleta usada en buen estado.',
                'Tipo_Publicacion' => 1, // Demanda
                'Tipo_Beneficio' => 1, // Producto
                'description' => 'Estoy buscando una bicicleta usada en buen estado.',
                'hours' => 2,
                'type' => 'Producto',
                'mode' => 'Demanda',
                'status' => 'active',
                'author' => $jose->name,
                'image_urls' => ['https://picsum.photos/410'],
                'location_lat' => -33.4372,
                'location_lng' => -70.6506,
                'location_address' => 'Recoleta, Santiago, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Reparación de computadores', 'id_usuario' => $juan->id],
            [
                'Nombre' => 'Reparación de computadores',
                'Descripcion' => 'Ofrezco reparación, mantenimiento e instalación de software.',
                'Tipo_Publicacion' => 0, // Oferta
                'Tipo_Beneficio' => 0, // Servicio
                'description' => 'Ofrezco reparación, mantenimiento e instalación de software.',
                'hours' => 3,
                'type' => 'Servicio',
                'mode' => 'Oferta',
                'status' => 'active',
                'author' => $juan->name,
                'image_urls' => ['https://picsum.photos/411'],
                'location_lat' => -33.4417,
                'location_lng' => -70.6506,
                'location_address' => 'Providencia, Santiago, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Vendo escritorio de madera', 'id_usuario' => $marcelo->id],
            [
                'Nombre' => 'Vendo escritorio de madera',
                'Descripcion' => 'Escritorio de madera usado en excelentes condiciones.',
                'Tipo_Publicacion' => 0,
                'Tipo_Beneficio' => 1,
                'description' => 'Escritorio de madera usado en excelentes condiciones.',
                'hours' => 2,
                'type' => 'Producto',
                'mode' => 'Oferta',
                'status' => 'active',
                'author' => $marcelo->name,
                'image_urls' => ['https://picsum.photos/412'],
                'location_lat' => -33.4462,
                'location_lng' => -70.6506,
                'location_address' => 'Santiago Centro, Santiago, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Busco profesor de matemáticas', 'id_usuario' => $gloria->id],
            [
                'Nombre' => 'Busco profesor de matemáticas',
                'Descripcion' => 'Necesito clases particulares de matemáticas para nivel universitario.',
                'Tipo_Publicacion' => 1,
                'Tipo_Beneficio' => 0,
                'description' => 'Necesito clases particulares de matemáticas para nivel universitario.',
                'hours' => 2,
                'type' => 'Servicio',
                'mode' => 'Demanda',
                'status' => 'active',
                'author' => $gloria->name,
                'image_urls' => ['https://picsum.photos/413'],
                'location_lat' => -33.4462,
                'location_lng' => -70.6636,
                'location_address' => 'Providencia, Santiago, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Busco monitor para PC', 'id_usuario' => $jose->id],
            [
                'Nombre' => 'Busco monitor para PC',
                'Descripcion' => 'Necesito un monitor de segunda mano que funcione correctamente.',
                'Tipo_Publicacion' => 1,
                'Tipo_Beneficio' => 1,
                'description' => 'Necesito un monitor de segunda mano que funcione correctamente.',
                'hours' => 2,
                'type' => 'Producto',
                'mode' => 'Demanda',
                'status' => 'active',
                'author' => $jose->name,
                'image_urls' => ['https://picsum.photos/414'],
                'location_lat' => -33.4642,
                'location_lng' => -70.6506,
                'location_address' => 'Ñuñoa, Santiago, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Servicio de jardinería', 'id_usuario' => $juan->id],
            [
                'Nombre' => 'Servicio de jardinería',
                'Descripcion' => 'Ofrezco mantenimiento de jardines, poda y cuidado de plantas.',
                'Tipo_Publicacion' => 0,
                'Tipo_Beneficio' => 0,
                'description' => 'Ofrezco mantenimiento de jardines, poda y cuidado de plantas.',
                'hours' => 4,
                'type' => 'Servicio',
                'mode' => 'Oferta',
                'status' => 'active',
                'author' => $juan->name,
                'image_urls' => ['https://picsum.photos/415'],
                'location_lat' => -33.4282,
                'location_lng' => -70.6506,
                'location_address' => 'Recoleta, Santiago, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Vendo bicicleta de montaña', 'id_usuario' => $marcelo->id],
            [
                'Nombre' => 'Vendo bicicleta de montaña',
                'Descripcion' => 'Bicicleta de montaña usada, en buen estado y lista para utilizar.',
                'Tipo_Publicacion' => 0,
                'Tipo_Beneficio' => 1,
                'description' => 'Bicicleta de montaña usada, en buen estado y lista para utilizar.',
                'hours' => 2,
                'type' => 'Producto',
                'mode' => 'Oferta',
                'status' => 'active',
                'author' => $marcelo->name,
                'image_urls' => ['https://picsum.photos/416'],
                'location_lat' => -33.4372,
                'location_lng' => -70.7406,
                'location_address' => 'Estación Central, Santiago, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Busco electricista', 'id_usuario' => $gloria->id],
            [
                'Nombre' => 'Busco electricista',
                'Descripcion' => 'Necesito reparar una instalación eléctrica en mi departamento.',
                'Tipo_Publicacion' => 1,
                'Tipo_Beneficio' => 0,
                'description' => 'Necesito reparar una instalación eléctrica en mi departamento.',
                'hours' => 3,
                'type' => 'Servicio',
                'mode' => 'Demanda',
                'status' => 'active',
                'author' => $gloria->name,
                'image_urls' => ['https://picsum.photos/417'],
                'location_lat' => -33.5001,
                'location_lng' => -70.6506,
                'location_address' => 'La Florida, Santiago, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Reparación de electrodomésticos', 'id_usuario' => $jose->id],
            [
                'Nombre' => 'Reparación de electrodomésticos',
                'Descripcion' => 'Ofrezco reparación de lavadoras, refrigeradores y otros electrodomésticos.',
                'Tipo_Publicacion' => 0,
                'Tipo_Beneficio' => 0,
                'description' => 'Ofrezco reparación de lavadoras, refrigeradores y otros electrodomésticos.',
                'hours' => 5,
                'type' => 'Servicio',
                'mode' => 'Oferta',
                'status' => 'active',
                'author' => $jose->name,
                'image_urls' => ['https://picsum.photos/418'],
                'location_lat' => -33.4822,
                'location_lng' => -70.6506,
                'location_address' => 'San Miguel, Santiago, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Busco guitarra acústica', 'id_usuario' => $juan->id],
            [
                'Nombre' => 'Busco guitarra acústica',
                'Descripcion' => 'Quiero comprar una guitarra acústica usada para aprender a tocar.',
                'Tipo_Publicacion' => 1,
                'Tipo_Beneficio' => 1,
                'description' => 'Quiero comprar una guitarra acústica usada para aprender a tocar.',
                'hours' => 2,
                'type' => 'Producto',
                'mode' => 'Demanda',
                'status' => 'active',
                'author' => $juan->name,
                'image_urls' => ['https://picsum.photos/419'],
                'location_lat' => -33.5092,
                'location_lng' => -70.7046,
                'location_address' => 'Maipú, Santiago, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Clases particulares de inglés', 'id_usuario' => $marcelo->id],
            [
                'Nombre' => 'Clases particulares de inglés',
                'Descripcion' => 'Ofrezco clases de inglés para principiantes y estudiantes intermedios.',
                'Tipo_Publicacion' => 0,
                'Tipo_Beneficio' => 0,
                'description' => 'Ofrezco clases de inglés para principiantes y estudiantes intermedios.',
                'hours' => 2,
                'type' => 'Servicio',
                'mode' => 'Oferta',
                'status' => 'active',
                'author' => $marcelo->name,
                'image_urls' => ['https://picsum.photos/420'],
                'location_lat' => -33.5451,
                'location_lng' => -70.5166,
                'location_address' => 'Peñalolén, Santiago, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Busco muebles para departamento', 'id_usuario' => $gloria->id],
            [
                'Nombre' => 'Busco muebles para departamento',
                'Descripcion' => 'Necesito muebles usados para amoblar mi nuevo departamento.',
                'Tipo_Publicacion' => 1,
                'Tipo_Beneficio' => 1,
                'description' => 'Necesito muebles usados para amoblar mi nuevo departamento.',
                'hours' => 2,
                'type' => 'Producto',
                'mode' => 'Demanda',
                'status' => 'active',
                'author' => $gloria->name,
                'image_urls' => ['https://picsum.photos/421'],
                'location_lat' => -33.4372,
                'location_lng' => -70.8766,
                'location_address' => 'Pudahuel, Santiago, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Vendo herramientas de carpintería', 'id_usuario' => $jose->id],
            [
                'Nombre' => 'Vendo herramientas de carpintería',
                'Descripcion' => 'Vendo herramientas manuales y eléctricas para trabajos de madera.',
                'Tipo_Publicacion' => 0,
                'Tipo_Beneficio' => 1,
                'description' => 'Vendo herramientas manuales y eléctricas para trabajos de madera.',
                'hours' => 2,
                'type' => 'Producto',
                'mode' => 'Oferta',
                'status' => 'active',
                'author' => $jose->name,
                'image_urls' => ['https://picsum.photos/422'],
                'location_lat' => -33.6172,
                'location_lng' => -70.6506,
                'location_address' => 'Puente Alto, Santiago, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Necesito reparación de lavadora', 'id_usuario' => $juan->id],
            [
                'Nombre' => 'Necesito reparación de lavadora',
                'Descripcion' => 'Busco un técnico especializado en reparación de lavadoras.',
                'Tipo_Publicacion' => 1,
                'Tipo_Beneficio' => 0,
                'description' => 'Busco un técnico especializado en reparación de lavadoras.',
                'hours' => 3,
                'type' => 'Servicio',
                'mode' => 'Demanda',
                'status' => 'active',
                'author' => $juan->name,
                'image_urls' => ['https://picsum.photos/423'],
                'location_lat' => -33.5722,
                'location_lng' => -70.4706,
                'location_address' => 'La Reina, Santiago, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Vendo teléfono celular', 'id_usuario' => $marcelo->id],
            [
                'Nombre' => 'Vendo teléfono celular',
                'Descripcion' => 'Celular usado en buen estado, con batería y pantalla funcionando perfectamente.',
                'Tipo_Publicacion' => 0,
                'Tipo_Beneficio' => 1,
                'description' => 'Celular usado en buen estado, con batería y pantalla funcionando perfectamente.',
                'hours' => 2,
                'type' => 'Producto',
                'mode' => 'Oferta',
                'status' => 'active',
                'author' => $marcelo->name,
                'image_urls' => ['https://picsum.photos/424'],
                'location_lat' => -33.6172,
                'location_lng' => -70.3806,
                'location_address' => 'San Bernardo, Santiago, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Busco clases de guitarra', 'id_usuario' => $gloria->id],
            [
                'Nombre' => 'Busco clases de guitarra',
                'Descripcion' => 'Quiero aprender guitarra desde cero y necesito un profesor particular.',
                'Tipo_Publicacion' => 1,
                'Tipo_Beneficio' => 0,
                'description' => 'Quiero aprender guitarra desde cero y necesito un profesor particular.',
                'hours' => 2,
                'type' => 'Servicio',
                'mode' => 'Demanda',
                'status' => 'active',
                'author' => $gloria->name,
                'image_urls' => ['https://picsum.photos/425'],
                'location_lat' => -33.1672,
                'location_lng' => -70.6506,
                'location_address' => 'Colina, Santiago, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Servicio de pintura de interiores', 'id_usuario' => $jose->id],
            [
                'Nombre' => 'Servicio de pintura de interiores',
                'Descripcion' => 'Ofrezco servicios de pintura para casas y departamentos.',
                'Tipo_Publicacion' => 0,
                'Tipo_Beneficio' => 0,
                'description' => 'Ofrezco servicios de pintura para casas y departamentos.',
                'hours' => 4,
                'type' => 'Servicio',
                'mode' => 'Oferta',
                'status' => 'active',
                'author' => $jose->name,
                'image_urls' => ['https://picsum.photos/426'],
                'location_lat' => -33.7972,
                'location_lng' => -70.6506,
                'location_address' => 'Buin, Santiago, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Busco refrigerador usado', 'id_usuario' => $juan->id],
            [
                'Nombre' => 'Busco refrigerador usado',
                'Descripcion' => 'Necesito un refrigerador económico de segunda mano y en buen estado.',
                'Tipo_Publicacion' => 1,
                'Tipo_Beneficio' => 1,
                'description' => 'Necesito un refrigerador económico de segunda mano y en buen estado.',
                'hours' => 2,
                'type' => 'Producto',
                'mode' => 'Demanda',
                'status' => 'active',
                'author' => $juan->name,
                'image_urls' => ['https://picsum.photos/427'],
                'location_lat' => -33.4372,
                'location_lng' => -71.1906,
                'location_address' => 'Melipilla, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Vendo mesa de comedor', 'id_usuario' => $marcelo->id],
            [
                'Nombre' => 'Vendo mesa de comedor',
                'Descripcion' => 'Mesa de comedor de madera para seis personas, usada y bien conservada.',
                'Tipo_Publicacion' => 0,
                'Tipo_Beneficio' => 1,
                'description' => 'Mesa de comedor de madera para seis personas, usada y bien conservada.',
                'hours' => 2,
                'type' => 'Producto',
                'mode' => 'Oferta',
                'status' => 'active',
                'author' => $marcelo->name,
                'image_urls' => ['https://picsum.photos/428'],
                'location_lat' => -33.8872,
                'location_lng' => -70.6506,
                'location_address' => 'Rancagua, Chile',
            ]
        );

        Advertisement::updateOrCreate(
            ['title' => 'Busco servicio de mudanza', 'id_usuario' => $gloria->id],
            [
                'Nombre' => 'Busco servicio de mudanza',
                'Descripcion' => 'Necesito ayuda profesional para realizar una mudanza.',
                'Tipo_Publicacion' => 1,
                'Tipo_Beneficio' => 0,
                'description' => 'Necesito ayuda profesional para realizar una mudanza.',
                'hours' => 3,
                'type' => 'Servicio',
                'mode' => 'Demanda',
                'status' => 'active',
                'author' => $gloria->name,
                'image_urls' => ['https://picsum.photos/429'],
                'location_lat' => -33.4372,
                'location_lng' => -71.2906,
                'location_address' => 'San Antonio, Chile',
            ]
        );

    }
}