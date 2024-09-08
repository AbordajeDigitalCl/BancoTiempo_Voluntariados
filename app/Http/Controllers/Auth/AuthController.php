<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\Cruds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller{

    use Cruds;

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|present|email',
            'password' => 'required|string|present'
        ]);

        if($validator->fails())
            return response()->json(["message" => $validator->errors()->all()], 400);

        if(is_null($user = $this->findUserByEmail($request)))
            return response()->json(["message" => "Incorrect credentials"], 401);

        if (!Hash::check($request->password, $user->password))
            return response()->json(["message" => "Incorrect credentials"], 401);

        return response()->json([
            'token' => $user->createToken('api-token')->plainTextToken
        ]);
    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'Logged out'
        ]);
    }

}
