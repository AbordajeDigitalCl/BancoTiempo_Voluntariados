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

        //------------------------ PUBLICACIONES DE JOSE -----------------

        $publicaciones = [
            [
                'title' => 'Clases particulares de matemáticas',
                'Nombre' => 'Clases particulares de matemáticas',
                'Descripcion' => 'Ofrezco clases de matemáticas para estudiantes de enseñanza básica y media.',
                'Tipo_Publicacion' => 0, 'Tipo_Beneficio' => 0,
                'description' => 'Ofrezco clases de matemáticas para estudiantes de enseñanza básica y media.',
                'hours' => 1, 'type' => 'Servicio', 'mode' => 'Oferta',
                'status' => 'active', 'image' => 409,
                'lat' => -33.4489, 'lng' => -70.6693, 'address' => 'Santiago Centro, Chile',
            ],
            [
                'title' => 'Busco bicicleta usada',
                'Nombre' => 'Busco bicicleta usada',
                'Descripcion' => 'Busco una bicicleta en condiciones razonables para desplazarme diariamente.',
                'Tipo_Publicacion' => 1, 'Tipo_Beneficio' => 1,
                'description' => 'Busco una bicicleta en condiciones razonables para desplazarme diariamente.',
                'hours' => 1, 'type' => 'Producto', 'mode' => 'Demanda',
                'status' => 'active', 'image' => 410,
                'lat' => -33.4372, 'lng' => -70.6506, 'address' => 'Santiago Centro, Chile',
            ],
            [
                'title' => 'Reparación de computadores',
                'Nombre' => 'Reparación de computadores',
                'Descripcion' => 'Realizo mantenimiento, limpieza interna e instalación de programas en computadores.',
                'Tipo_Publicacion' => 0, 'Tipo_Beneficio' => 0,
                'description' => 'Realizo mantenimiento, limpieza interna e instalación de programas en computadores.',
                'hours' => 2, 'type' => 'Servicio', 'mode' => 'Oferta',
                'status' => 'active', 'image' => 411,
                'lat' => -33.4260, 'lng' => -70.6150, 'address' => 'Providencia, Santiago, Chile',
            ],
            [
                'title' => 'Vendo silla de escritorio',
                'Nombre' => 'Vendo silla de escritorio',
                'Descripcion' => 'Silla ergonómica usada, con altura regulable y ruedas en buen estado.',
                'Tipo_Publicacion' => 0, 'Tipo_Beneficio' => 1,
                'description' => 'Silla ergonómica usada, con altura regulable y ruedas en buen estado.',
                'hours' => 1, 'type' => 'Producto', 'mode' => 'Oferta',
                'status' => 'paused', 'image' => 412,
                'lat' => -33.4215, 'lng' => -70.6100, 'address' => 'Providencia, Santiago, Chile',
            ],
            [
                'title' => 'Busco clases de francés',
                'Nombre' => 'Busco clases de francés',
                'Descripcion' => 'Busco profesor de francés para practicar conversación desde nivel principiante.',
                'Tipo_Publicacion' => 1, 'Tipo_Beneficio' => 0,
                'description' => 'Busco profesor de francés para practicar conversación desde nivel principiante.',
                'hours' => 2, 'type' => 'Servicio', 'mode' => 'Demanda',
                'status' => 'active', 'image' => 413,
                'lat' => -33.4370, 'lng' => -70.6250, 'address' => 'Providencia, Santiago, Chile',
            ],
            [
                'title' => 'Diseño de páginas web',
                'Nombre' => 'Diseño de páginas web',
                'Descripcion' => 'Diseño páginas web sencillas y adaptables para pequeños negocios y emprendimientos.',
                'Tipo_Publicacion' => 0, 'Tipo_Beneficio' => 0,
                'description' => 'Diseño páginas web sencillas y adaptables para pequeños negocios y emprendimientos.',
                'hours' => 3, 'type' => 'Servicio', 'mode' => 'Oferta',
                'status' => 'finished', 'image' => 414,
                'lat' => -33.4550, 'lng' => -70.5800, 'address' => 'Ñuñoa, Santiago, Chile',
            ],
            [
                'title' => 'Vendo bicicleta de montaña',
                'Nombre' => 'Vendo bicicleta de montaña',
                'Descripcion' => 'Bicicleta de montaña con cambios Shimano, usada pero en buen estado.',
                'Tipo_Publicacion' => 0, 'Tipo_Beneficio' => 1,
                'description' => 'Bicicleta de montaña con cambios Shimano, usada pero en buen estado.',
                'hours' => 1, 'type' => 'Producto', 'mode' => 'Oferta',
                'status' => 'active', 'image' => 415,
                'lat' => -33.4750, 'lng' => -70.6200, 'address' => 'Macul, Santiago, Chile',
            ],
            [
                'title' => 'Busco reparación de bicicleta',
                'Nombre' => 'Busco reparación de bicicleta',
                'Descripcion' => 'Necesito ayuda para ajustar los frenos y los cambios de mi bicicleta.',
                'Tipo_Publicacion' => 1, 'Tipo_Beneficio' => 0,
                'description' => 'Necesito ayuda para ajustar los frenos y los cambios de mi bicicleta.',
                'hours' => 2, 'type' => 'Servicio', 'mode' => 'Demanda',
                'status' => 'active', 'image' => 416,
                'lat' => -33.4900, 'lng' => -70.6000, 'address' => 'La Florida, Santiago, Chile',
            ],
            [
                'title' => 'Vendo monitor de computadora',
                'Nombre' => 'Vendo monitor de computadora',
                'Descripcion' => 'Monitor de 24 pulgadas, resolución Full HD, funcionando perfectamente.',
                'Tipo_Publicacion' => 0, 'Tipo_Beneficio' => 1,
                'description' => 'Monitor de 24 pulgadas, resolución Full HD, funcionando perfectamente.',
                'hours' => 1, 'type' => 'Producto', 'mode' => 'Oferta',
                'status' => 'deleted', 'image' => 417,
                'lat' => -33.4100, 'lng' => -70.5700, 'address' => 'Las Condes, Santiago, Chile',
            ],
            [
                'title' => 'Clases de guitarra para principiantes',
                'Nombre' => 'Clases de guitarra para principiantes',
                'Descripcion' => 'Enseño acordes básicos, ritmos y canciones sencillas para comenzar a tocar guitarra.',
                'Tipo_Publicacion' => 0, 'Tipo_Beneficio' => 0,
                'description' => 'Enseño acordes básicos, ritmos y canciones sencillas para comenzar a tocar guitarra.',
                'hours' => 2, 'type' => 'Servicio', 'mode' => 'Oferta',
                'status' => 'active', 'image' => 418,
                'lat' => -33.4500, 'lng' => -70.6300, 'address' => 'Santiago Centro, Chile',
            ],
            [
                'title' => 'Busco escritorio para estudiar',
                'Nombre' => 'Busco escritorio para estudiar',
                'Descripcion' => 'Necesito un escritorio pequeño que pueda instalar en una habitación.',
                'Tipo_Publicacion' => 1, 'Tipo_Beneficio' => 1,
                'description' => 'Necesito un escritorio pequeño que pueda instalar en una habitación.',
                'hours' => 1, 'type' => 'Producto', 'mode' => 'Demanda',
                'status' => 'active', 'image' => 419,
                'lat' => -33.4300, 'lng' => -70.5900, 'address' => 'Providencia, Santiago, Chile',
            ],
            [
                'title' => 'Fotografía para eventos pequeños',
                'Nombre' => 'Fotografía para eventos pequeños',
                'Descripcion' => 'Ofrezco sesiones fotográficas para cumpleaños, reuniones familiares y celebraciones.',
                'Tipo_Publicacion' => 0, 'Tipo_Beneficio' => 0,
                'description' => 'Ofrezco sesiones fotográficas para cumpleaños, reuniones familiares y celebraciones.',
                'hours' => 2, 'type' => 'Servicio', 'mode' => 'Oferta',
                'status' => 'paused', 'image' => 420,
                'lat' => -33.4600, 'lng' => -70.5700, 'address' => 'Ñuñoa, Santiago, Chile',
            ],
            [
                'title' => 'Vendo libros de programación',
                'Nombre' => 'Vendo libros de programación',
                'Descripcion' => 'Colección de libros usados sobre programación, algoritmos y desarrollo web.',
                'Tipo_Publicacion' => 0, 'Tipo_Beneficio' => 1,
                'description' => 'Colección de libros usados sobre programación, algoritmos y desarrollo web.',
                'hours' => 1, 'type' => 'Producto', 'mode' => 'Oferta',
                'status' => 'active', 'image' => 421,
                'lat' => -33.4400, 'lng' => -70.6400, 'address' => 'Santiago Centro, Chile',
            ],
            [
                'title' => 'Busco ayuda con Excel',
                'Nombre' => 'Busco ayuda con Excel',
                'Descripcion' => 'Necesito aprender a utilizar fórmulas, tablas dinámicas y organizar datos en Excel.',
                'Tipo_Publicacion' => 1, 'Tipo_Beneficio' => 0,
                'description' => 'Necesito aprender a utilizar fórmulas, tablas dinámicas y organizar datos en Excel.',
                'hours' => 2, 'type' => 'Servicio', 'mode' => 'Demanda',
                'status' => 'finished', 'image' => 422,
                'lat' => -33.4200, 'lng' => -70.6000, 'address' => 'Providencia, Santiago, Chile',
            ],
            [
                'title' => 'Vendo lámpara de escritorio',
                'Nombre' => 'Vendo lámpara de escritorio',
                'Descripcion' => 'Lámpara LED de escritorio con intensidad regulable y conexión USB.',
                'Tipo_Publicacion' => 0, 'Tipo_Beneficio' => 1,
                'description' => 'Lámpara LED de escritorio con intensidad regulable y conexión USB.',
                'hours' => 1, 'type' => 'Producto', 'mode' => 'Oferta',
                'status' => 'active', 'image' => 423,
                'lat' => -33.4700, 'lng' => -70.5900, 'address' => 'Macul, Santiago, Chile',
            ],
            [
                'title' => 'Servicio de traducción inglés-español',
                'Nombre' => 'Servicio de traducción inglés-español',
                'Descripcion' => 'Realizo traducciones de textos generales, documentos y trabajos académicos.',
                'Tipo_Publicacion' => 0, 'Tipo_Beneficio' => 0,
                'description' => 'Realizo traducciones de textos generales, documentos y trabajos académicos.',
                'hours' => 1, 'type' => 'Servicio', 'mode' => 'Oferta',
                'status' => 'active', 'image' => 424,
                'lat' => -33.4350, 'lng' => -70.6100, 'address' => 'Providencia, Santiago, Chile',
            ],
            [
                'title' => 'Busco monitor para teletrabajo',
                'Nombre' => 'Busco monitor para teletrabajo',
                'Descripcion' => 'Busco un monitor de al menos 22 pulgadas para trabajar desde casa.',
                'Tipo_Publicacion' => 1, 'Tipo_Beneficio' => 1,
                'description' => 'Busco un monitor de al menos 22 pulgadas para trabajar desde casa.',
                'hours' => 1, 'type' => 'Producto', 'mode' => 'Demanda',
                'status' => 'active', 'image' => 425,
                'lat' => -33.4500, 'lng' => -70.5800, 'address' => 'Ñuñoa, Santiago, Chile',
            ],
            [
                'title' => 'Mantenimiento de jardines',
                'Nombre' => 'Mantenimiento de jardines',
                'Descripcion' => 'Realizo poda, limpieza y cuidado general de jardines y plantas domésticas.',
                'Tipo_Publicacion' => 0, 'Tipo_Beneficio' => 0,
                'description' => 'Realizo poda, limpieza y cuidado general de jardines y plantas domésticas.',
                'hours' => 2, 'type' => 'Servicio', 'mode' => 'Oferta',
                'status' => 'paused', 'image' => 426,
                'lat' => -33.5000, 'lng' => -70.6100, 'address' => 'La Florida, Santiago, Chile',
            ],
            [
                'title' => 'Vendo teclado mecánico',
                'Nombre' => 'Vendo teclado mecánico',
                'Descripcion' => 'Teclado mecánico USB, con iluminación RGB y todas sus teclas funcionando.',
                'Tipo_Publicacion' => 0, 'Tipo_Beneficio' => 1,
                'description' => 'Teclado mecánico USB, con iluminación RGB y todas sus teclas funcionando.',
                'hours' => 1, 'type' => 'Producto', 'mode' => 'Oferta',
                'status' => 'active', 'image' => 427,
                'lat' => -33.4250, 'lng' => -70.5900, 'address' => 'Providencia, Santiago, Chile',
            ],
            [
                'title' => 'Busco clases de guitarra',
                'Nombre' => 'Busco clases de guitarra',
                'Descripcion' => 'Busco clases individuales para aprender guitarra acústica desde cero.',
                'Tipo_Publicacion' => 1, 'Tipo_Beneficio' => 0,
                'description' => 'Busco clases individuales para aprender guitarra acústica desde cero.',
                'hours' => 2, 'type' => 'Servicio', 'mode' => 'Demanda',
                'status' => 'active', 'image' => 428,
                'lat' => -33.4550, 'lng' => -70.6100, 'address' => 'Ñuñoa, Santiago, Chile',
            ],
        ];

        foreach ($publicaciones as $p) {
            Advertisement::updateOrCreate(
                [
                    'title' => $p['title'],
                    'id_usuario' => $jose->id,
                ],
                [
                    'Nombre' => $p['Nombre'],
                    'Descripcion' => $p['Descripcion'],
                    'Tipo_Publicacion' => $p['Tipo_Publicacion'],
                    'Tipo_Beneficio' => $p['Tipo_Beneficio'],
                    'description' => $p['description'],
                    'hours' => $p['hours'],
                    'type' => $p['type'],
                    'mode' => $p['mode'],
                    'status' => $p['status'],
                    'author' => $jose->name,
                    'image_urls' => ['https://picsum.photos/' . $p['image']],
                    'location_lat' => $p['lat'],
                    'location_lng' => $p['lng'],
                    'location_address' => $p['address'],
                ]
            );
        }

    }
}