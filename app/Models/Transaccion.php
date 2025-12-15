<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_usuario_origen',
        'id_usuario_destino',
        'Tipo_Transaccion'
    ];

    public function usuarioOrigen(){
        return $this->belongsTo(User::class, 'id_usuario_origen');
    }

    public function usuarioDestino(){
        return $this->belongsTo(User::class, 'id_usuario_destino');
    }
}
