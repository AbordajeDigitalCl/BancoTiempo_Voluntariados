<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publicacion extends Model
{
    use HasFactory;
//Cristóbal 03-03-2026
//Se agregó la línea tipo_beneficio meramente para que se pueda guardar el dato en la base de datos.
    protected $fillable = [
        'id_usuario',
        'Nombre',
        'Descripcion',
        'Tipo_Publicacion',
        'Tipo_Beneficio',
        'status'
    ];
}
