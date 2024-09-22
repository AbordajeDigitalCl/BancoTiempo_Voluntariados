<?php

namespace App\Http\Controllers;

use App\Traits\Cruds;
use Symfony\Component\HttpFoundation\Request;

class TransaccionController extends Controller{

    use Cruds;
    
    public function find(Request $request){
        $result = $this->Obtener_transaccions($request, $use_for = '-', $offset = 0, $ammount = 20);
        return response()->json($result, 200);
    }

    public function edit(Request $request){
        $result = $this->Editar_transaccions($request);
        return response()->json($result, 200);
    }
}
