<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    // MODIFICADO por: Gloria Cornelio - 2026/05/19
    // Se cambió la tabla de advertisements a publicacions para unificar ambas tablas
    // La tabla advertisements queda obsoleta y fue removida de las migraciones
    protected $table = 'publicacions';

    protected $fillable = [
        'Nombre',        // campo obligatorio de publicacions
        'Descripcion',   // campo obligatorio de publicacions
        'Tipo_Publicacion',
        'Tipo_Beneficio', 
        'title',
        'description',
        'hours',
        'type',
        'mode',
        'status',
        'author',
// Se cambió user_id por id_usuario porque la tabla publicacions
        'id_usuario',
        'image_urls',
        'location_id',
        'location_lat',
        'location_lng',
        'location_address',
    ];

    protected $casts = [
        'image_urls' => 'array',
        'location_lat' => 'float',
        'location_lng' => 'float',
    ];
}