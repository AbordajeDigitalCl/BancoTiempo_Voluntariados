<?php

namespace App\Http\Controllers;

use App\Traits\Cruds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\isNull;

class UserController extends Controller{

    use Cruds;

    public function find(Request $request){

        $result = $this->Obtener_usuarios($request, $use_for = '-', $offset = 0, $ammount = 20);
        return response()->json($result, 200);
    }

    public function edit(Request $request){
        $result = $this->Editar_usuarios($request, $hard_delete = false);
        return response()->json($result, 200);
    }

    public function findByEmail(Request $request)
    {
        return $this->findUserByEmail($request);
    }

    public function getUserInfo(Request $request)
    {
        return $request->user();
    }

}
