<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;


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


Route::post('editar',[TestController::class, 'editar'])->name('editar');
Route::put('editar',[TestController::class, 'editar'])->name('editar');
Route::delete('editar',[TestController::class, 'editar'])->name('editar');
Route::get('obtener', [TestController::class, 'obtener'])->name('obtener');
