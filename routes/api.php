<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Traits\Cruds;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('post', function (Request $request) {
    $result = $this->Obtener_transaccions($request, $use_for = '-', $offset = 0, $ammount = 20);
    // $result = $this->Editar_transaccions($request);
    // $result = $this->Obtener_inter__transa__publics($request, $use_for = '-', $offset = 0, $ammount = 20);
    // $result = $this->Editar_inter__transa__publics($request);
    // $result = $this->Obtener_proyectos($request, $use_for = '-', $offset = 0, $ammount = 20);
    // $result = $this->Editar_proyectos($request);
    // $result = $this->Obtener_voluntario($request, $use_for = 0, $offset = 0, $ammount = 20);
    // $result = $this->Editar_voluntario($request, $hard_delete = true);
    // $result = $this->Obtener_mensaje($request, $use_for = 0, $offset = 0, $ammount = 20);
    // $result = $this->Editar_mensaje($request);
    // $result = $this->Obtener_publicacions($request, $use_for = 0, $offset = 0, $ammount = 20);
    // $result = $this->Editar_publicacions($request);
    // $result = $this->Obtener_demandas($request, $use_for = 0, $offset = 0, $ammount = 20);
    // $result = $this->Editar_demandas($request);
    // $result = $this->Obtener_ofertas($request, $use_for = 0, $offset = 0, $ammount = 20);
    // $result = $this->Editar_ofertas($request);
    // $result = $this->Obtener_imagens($request, $use_for = 0, $offset = 0, $ammount = 20);
    // $result = $this->Editar_imagens($request);
    // $result = $this->Obtener_inter__calif__publics($request, $use_for = 0, $offset = 0, $ammount = 20);
    // $result = $this->Editar_inter__calif__publics($request);
    // $result = $this->Obtener_calificacions($request, $use_for = 0, $offset = 0, $ammount = 20);
    // $result = $this->Editar_calificacions($request);
    // $result = $this->Obtener_tipo__usuarios($request, $use_for = 0, $offset = 0, $ammount = 20);
    // $result = $this->Editar_tipo__usuarios($request);
    // $result = $this->Obtener_usuarios($request, $use_for = '-', $offset = 0, $ammount = 20);
    // $result = $this->Editar_usuarios($request, $hard_delete = false);
    // $result = $this->Revisar_Acceso(1, 1);
    return response()->json($result, 200);
})->name('get');
