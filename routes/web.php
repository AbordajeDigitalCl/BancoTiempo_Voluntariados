<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

Route::get('/chat', function () {
    return Inertia::render('Chat');
})->middleware(['auth', 'verified'])->name('chat');

Route::get('/VerPerfil', function () {
    return Inertia::render('VerPerfil');
})->middleware(['auth', 'verified'])->name('verperfil');

Route::get('/EditarServicio', function () {
    return Inertia::render('EditarServicio');
})->middleware(['auth', 'verified'])->name('editarservicio');

Route::get('/Advertisement/create', function () {
    return Inertia::render('CreateAdvertisement');
})->middleware(['auth', 'verified'])->name('advertisement.create');

Route::get('/EditarPublicacion', function (Request $request) {
    return Inertia::render('EditarPublicacion', [
        'item' => $request->input('item'),
    ]);
})->middleware(['auth', 'verified'])->name('editarpublicacion');


Route::get('/Notifications', function () {
    return Inertia::render('Notifications');
})->middleware(['auth', 'verified'])->name('notifications');



Route::get('/Advertisement', function (Request $request) {
    $createdAds = session('created_ads', []);

    return Inertia::render('Advertisement', [
        'createdAds' => $createdAds,
    ]);
})->middleware(['auth', 'verified'])->name('advertisement');

Route::post('/Advertisement', function (Request $request) {
    $data = $request->validate([
        'title' => ['required', 'string', 'max:80'],
        'description' => ['required', 'string', 'max:500'],
        'type' => ['required', 'in:Servicio,Producto'],
        'mode' => ['required', 'in:Oferta,Demanda'],
        'hours' => ['required', 'integer', 'min:1', 'max:999'],
        'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
    ]);

    $imageUrl = null;

    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('ads', 'public');
        $imageUrl = Storage::url($path);
    }

    $new = [
        'id' => now()->timestamp,
        'title' => $data['title'],
        'description' => $data['description'],
        'hours' => (int) $data['hours'],
        'type' => $data['type'],
        'mode' => $data['mode'],
        'author' => $request->user()->name ?? 'Usuario',
        'imageUrl' => $imageUrl ?: 'https://picsum.photos/seed/' . now()->timestamp . '/900/600',
    ];

    $created = session('created_ads', []);
    array_unshift($created, $new);
    session(['created_ads' => $created]);

    return redirect()->route('advertisement');
})->middleware(['auth', 'verified'])->name('advertisement.store');

Route::put('/Advertisement/{id}', function (Request $request, $id) {
    $data = $request->validate([
        'title' => ['required', 'string', 'max:80'],
        'description' => ['required', 'string', 'max:500'],
        'type' => ['required', 'in:Servicio,Producto'],
        'mode' => ['required', 'in:Oferta,Demanda'],
        'hours' => ['required', 'integer', 'min:1', 'max:999'],
    ]);

    $created = session('created_ads', []);

    $updated = [];
    $found = false;

    foreach ($created as $ad) {
        if ((string)($ad['id'] ?? '') === (string)$id) {
            $ad['title'] = $data['title'];
            $ad['description'] = $data['description'];
            $ad['type'] = $data['type'];
            $ad['mode'] = $data['mode'];
            $ad['hours'] = (int)$data['hours'];
            $found = true;
        }
        $updated[] = $ad;
    }

    if (!$found) {
        return redirect()->route('advertisement');
    }

    session(['created_ads' => $updated]);

    return redirect()->route('advertisement');
})->middleware(['auth', 'verified'])->name('advertisement.update');


Route::delete('/Advertisement/{id}', function (Request $request, $id) {
    $created = session('created_ads', []);

    $created = array_values(array_filter($created, function ($ad) use ($id) {
        return (string)($ad['id'] ?? '') !== (string)$id;
    }));

    session(['created_ads' => $created]);

    return redirect()->route('advertisement');
})->middleware(['auth', 'verified'])->name('advertisement.destroy');

Route::get('/ServiceView', function (Request $request) {
    return Inertia::render('ServiceView', [
        'item' => $request->input('item'),
    ]);
})->middleware(['auth', 'verified'])->name('serviceview');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/z', function () {
    return "abc";
});

require __DIR__ . '/auth.php';

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
