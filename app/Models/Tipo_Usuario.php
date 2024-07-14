<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Tipo_Usuario extends Model
{
    use HasApiTokens, Notifiable, HasFactory;

    protected $fillable = [
        "id_usuario",
        "Nombre_Tipo_Usuario",
        "Etiqueta"
    ];
}
