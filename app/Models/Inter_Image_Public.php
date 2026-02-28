<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inter_Image_Public extends Model
{
    use HasFactory;

    protected $table = 'inter__imagen__public';

    protected $fillable = [
        'id_publicacion',
        'id_imagen',
    ];

    public function publicacion(): BelongsTo{
        return $this->belongsTo(Publicacion::class);
    }
       
}
