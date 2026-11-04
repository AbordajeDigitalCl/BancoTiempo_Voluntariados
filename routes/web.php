<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Advertisement;
use App\Http\Controllers\TestControllerMarcelo;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\AdvertisementController;
use App\Http\Controllers\DashboardController;

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

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');


    Route::get('/EditarPerfil/{id?}', [\App\Http\Controllers\PerfilController::class, 'editarPerfil']);

    Route::get('/EditarPerfil', [\App\Http\Controllers\TestControllerCris::class, 'editarPerfil'])
        ->middleware(['auth', 'verified'])->name('editarperfil');


    Route::post('/EditarPerfil/{id?}', [\App\Http\Controllers\PerfilController::class, 'guardarPerfil'])
        ->middleware(['auth', 'verified']);

    // MODIFICADO: Ruta de upload movida de routes/api.php a routes/web.php porque las rutas API no reconocen autenticación por sesión (solo Sanctum tokens).
    // Este endpoint necesita funcionar con cookies de sesión como el resto del panel de edición de perfil.
    Route::post('/upload', [\App\Http\Controllers\FileUploadController::class, 'upload'])
        ->middleware('auth')->name('upload');

    // MODIFICADO: Endpoint adicional en TestControllerCris.php para subir imagen con usuario_id específico.
    // Permite editar imágenes de otros usuarios como admin, y devuelve CSRF token actualizado para evitar error 419.
    // Se aplican los mismos middleware que las otras rutas del grupo (auth, verified, csrf automático)
    Route::post('/subir-imagen/{usuario_id}', [\App\Http\Controllers\PerfilController::class, 'subirImagen'])
        ->name('subir-imagen');

    // NUEVO: Endpoint para subir imagen a una publicación
    Route::post('/subir-imagen-publicacion/{publicacion_id}', [\App\Http\Controllers\AdvertisementController::class, 'subirImagenPublicacion'])
        ->name('subir-imagen-publicacion');

    // NUEVO: Endpoint para eliminar una imagen de una publicación
    // Solo desasocia la imagen
    Route::delete('/eliminar-imagen-publicacion/{publicacion_id}/{imagen_id}', [\App\Http\Controllers\AdvertisementController::class, 'eliminarImagenPublicacion'])
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

    Route::get('/Advertisement', [AdvertisementController::class, 'advertisementIndex'])->name('advertisement');

    Route::get('/MenuHeader', function () {
        return Inertia::render('MenuHeader');
    })->middleware(['auth', 'verified'])->name('menuheader');

    Route::get('/Administrar-Usuarios', [\App\Http\Controllers\UserAdminController::class, 'administrarUsuarios'])
        ->middleware(['auth', 'verified'])->name('administrar-usuarios');

    Route::post('/administrar-usuarios/crear-conversacion', [\App\Http\Controllers\ConversacionController::class, 'crearOBuscarConversacion'])
        ->middleware(['auth', 'verified']);

    Route::delete('/administrar-usuarios/eliminar-usuario', [\App\Http\Controllers\UserAdminController::class, 'eliminarUsuario'])
        ->middleware(['auth', 'verified']);

    Route::get('/Editar-Publicacion', function () {
        return Inertia::render('SeleccionarPublicacionEditarCRIS');
    })->middleware(['auth', 'verified'])->name('editar-publicacion');

    Route::get('/Editar-Publicacion/{id}', [\App\Http\Controllers\AdvertisementController::class, 'editarPublicacion'])
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

    Route::get('/Advertisement/create', [AdvertisementController::class, 'advertisementCreate'])
        ->name('advertisement.create');

    Route::get('/MyAdvertisements', [AdvertisementController::class, 'myAdvertisementsIndex'])
    ->name('myadvertisements')
    ->middleware(['auth', 'verified']);

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
            'imageUrls' => ['nullable', 'array', 'max:5'],
            'imageUrls.*' => ['nullable', 'string'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'location_id' => ['nullable', 'integer'],
            'location_lat' => ['nullable', 'numeric', 'min:-90', 'max:90'],
            'location_lng' => ['nullable', 'numeric', 'min:-180', 'max:180'],
            'location_address' => ['nullable', 'string', 'max:500'],
        ]);

        $imgs = $data['imageUrls'] ?? [];
        $imgs = array_values(array_filter($imgs, fn ($x) => is_string($x) && trim($x) !== ''));
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

        Advertisement::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'hours' => (int) $data['hours'],
            'type' => $data['type'],
            'mode' => $data['mode'],
            'status' => 'active',
            'author' => $targetAuthor,
// La tabla publicacions usa id_usuario en lugar de user_id, por eso se camabio.
            'id_usuario' => $targetUserId,
// publicacions requiere Nombre y Descripcion como campos obligatorios
// Se mapean desde title y description del formulario
            'Nombre' => $data['title'],
            'Descripcion' => $data['description'],
