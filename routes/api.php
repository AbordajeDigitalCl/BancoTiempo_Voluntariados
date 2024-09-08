<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TipoUsuario;
use App\Http\Controllers\UserController;
use App\Models\Tipo_Usuario;
use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\isNull;

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

/*** Se usarán para pruebas, no tocar por favor -JJ- ***/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/user/info', [
    UserController::class, 'getUserInfo'
])->middleware('auth:sanctum');

Route::post('/token/logout', [
    AuthController::class, 'logout'
])->middleware('auth:sanctum');

Route::post("/token/login", [
    AuthController::class, 'login'
]);

Route::get('/user/list',[
    UserController::class, 'Obtener_usuarios'
]);

Route::post('/user',[
    RegisteredUserController::class, 'storeApi'
]);

Route::post('/user/type', [
    TipoUsuario::class, 'edit'
]);
Route::put('/user/type', [
    TipoUsuario::class, 'edit'
])->middleware('auth:sanctum');


Route::post('editar',[TestController::class, 'editar'])->name('editar');
Route::put('editar',[TestController::class, 'editar'])->name('editar');
Route::delete('editar',[TestController::class, 'editar'])->name('editar');
Route::get('obtener', [TestController::class, 'obtener'])->name('obtener');
