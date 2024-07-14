<?php

namespace App\Http\Controllers;

use App\Traits\Cruds;
use Symfony\Component\HttpFoundation\Request;

class TipoUsuario extends Controller{

    use Cruds;

    public function find(Request $request){
        $result = $this->Obtener_tipo__usuarios($request, $use_for = 0, $offset = 0, $ammount = 20);
        return response()->json($result, 200);

    }

    public function edit(Request $request){
        $result = $this->Editar_tipo__usuarios($request);
        return response()->json($result, 200);
    } 

}