// publicacions requiere Tipo_Publicacion y Tipo_Beneficio como campos obligatorios
// Oferta=0, Demanda=1 / Servicio=0, Producto=1
            'Tipo_Publicacion' => $data['mode'] === 'Oferta' ? 0 : 1,
            'Tipo_Beneficio' => $data['type'] === 'Servicio' ? 0 : 1,
            'image_urls' => $imgs,
            'location_id' => $data['location_id'] ?? null,
            'location_lat' => $data['location_lat'] ?? null,
            'location_lng' => $data['location_lng'] ?? null,
            'location_address' => $data['location_address'] ?? null,
]);

        return redirect()->route('myadvertisements');
    })->name('advertisement.store');

    Route::get('/Advertisement/{id}/edit', [AdvertisementController::class, 'advertisementEdit'])->name('advertisement.edit');
    /*
    Route::patch('/Advertisement/{id}', function (Request $request, $id) {
        $isAdmin = (bool) $request->session()->get('is_admin', false);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:80'],
            'description' => ['required', 'string', 'max:1000'],
            'hours' => ['required', 'integer', 'min:1', 'max:999'],
            'type' => ['required', 'in:Servicio,Producto'],
            'mode' => ['required', 'in:Oferta,Demanda'],
            'imageUrls' => ['nullable', 'array', 'max:5'],
            'imageUrls.*' => ['nullable', 'string'],
            'location_id' => ['nullable', 'integer'],
            'location_lat' => ['nullable', 'numeric', 'min:-90', 'max:90'],
            'location_lng' => ['nullable', 'numeric', 'min:-180', 'max:180'],
            'location_address' => ['nullable', 'string', 'max:500'],
        ]);

        $ad = Advertisement::find($id);
        if (!$ad) {
            return redirect()->route('myadvertisements');
        }

        if (
            !$isAdmin &&
            (int) ($ad->user_id ?? 0) !== (int) ($request->user()->id ?? 0) &&
            (string) ($ad->author ?? '') !== (string) ($request->user()->name ?? '')
        ) {
            return redirect()->route('myadvertisements');
        }

        $imgs = $data['imageUrls'] ?? [];
        $imgs = array_values(array_filter($imgs, fn ($x) => is_string($x) && trim($x) !== ''));
        $imgs = array_slice($imgs, 0, 5);

        $ad->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'hours' => (int) $data['hours'],
            'type' => $data['type'],
            'mode' => $data['mode'],
            'image_urls' => $imgs,
            'location_id' => array_key_exists('location_id', $data) ? $data['location_id'] : null,
            'location_lat' => array_key_exists('location_lat', $data) ? $data['location_lat'] : null,
            'location_lng' => array_key_exists('location_lng', $data) ? $data['location_lng'] : null,
            'location_address' => array_key_exists('location_address', $data) ? $data['location_address'] : null,
        ]);

        return redirect()->route('myadvertisements');
    })->name('advertisement.update');
    */
    Route::patch('/Advertisement/{id}', [AdvertisementController::class, 'advertisementUpdate'])
    ->name('advertisement.update');

    Route::patch('/Advertisement/{id}/status', function (Request $request, $id) {
        $isAdmin = (bool) $request->session()->get('is_admin', false);

        $data = $request->validate([
            'status' => ['required', 'in:active,paused,finished'],
        ]);

        $ad = Advertisement::find($id);
        if (!$ad) return redirect()->back();

        if (
            !$isAdmin &&
            (int) ($ad->user_id ?? 0) !== (int) ($request->user()->id ?? 0) &&
            (string) ($ad->author ?? '') !== (string) ($request->user()->name ?? '')
        ) {
            return redirect()->back();
        }

        $ad->update([
            'status' => $data['status'],
        ]);

        return redirect()->back();
    })->name('advertisement.status');

    Route::delete('/Advertisement/{id}', function (Request $request, $id) {
        $isAdmin = (bool) $request->session()->get('is_admin', false);

        $ad = Advertisement::find($id);
        if (!$ad) return redirect()->back();

        if (
            !$isAdmin &&
            (int) ($ad->user_id ?? 0) !== (int) ($request->user()->id ?? 0) &&
            (string) ($ad->author ?? '') !== (string) ($request->user()->name ?? '')
        ) {
            return redirect()->back();
        }

        $ad->delete();

        return redirect()->back();
    })->name('advertisement.destroy');

    Route::post('/Advertisement/{id}/report', function (Request $request, $id) {
        $ad = Advertisement::find($id);
        if (!$ad) {
            return redirect()->back();
        }

        $reports = session('ad_reports', []);
        $reports[] = [
            'ad_id' => (int) $id,
            'by' => $request->user()->name,
            'at' => now()->toDateTimeString(),
        ];
        session(['ad_reports' => $reports]);

        return redirect()->back();
    })->name('advertisement.report');

    Route::get('/Advertisement/{id}', [AdvertisementController::class, 'advertisementShow'])->name('advertisement.show');

    Route::get('/chat', [\App\Http\Controllers\ChatController::class, 'chat'])
        ->middleware(['auth', 'verified'])->name('chat');

    Route::post('/chat/enviar-mensaje', [\App\Http\Controllers\MensajeController::class, 'enviarMensaje'])
        ->middleware(['auth', 'verified']);

    Route::post('/chat/enviar-mensaje-imagen', [\App\Http\Controllers\MensajeController::class, 'enviarMensajeConImagen'])
        ->middleware(['auth', 'verified']);

    Route::post('/chat/marcar-leidos', [\App\Http\Controllers\MensajeController::class, 'marcarLeidos'])
    ->middleware(['auth', 'verified'])
    ->name('chat.marcar-leidos');

    //Ruta para obtener mensajes de una conversación para el mostrar el chat en tiempo real
    Route::get('/chat/mensajes', [\App\Http\Controllers\MensajeController::class, 'obtenerMensajesConversacion'])
    ->name('chat.mensajes');

    Route::get('/verperfil/{id?}', [\App\Http\Controllers\PerfilController::class, 'verPerfil'])
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