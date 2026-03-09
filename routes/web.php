<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\User;
use App\Http\Controllers\TestControllerMarcelo;
use App\Http\Controllers\FileUploadController;

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


    Route::get('/EditarPerfil/{id?}', [\App\Http\Controllers\TestControllerCris::class, 'editarPerfil'])
        ->middleware(['auth', 'verified'])->name('editarperfil');

    Route::post('/EditarPerfil/{id?}', [\App\Http\Controllers\TestControllerCris::class, 'guardarPerfil'])
        ->middleware(['auth', 'verified']);

    // MODIFICADO: Ruta de upload movida de routes/api.php a routes/web.php porque las rutas API no reconocen autenticación por sesión (solo Sanctum tokens).
    // Este endpoint necesita funcionar con cookies de sesión como el resto del panel de edición de perfil.
    Route::post('/upload', [\App\Http\Controllers\FileUploadController::class, 'upload'])
        ->middleware('auth')->name('upload');

    // MODIFICADO: Endpoint adicional en TestControllerCris.php para subir imagen con usuario_id específico.
    // Permite editar imágenes de otros usuarios como admin, y devuelve CSRF token actualizado para evitar error 419.
    // Se aplican los mismos middleware que las otras rutas del grupo (auth, verified, csrf automático)
    Route::post('/subir-imagen/{usuario_id}', [\App\Http\Controllers\TestControllerCris::class, 'subirImagen'])
        ->name('subir-imagen');

    // NUEVO: Endpoint para subir imagen a una publicación
    Route::post('/subir-imagen-publicacion/{publicacion_id}', [\App\Http\Controllers\TestControllerCris::class, 'subirImagenPublicacion'])
        ->name('subir-imagen-publicacion');

    // NUEVO: Endpoint para eliminar una imagen de una publicación
    // Solo desasocia la imagen
    Route::delete('/eliminar-imagen-publicacion/{publicacion_id}/{imagen_id}', [\App\Http\Controllers\TestControllerCris::class, 'eliminarImagenPublicacion'])
        ->name('eliminar-imagen-publicacion');

    // NUEVO: Endpoint alternativo para servir imágenes (backup si /api/file/ no funciona)
    Route::get('/imagen/{filename}', [\App\Http\Controllers\TestControllerCris::class, 'servirImagen'])
        ->name('servir-imagen')
        ->withoutMiddleware(['auth', 'verified']); // Permitir acceso sin autenticación para cargar imágenes

    // SACADO MOMENTÁNEAMENTE: Comentada para la función obtenerImagenesPublicacion() de TesControllerCris.php.
    /*
    Route::get('/obtener-imagenes-publicacion/{publicacion_id}', [\App\Http\Controllers\TestControllerCris::class, 'obtenerImagenesPublicacion'])
        ->name('obtener-imagenes-publicacion')
        ->withoutMiddleware('verified'); // Solo requiere auth básica
    */

    // NUEVO: Endpoint para sincronizar el CSRF token después de un upload.
    Route::get('/refresh-csrf', function () {
        return response()->json(['csrf_token' => csrf_token()]);
    })->name('refresh-csrf');

    Route::get('/Advertisement', [TestControllerMarcelo::class, 'advertisementIndex'])->name('advertisement');
    


    Route::get('/MenuHeader', function () {
        return Inertia::render('MenuHeader');
    })->middleware(['auth', 'verified'])->name('menuheader');

    Route::get('/Administrar-Usuarios', [\App\Http\Controllers\TestControllerCris::class, 'administrarUsuarios'])
        ->middleware(['auth', 'verified'])->name('administrar-usuarios');

    Route::post('/administrar-usuarios/crear-conversacion', [\App\Http\Controllers\TestControllerCris::class, 'crearOBuscarConversacion'])
        ->middleware(['auth', 'verified']);

    Route::delete('/administrar-usuarios/eliminar-usuario', [\App\Http\Controllers\TestControllerCris::class, 'eliminarUsuario'])
        ->middleware(['auth', 'verified']);

    Route::get('/Editar-Publicacion', function () {
        return Inertia::render('SeleccionarPublicacionEditarCRIS');
    })->middleware(['auth', 'verified'])->name('editar-publicacion');

    Route::get('/Editar-Publicacion/{id}', [\App\Http\Controllers\TestControllerCris::class, 'editarPublicacion'])
        ->middleware(['auth', 'verified'])->name('editar-publicacion-detalle');

    Route::post('/Editar-Publicacion/{id}', [\App\Http\Controllers\TestControllerCris::class, 'guardarPublicacion'])
        ->middleware(['auth', 'verified']);

    // RUTAS COMENTADAS: Sistema de calificaciones (En desarrollo)
    // Estas rutas son PLACEHOLDERS y están comentadas porque:
    // 1. Las funciones calificaciones() y asociarcalificaciones() están desactivadas
    // 2. El diseño de la BD aún no está terminado
    // SE DESCOMENTA CUANDO: Las funciones se activen y la BD esté lista
    
    // Route::get('/calificaciones/{publicacion_id}', [\App\Http\Controllers\TestControllerCris::class, 'calificaciones'])
    //     ->middleware(['auth', 'verified'])->name('calificaciones-get');
    //
    // Route::post('/asociar-calificaciones/{publicacion_id}', [\App\Http\Controllers\TestControllerCris::class, 'asociarcalificaciones'])
    //     ->middleware(['auth', 'verified'])->name('calificaciones-post');

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

    Route::get('/chat', [\App\Http\Controllers\TestControllerCris::class, 'chat'])
        ->middleware(['auth', 'verified'])->name('chat');

    Route::post('/chat/enviar-mensaje', [\App\Http\Controllers\TestControllerCris::class, 'enviarMensaje'])
        ->middleware(['auth', 'verified']);

    Route::post('/chat/enviar-mensaje-imagen', [\App\Http\Controllers\TestControllerCris::class, 'enviarMensajeConImagen'])
        ->middleware(['auth', 'verified']);

    Route::get('/verperfil/{id?}', [\App\Http\Controllers\TestControllerCris::class, 'verPerfil'])
        ->middleware(['auth', 'verified'])->name('verperfil');

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