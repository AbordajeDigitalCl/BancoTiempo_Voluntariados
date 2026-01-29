<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Publicaciones (vista general)
    Route::get('/Advertisement', function () {
        return Inertia::render('Advertisement');
    })->name('advertisement');

    // Mis publicaciones
    Route::get('/MisPublicaciones', function () {
        return Inertia::render('MisPublicaciones');
    })->name('myadvertisements');

    // Mensajes / Chat
    Route::get('/chat', function () {
        return Inertia::render('Chat');
    })->name('chat');

    // Ver Perfil
    Route::get('/verperfil', function () {
        return Inertia::render('VerPerfil');
    })->name('verperfil');

    // Editar Servicio
    Route::get('/editarservicio', function () {
        return Inertia::render('EditarServicio');
    })->name('editarservicio');

    // Editar Publicacion
    Route::get('/editarpublicacion', function () {
        return Inertia::render('EditarPublicacion');
    })->name('editarpublicacion');

    // Notificaciones
    Route::get('/Notificaciones', function () {
        return Inertia::render('Notifications');
    })->name('notifications');
});

require __DIR__.'/auth.php';
