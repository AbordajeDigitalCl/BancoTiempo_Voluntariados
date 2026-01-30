<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/ServiceView', function () {
    return Inertia::render('ServiceView');
})->middleware(['auth', 'verified'])->name('serviceview');

Route::get('/chat', function () {
    return Inertia::render('Chat');
})->middleware(['auth', 'verified'])->name('chat');

Route::get('/VerPerfil', function () {
    return Inertia::render('VerPerfil');
})->middleware(['auth', 'verified'])->name('verperfil');

Route::get('/EditarPerfil', function () {
    return Inertia::render('EditarPerfil');
})->middleware(['auth', 'verified'])->name('editarperfil');

Route::get('/MenuHeader', function () {
    return Inertia::render('MenuHeader');
})->middleware(['auth', 'verified'])->name('menuheader');

Route::get('/EditarServicio', function () {
    return Inertia::render('EditarServicio');
})->middleware(['auth', 'verified'])->name('editarservicio');

Route::get('/EditarPublicacion', function () {
    return Inertia::render('EditarPublicacion');
})->middleware(['auth', 'verified'])->name('editarpublicacion');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/z', function(){
    return "abc";
});



require __DIR__.'/auth.php';

Route::post('count', function (Request $request) {
    return response()->json([
        'message' => $request->message,
    ]);
});

Route::post('select3', function (Request $request) {
    return response()->json([
        ['id' => 1, 'value' => 'option1', 'label' => 'Option 1'],
        ['id' => 2, 'value' => 'option2', 'label' => 'Option 2'],
    ]);
});