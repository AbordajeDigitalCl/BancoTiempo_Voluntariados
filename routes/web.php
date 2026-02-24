<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\User;
use App\Http\Controllers\TestControllerMarcelo;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return Inertia::render('Welcome', [
        'laravelVersion' => app()->version(),
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::get('/Admin', function () {
    return Inertia::render('AdminHome');
})->name('admin.home');

Route::middleware('guest')->group(function () {

    Route::get('/Admin/Login', function () {
        session(['url.intended' => '/Advertisement']);
        session(['is_admin' => true]);
        return Inertia::render('AdminLogin');
    })->name('admin.login');

    Route::get('/Admin/Register', function () {
        session(['is_admin' => true]);
        return Inertia::render('AdminRegister');
    })->name('admin.register');

    Route::post('/Admin/Register', [TestControllerMarcelo::class, 'adminRegister'])->name('admin.register.store');
    Route::post('/Admin/Login', [TestControllerMarcelo::class, 'adminLogin'])->name('admin.login.store');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [TestControllerMarcelo::class, 'dashboard'])->name('dashboard');

    Route::get('/Advertisement', [TestControllerMarcelo::class, 'advertisementIndex'])->name('advertisement');
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

    Route::get('/Advertisement/create', [TestControllerMarcelo::class, 'advertisementCreate'])
    ->name('advertisement.create');
    
    Route::get('/MyAdvertisements', [TestControllerMarcelo::class, 'myAdvertisementsIndex'])->name('myadvertisements');

    Route::get('/geo/reverse', function (Request $request) {
        $data = $request->validate([
            'lat' => ['required', 'numeric', 'min:-90', 'max:90'],
            'lng' => ['required', 'numeric', 'min:-180', 'max:180'],
        ]);
        $lat = $data['lat'];
        $lng = $data['lng'];
        $url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' . urlencode($lat) . '&lon=' . urlencode($lng);

        $ctx = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: BancoTiempo/1.0\r\nAccept: application/json\r\n",
                'timeout' => 8,
            ],
        ]);

        $raw = @file_get_contents($url, false, $ctx);
        if ($raw === false) {
            return response()->json(['display_name' => ''], 200);
        }

        $json = json_decode($raw, true);

        return response()->json([
            'display_name' => $json['display_name'] ?? '',
        ]);
    })->name('geo.reverse');

    Route::post('/Advertisement', function (Request $request) {
        $isAdmin = (bool) $request->session()->get('is_admin', false);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:80'],
            'description' => ['required', 'string', 'max:1000'],
            'hours' => ['required', 'integer', 'min:1', 'max:999'],
            'type' => ['required', 'in:Servicio,Producto'],
            'mode' => ['required', 'in:Oferta,Demanda'],
            'imageUrls' => ['nullable', 'array'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],

            'location_id' => ['nullable', 'integer'],
            'location_lat' => ['nullable', 'numeric', 'min:-90', 'max:90'],
            'location_lng' => ['nullable', 'numeric', 'min:-180', 'max:180'],
            'location_address' => ['nullable', 'string', 'max:500'], 
        ]);

        $createdAds = session('created_ads', []);
        $id = count($createdAds) > 0 ? (max(array_column($createdAds, 'id')) + 1) : 1;

        $imgs = $data['imageUrls'] ?? [];
        $imgs = array_slice($imgs, 0, 5);

        $targetUserId = $request->user()->id;
        $targetAuthor = $request->user()->name;

        if ($isAdmin && !empty($data['user_id'])) {
            $u = User::query()->select('id', 'name')->find($data['user_id']);
            if ($u) {
                $targetUserId = $u->id;
                $targetAuthor = $u->name;
            }
        }

        $new = [
            'id' => $id,
            'title' => $data['title'],
            'description' => $data['description'],
            'hours' => (int)$data['hours'],
            'type' => $data['type'],
            'mode' => $data['mode'],
            'status' => 'active',
            'author' => $targetAuthor,
            'user_id' => $targetUserId,
            'imageUrls' => $imgs,
            'imageUrl' => count($imgs) ? $imgs[0] : null,

            'location_id' => array_key_exists('location_id', $data) ? $data['location_id'] : null,
            'location_lat' => array_key_exists('location_lat', $data) ? $data['location_lat'] : null,
            'location_lng' => array_key_exists('location_lng', $data) ? $data['location_lng'] : null,
            'location_address' => array_key_exists('location_address', $data) ? $data['location_address'] : null, 
        ];

        $createdAds[] = $new;
        session(['created_ads' => $createdAds]);

        return redirect()->route('myadvertisements');
    })->name('advertisement.store');

    Route::get('/Advertisement/{id}/edit', [TestControllerMarcelo::class, 'advertisementEdit'])->name('advertisement.edit');

    Route::patch('/Advertisement/{id}', function (Request $request, $id) {
        $isAdmin = (bool) $request->session()->get('is_admin', false);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:80'],
            'description' => ['required', 'string', 'max:1000'],
            'hours' => ['required', 'integer', 'min:1', 'max:999'],
            'type' => ['required', 'in:Servicio,Producto'],
            'mode' => ['required', 'in:Oferta,Demanda'],
            'imageUrls' => ['nullable', 'array'],

            'location_id' => ['nullable', 'integer'],
            'location_lat' => ['nullable', 'numeric', 'min:-90', 'max:90'],
            'location_lng' => ['nullable', 'numeric', 'min:-180', 'max:180'],
            'location_address' => ['nullable', 'string', 'max:500'],
        ]);

        $createdAds = session('created_ads', []);
        $idx = collect($createdAds)->search(fn ($x) => (int)$x['id'] === (int)$id);
        if ($idx === false) return redirect()->route('myadvertisements');

        if (!$isAdmin && (($createdAds[$idx]['author'] ?? '') !== $request->user()->name)) {
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

        $createdAds[$idx]['location_id'] = array_key_exists('location_id', $data) ? $data['location_id'] : null;
        $createdAds[$idx]['location_lat'] = array_key_exists('location_lat', $data) ? $data['location_lat'] : null;
        $createdAds[$idx]['location_lng'] = array_key_exists('location_lng', $data) ? $data['location_lng'] : null;
        $createdAds[$idx]['location_address'] = array_key_exists('location_address', $data) ? $data['location_address'] : null; // ✅ NUEVO

        session(['created_ads' => $createdAds]);

        return redirect()->route('myadvertisements');
    })->name('advertisement.update');

    Route::patch('/Advertisement/{id}/status', function (Request $request, $id) {
        $isAdmin = (bool) $request->session()->get('is_admin', false);

        $data = $request->validate([
            'status' => ['required', 'in:active,paused,finished'],
        ]);

        $createdAds = session('created_ads', []);
        $idx = collect($createdAds)->search(fn ($x) => (int)$x['id'] === (int)$id);
        if ($idx === false) return redirect()->back();

        if (!$isAdmin && (($createdAds[$idx]['author'] ?? '') !== $request->user()->name)) {
            return redirect()->back();
        }

        $createdAds[$idx]['status'] = $data['status'];
        session(['created_ads' => $createdAds]);

        return redirect()->back();
    })->name('advertisement.status');

    Route::delete('/Advertisement/{id}', function (Request $request, $id) {
        $isAdmin = (bool) $request->session()->get('is_admin', false);

        $createdAds = session('created_ads', []);
        $createdAds = array_values(array_filter($createdAds, function ($x) use ($id, $request, $isAdmin) {
            if ((int)$x['id'] !== (int)$id) return true;
            if ($isAdmin) return false;
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

    Route::get('/Advertisement/{id}', [TestControllerMarcelo::class, 'advertisementShow'])->name('advertisement.show');

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
});

Route::middleware('guest')->group(function () {
    Route::get('/test/register', [TestControllerMarcelo::class, 'showRegister'])->name('test.register.view');
    Route::post('/test/register', [TestControllerMarcelo::class, 'register'])->name('test.register.store');

    Route::get('/test/login', [TestControllerMarcelo::class, 'showLogin'])->name('test.login.view');
    Route::post('/test/login', [TestControllerMarcelo::class, 'login'])->name('test.login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/test/logout', [TestControllerMarcelo::class, 'logout'])->name('test.logout');
});

require __DIR__ . '/auth.php';