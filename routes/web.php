<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return Inertia::render('Welcome', [
        'laravelVersion' => app()->version(),
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    

    Route::get('/Admin', function () {
    return Inertia::render('AdminHome');
    })->name('admin.home');


    

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');


    Route::get('/Advertisement', function (Request $request) {
        $createdAds = session('created_ads', []);
        return Inertia::render('Advertisement', [
            'createdAds' => $createdAds,
        ]);
    })->name('advertisement');

    Route::get('/EditarPerfil', function () {
        return Inertia::render('EditarPerfil');
    })->middleware(['auth', 'verified'])->name('editarperfil');

    Route::get('/MenuHeader', function () {
        return Inertia::render('MenuHeader');
    })->middleware(['auth', 'verified'])->name('menuheader');

    Route::get('/Administrar-Usuarios', function () {
        return Inertia::render('AdministrarUsuarios');
    })->middleware(['auth', 'verified'])->name('administrar-usuarios');

    Route::get('/Editar-Publicacion', function () {
        return Inertia::render('SeleccionarPublicacionEditarCRIS');
    })->middleware(['auth', 'verified'])->name('editar-publicacion');

    Route::get('/Editar-Publicacion/{id}', function ($id) {
        return Inertia::render('EditarMiPublicacionCRIS', [
            'publicacionId' => $id
        ]);
    })->middleware(['auth', 'verified'])->name('editar-publicacion-detalle');

    Route::get('/Advertisement/create', function () {
        return Inertia::render('CreateAdvertisement');
    })->name('advertisement.create');


    Route::get('/MyAdvertisements', function (Request $request) {
        $createdAds = session('created_ads', []);
        return Inertia::render('MyAdvertisements', [
            'createdAds' => $createdAds,
        ]);
    })->name('myadvertisements');


    Route::post('/Advertisement', function (Request $request) {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:80'],
            'description' => ['required', 'string', 'max:1000'],
            'hours' => ['required', 'integer', 'min:1', 'max:999'],
            'type' => ['required', 'in:Servicio,Producto'],
            'mode' => ['required', 'in:Oferta,Demanda'],
            'imageUrls' => ['nullable', 'array'],
        ]);

        $createdAds = session('created_ads', []);
        $id = count($createdAds) > 0 ? (max(array_column($createdAds, 'id')) + 1) : 1;

        $imgs = $data['imageUrls'] ?? [];
        $imgs = array_slice($imgs, 0, 5);

        $new = [
            'id' => $id,
            'title' => $data['title'],
            'description' => $data['description'],
            'hours' => (int)$data['hours'],
            'type' => $data['type'],
            'mode' => $data['mode'],
            'status' => 'active',
            'author' => $request->user()->name,
            'imageUrls' => $imgs,
            'imageUrl' => count($imgs) ? $imgs[0] : null,
        ];

        $createdAds[] = $new;
        session(['created_ads' => $createdAds]);

        return redirect()->route('myadvertisements');
    })->name('advertisement.store');


    Route::get('/Advertisement/{id}/edit', function (Request $request, $id) {
        $createdAds = session('created_ads', []);
        $item = collect($createdAds)->firstWhere('id', (int)$id);

        return Inertia::render('EditarPublicacion', [
            'item' => $item ?: null,
        ]);
    })->name('advertisement.edit');


    Route::patch('/Advertisement/{id}', function (Request $request, $id) {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:80'],
            'description' => ['required', 'string', 'max:1000'],
            'hours' => ['required', 'integer', 'min:1', 'max:999'],
            'type' => ['required', 'in:Servicio,Producto'],
            'mode' => ['required', 'in:Oferta,Demanda'],
            'imageUrls' => ['nullable', 'array'],
        ]);

        $createdAds = session('created_ads', []);
        $idx = collect($createdAds)->search(fn ($x) => (int)$x['id'] === (int)$id);
        if ($idx === false) return redirect()->route('myadvertisements');

        if (($createdAds[$idx]['author'] ?? '') !== $request->user()->name) {
            return redirect()->route('myadvertisements');
        }

        $createdAds[$idx]['title'] = $data['title'];
        $createdAds[$idx]['description'] = $data['description'];
        $createdAds[$idx]['hours'] = (int)$data['hours'];
        $createdAds[$idx]['type'] = $data['type'];
        $createdAds[$idx]['mode'] = $data['mode'];

        $imgs = $data['imageUrls'] ?? [];
        $imgs = array_slice($imgs, 0, 5);

        $createdAds[$idx]['imageUrls'] = $imgs;
        $createdAds[$idx]['imageUrl'] = count($imgs) ? $imgs[0] : null;

        session(['created_ads' => $createdAds]);

        return redirect()->route('myadvertisements');
    })->name('advertisement.update');


    Route::patch('/Advertisement/{id}/status', function (Request $request, $id) {
        $data = $request->validate([
            'status' => ['required', 'in:active,paused,finished'],
        ]);

        $createdAds = session('created_ads', []);
        $idx = collect($createdAds)->search(fn ($x) => (int)$x['id'] === (int)$id);
        if ($idx === false) return redirect()->back();

        if (($createdAds[$idx]['author'] ?? '') !== $request->user()->name) {
            return redirect()->back();
        }

        $createdAds[$idx]['status'] = $data['status'];
        session(['created_ads' => $createdAds]);

        return redirect()->back();
    })->name('advertisement.status');

    

    Route::delete('/Advertisement/{id}', function (Request $request, $id) {
        $createdAds = session('created_ads', []);
        $createdAds = array_values(array_filter($createdAds, function ($x) use ($id, $request) {
            if ((int)$x['id'] !== (int)$id) return true;
            return ($x['author'] ?? '') !== $request->user()->name;
        }));

        session(['created_ads' => $createdAds]);
        return redirect()->back();
    })->name('advertisement.destroy');


    Route::post('/Advertisement/{id}/report', function (Request $request, $id) {
        $reports = session('ad_reports', []);
        $reports[] = [
            'ad_id' => (int)$id,
            'by' => $request->user()->name,
            'at' => now()->toDateTimeString(),
        ];
        session(['ad_reports' => $reports]);

        return redirect()->back();
    })->name('advertisement.report');


    Route::get('/Advertisement/{id}', function (Request $request, $id) {
        $createdAds = session('created_ads', []);
        $item = collect($createdAds)->firstWhere('id', (int)$id);

        return Inertia::render('AdvertisementShow', [
            'item' => $item ?: null,
        ]);
    })->name('advertisement.show');



    Route::get('/chat', function () {
        return Inertia::render('Chat');
    })->name('chat');

    Route::get('/verperfil', function () {
        return Inertia::render('VerPerfil');
    })->name('verperfil');

    Route::get('/verperfil-Admin/{id?}', function ($id = null) {
        return Inertia::render('VerPerfilAdmin', [
            'usuarioId' => $id
        ]);
    })->name('verperfil-admin');

    Route::get('/perfil-Admin/{id?}', function ($id = null) {
        return Inertia::render('PerfilAdmin', [
            'usuarioId' => $id
        ]);
    })->name('perfil-admin');

    Route::get('/editarservicio', function () {
        return Inertia::render('EditarServicio');
    })->name('editarservicio');

    Route::get('/Notificaciones', function () {
        return Inertia::render('Notifications');
    })->name('notifications');
});

require __DIR__ . '/auth.php';
