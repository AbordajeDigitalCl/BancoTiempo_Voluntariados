<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function login(Request $request)
    {
      $this->validateLogin($request);
      if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json([
          'message' => 'Unauthorized'
        ], 401);
      }
      return response()->json([
        'token' => $request->user()->createToken($request->device)->plainTextToken,
        'message' => 'Success'
      ]);
    }

    public function validateLogin(Request $request)
    {
      return $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device' => 'required'
      ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
