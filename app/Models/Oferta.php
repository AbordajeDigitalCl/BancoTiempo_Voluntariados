<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Oferta extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_publicacion',
        'Precio',
    ];

    public function publicacion(): BelongsTo
    {
        return $this->belongsTo(Publicacion::class);
    }
}
