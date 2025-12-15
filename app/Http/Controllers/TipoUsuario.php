<?php

namespace App\Http\Controllers;

use App\Traits\Cruds;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class TipoUsuario extends Controller{

    use Cruds;

    public function find(Request $request){
        $result = $this->Obtener_tipo__usuarios($request, $use_for = 0, $offset = 0, $ammount = 20);
        return response()->json($result, 200);
    }

    public function validateRole(Request $request){
        $result = $this->Obtener_tipo__usuarios($request, $use_for = 0, $offset = 0, $ammount = 20);
        return response()->json($result, 200);
    }

    public function edit(Request $request){
        if(!Gate::allows('admin-action', $request->user()))
            return response()->json(["message" => "Forbidden!"], 403);
        $result = $this->Editar_tipo__usuarios($request);
        return response()->json($result, 200);
    }

}
