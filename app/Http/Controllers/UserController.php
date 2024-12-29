<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\Cruds;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

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

    /**
     * Funcion para actualizar información de usuario
     * Valida la información del usuario y el permiso (admin y same-user)
     *
     * @param Request $request
     * @return Response
     */
    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|present',
            'name' => 'required|string|max:255',
            'Apellido_Paterno' => 'required|string|max:255',
            'Apellido_Materno' => 'required|string|max:255',
            'Age' => 'required|string|max:255',
            'Telefono' => 'required|string|max:15',
            'Direccion' => 'required|string|max:255',
            'Ciudad' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255',
        ]);
        LOG::info("MAIL!!!!!!1");
        LOG::info($request->email);
        if($validator->fails()){
            LOG::info("user info validation failed");
            return response()->json(["message" => $validator->errors()->all()], 400);
        }
        if(Gate::allows('admin-action', $request->user()) || Gate::allows('same-user', $request->id)){
            LOG::info("update user info");
            return response()->json($this->saveUser($request), 200);
        }
        LOG::info("Neither admin nor same user");
        return response()->json(["message" => "Access denied"], 403);
    }

    /**
     * Funcion para actualizar contraseña del usuario
     * Valida la información del usuario y el permiso (admin y same-user)
     *
     * @param Request $request
     * @return Response
     */
    public function updatePassword(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|present',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        if($validator->fails()){
            LOG::info("user info validation failed");
            return response()->json(["message" => $validator->errors()->all()], 400);
        }
        if(Gate::allows('admin-action', $request->user()) || Gate::allows('same-user', $request->id)){
            LOG::info("update user password");
            return response()->json($this->updatePassword($request), 200);
        }
        LOG::info("Neither admin nor same user");
        return response()->json(["message" => "Access denied"], 403);
    }

}
