<?php

namespace App\Enums;

enum TipoPublicacion: int
{

    case PRODUCTO = 0;
    case SERVICIO = 1;

    public function label() : string
    {
        return match($this){
            self::PRODUCTO => 'producto',
            self::SERVICIO => 'servicio',
        };
    }

}
