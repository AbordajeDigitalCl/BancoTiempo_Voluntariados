<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');


    Route::get('/Advertisement', function (Request $request) {
        $createdAds = session('created_ads', []);
        return Inertia::render('Advertisement', [
            'createdAds' => $createdAds,
        ]);
    })->name('advertisement');


    Route::get('/Advertisement/{id}', function (Request $request, $id) {
        $createdAds = session('created_ads', []);
        $item = collect($createdAds)->firstWhere('id', (int)$id);

        return Inertia::render('AdvertisementShow', [
            'item' => $item ?: null,
        ]);
    })->name('advertisement.show');


    Route::get('/MyAdvertisements', function (Request $request) {
        $createdAds = session('created_ads', []);
        return Inertia::render('MyAdvertisements', [
            'createdAds' => $createdAds,
        ]);
    })->name('myadvertisements');


    Route::get('/Advertisement/create', function () {
        return Inertia::render('CreateAdvertisement');
    })->name('advertisement.create');


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
            'imageUrl' => count($imgs) ? $imgs[0] : null, // compat
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

    // Eliminar (autor)
    Route::delete('/Advertisement/{id}', function (Request $request, $id) {
        $createdAds = session('created_ads', []);
        $createdAds = array_values(array_filter($createdAds, function ($x) use ($id, $request) {
            if ((int)$x['id'] !== (int)$id) return true;
            return ($x['author'] ?? '') !== $request->user()->name;
        }));

        session(['created_ads' => $createdAds]);
        return redirect()->back();
    })->name('advertisement.destroy');

    // reportes
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

    //otras vistas

    Route::get('/chat', function () {
        return Inertia::render('Chat');
    })->name('chat');

    Route::get('/verperfil', function () {
        return Inertia::render('VerPerfil');
    })->name('verperfil');

    Route::get('/editarservicio', function () {
        return Inertia::render('EditarServicio');
    })->name('editarservicio');

    Route::get('/Notificaciones', function () {
        return Inertia::render('Notifications');
    })->name('notifications');
});

require __DIR__ . '/auth.php';
