<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inter_Transa_Public extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_publicacion',
        'id_transaccion',
        'cantidad'
    ];

    public function publicacion(): BelongsTo{
        return $this->belongsTo(Publicacion::class);
    }
}
