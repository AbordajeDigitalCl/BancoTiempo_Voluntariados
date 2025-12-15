<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tipo_Usuario extends Model
{

    protected $fillable = [
        'id_usuario',
        "Nombre_Tipo_Usuario",
        "Etiqueta"
    ];

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }

}
