<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\PublicacionesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestControllerCris;
use App\Http\Controllers\TipoUsuario;
use App\Http\Controllers\TransaccionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FileUploadController;
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
/** Test info de usuario */
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

Route::put('/user/type', [
    TipoUsuario::class, 'edit'
])->middleware('auth:sanctum');

Route::get('/post', [
    PublicacionesController::class, 'find'
])->middleware('auth:sanctum');

Route::post('/post', [
    TestControllerCris::class, 'crearPublicacion'
])->middleware('auth:sanctum');

Route::get('/oferta/{ofertaId}', [
    PublicacionesController::class, 'findOferta'
])->middleware('auth:sanctum');

Route::get('/demanda/{demandaId}', [
    PublicacionesController::class, 'findDemanda'
])->middleware('auth:sanctum');

Route::put('/oferta', [
    PublicacionesController::class, 'updateOferta'
])->middleware('auth:sanctum');

Route::put('/demanda', [
    PublicacionesController::class, 'updateDemanda'
])->middleware('auth:sanctum');

Route::get('/publicaciones', [
    PublicacionesController::class, 'showAllPublicaciones'
])->middleware("auth:sanctum");

Route::post('/buy', [
    TransaccionController::class, "buy"
])->middleware("auth:sanctum");

Route::post('/pay', [
    TransaccionController::class, "pay"
])->middleware("auth:sanctum");

// MODIFICADO: Ruta POST /upload removida - fue movida a routes/web.php porque necesita autenticación por sesión (no Sanctum).
// La ruta GET /file/{filename} se mantiene sin autenticación para que las etiquetas <img> puedan cargar las imágenes.

//CRISTÓBAL (25-02-2026)
// Se removió autenticación para permitir que las imágenes de publicaciones se carguen en el frontend
// sin necesidad de headers de autorización (las etiquetas <img> no pueden enviar auth headers)
Route::get('/file/{filename}', [
    FileUploadController::class, 'show'
]);
Route::delete('/file/{filename}', [
    FileUploadController::class, 'delete'
])->middleware("auth:sanctum");
Route::post('/publicaciones/imagen', [
    PublicacionesController::class, 'asignImageToPublicacion'
])->middleware("auth:sanctum");
Route::get('/publicaciones/{publicacionId}/imagen',[
    PublicacionesController::class, 'getPublicacionImagen'
]);



Route::post('editar',[TestControllerCris::class, 'editar'])->name('editar');
Route::put('editar',[TestControllerCris::class, 'editar'])->name('editar');
Route::delete('editar',[TestControllerCris::class, 'editar'])->name('editar');
Route::get('obtener', [TestControllerCris::class, 'obtener'])->name('obtener');

