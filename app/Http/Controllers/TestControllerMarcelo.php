<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class TestControllerMarcelo extends Controller
{
    /*
     |--------------------------------------------------------------------------
     | Funciones TestControllerMarcelo
     |--------------------------------------------------------------------------
     | Controlador principal para:
     | - Auth usuario normal (register/login/logout)
     | - Auth admin (register/login con bandera is_admin en sesión)
     | - Publicaciones (listado general / detalle / editar / crear)
     | - Dashboard (estadísticas calculadas desde session('created_ads'))
     |
     | Importante:
     | - Publicaciones NO salen de BD: salen de session('created_ads', []).
     | - "Admin" en este flujo es una bandera de sesión (is_admin), no un rol persistido.
     */

    /*
     |--------------------------------------------------------------------------
     | REGISTRO DE USUARIO (VISTA + ACCIÓN)
     |--------------------------------------------------------------------------
     | - Vista: resources/js/Pages/Auth/Register.tsx
     | - Objetivo: mostrar formulario de registro y crear usuario.
     |
     | Campos esperados (users):
     |  name, Apellido_Paterno, Apellido_Materno, Age (fecha nac.), Telefono,
     |  Direccion, Ciudad, email (único), password (+ confirmation)
     |
     | Nota: profile_photo se valida, pero NO se persiste aún (falta columna/flujo).
     */

    /**
     * @summary Vista de registro de usuario.
     * @description Renderiza el formulario de registro para usuario normal.
     *
     * @view resources/js/Pages/Auth/Register.tsx
     * @param  none
     * @return \Inertia\Response
     */
    public function showRegister(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * @summary Acción de registro de usuario.
     * @description
     * - Valida los datos recibidos desde el formulario.
     * - Crea el usuario en la tabla users.
     * - Dispara el evento Registered.
     * - Loguea automáticamente al usuario y regenera sesión.
     *
     * @source Request (formulario Register)
     * @notes
     * - profile_photo se valida, pero NO se guarda (pendiente columna + store).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse Redirect a HOME autenticado.
     */
    public function register(Request $request): RedirectResponse
    {
        // Validación de formato y reglas mínimas antes de crear el usuario.
        $request->validate([
            'profile_photo' => ['nullable', 'file', 'image', 'max:4096'],
            'name' => ['required', 'string', 'max:255'],
            'Apellido_Paterno' => ['required', 'string', 'max:255'],
            'Apellido_Materno' => ['required', 'string', 'max:255'],
            'Age' => ['required', 'date'],
            'Telefono' => ['required', 'string', 'max:15'],
            'Direccion' => ['required', 'string', 'max:255'],
            'Ciudad' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        /*
         Pendiente (si se agrega profile_photo_path u otra columna):
         - $path = $request->file('profile_photo')->store('profiles', 'public');
         - Guardar $path en el usuario.
        */

        // Creación del usuario. Password queda hasheada por seguridad.
        $user = User::create([
            'name' => $request->name,
            'Apellido_Paterno' => $request->Apellido_Paterno,
            'Apellido_Materno' => $request->Apellido_Materno,
            'Age' => $request->Age,
            'Telefono' => $request->Telefono,
            'Direccion' => $request->Direccion,
            'Ciudad' => $request->Ciudad,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Evento estándar post-registro (Laravel).
        event(new Registered($user));

        // Autologin y hardening de sesión.
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /*
     |--------------------------------------------------------------------------
     | LOGIN DE USUARIO (VISTA + ACCIÓN)
     |--------------------------------------------------------------------------
     | - Vista: resources/js/Pages/Auth/Login.tsx
     | - LoginRequest define el shape esperado:
     |   email (required|email), password (required|string), remember (opcional)
     */

    /**
     * @summary Vista de login de usuario.
     * @description Renderiza el formulario de login para usuario normal.
     *
     * @view resources/js/Pages/Auth/Login.tsx
     * @return \Inertia\Response
     */
    public function showLogin(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => \Illuminate\Support\Facades\Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * @summary Acción de login de usuario.
     * @description
     * - Autentica usando LoginRequest (valida + authenticate()).
     * - Regenera la sesión por seguridad.
     *
     * @source LoginRequest (credenciales)
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse Redirect a HOME autenticado.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /*
     |--------------------------------------------------------------------------
     | LOGOUT
     |--------------------------------------------------------------------------
     | - Objetivo: cerrar sesión del usuario actual y limpiar sesión/token.
     */

    /**
     * @summary Cierre de sesión.
     * @description
     * - Desloguea al usuario.
     * - Invalida la sesión.
     * - Regenera token CSRF.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse Redirect a '/'.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /*
     |--------------------------------------------------------------------------
     | ADMIN REGISTER (ACCIÓN)
     |--------------------------------------------------------------------------
     | - Vista: resources/js/Pages/AdminRegister.tsx
     | - Diferencia clave: guarda bandera is_admin = true en sesión.
     | - Tener en consideracion: es un rol persistido en BD, es un flag temporal de sesión.
     */

    /**
     * @summary Registro de admin (por bandera de sesión).
     * @description
     * - Setea is_admin=true en sesión.
     * - Valida datos.
     * - Crea usuario.
     * - Dispara Registered.
     * - Autentica y regenera sesión.
     *
     * @view resources/js/Pages/AdminRegister.tsx
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse Redirect a '/Advertisement' autenticado.
     */
    public function adminRegister(Request $request): RedirectResponse
    {
        $request->session()->put('is_admin', true);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'Apellido_Paterno' => ['required', 'string', 'max:255'],
            'Apellido_Materno' => ['required', 'string', 'max:255'],
            'Age' => ['required', 'date'],
            'Telefono' => ['required', 'string', 'max:15'],
            'Direccion' => ['required', 'string', 'max:255'],
            'Ciudad' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'Apellido_Paterno' => $request->Apellido_Paterno,
            'Apellido_Materno' => $request->Apellido_Materno,
            'Age' => $request->Age,
            'Telefono' => $request->Telefono,
            'Direccion' => $request->Direccion,
            'Ciudad' => $request->Ciudad,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/Advertisement');
    }

    /*
     |--------------------------------------------------------------------------
     | ADMIN LOGIN (ACCIÓN)
     |--------------------------------------------------------------------------
     | - Vista: resources/js/Pages/AdminLogin.tsx
     | - Diferencia clave: setea is_admin=true en sesión y usa Auth::attempt().
     */

    /**
     * @summary Login de admin (por bandera de sesión).
     * @description
     * - Setea is_admin=true en sesión.
     * - Valida credenciales.
     * - Intenta Auth::attempt() (con remember opcional).
     * - Regenera sesión si autentica.
     * - Si falla, vuelve atrás con error en email.
     *
     * @view resources/js/Pages/AdminLogin.tsx
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse Redirect a '/Advertisement' o back con error.
     */
    public function adminLogin(Request $request): RedirectResponse
    {
        $request->session()->put('is_admin', true);

        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = (bool) $request->input('remember', false);

        if (!Auth::attempt($request->only('email', 'password'), $remember)) {
            return back()->withErrors([
                'email' => 'Credenciales incorrectas.',
            ])->withInput();
        }

        $request->session()->regenerate();

        return redirect()->intended('/Advertisement');
    }

    /*
     |--------------------------------------------------------------------------
     | PUBLICACIONES (LISTADO GENERAL)
     |--------------------------------------------------------------------------
     | - Vista: resources/js/Pages/Advertisement.tsx
     | - Fuente: session('created_ads', [])
     | - Envía: createdAds + filters + isAdmin
     */

    /**
     * @summary Listado general de publicaciones.
     * @description Renderiza la vista principal de publicaciones usando datos en sesión.
     *
     * @source session('created_ads', [])
     * @view resources/js/Pages/Advertisement.tsx
     * @param  \Illuminate\Http\Request  $request (query params opcionales)
     * @return \Inertia\Response
     */
    public function advertisementIndex(Request $request): Response
    {
        $createdAds = $request->session()->get('created_ads', []);
        $filters = $this->parseAdvertisementFilters($request);
        $isAdmin = (bool) $request->session()->get('is_admin', false);

        return Inertia::render('Advertisement', [
            'createdAds' => $createdAds,
            'filters' => $filters,
            'isAdmin' => $isAdmin,
        ]);
    }

    /**
     * @summary Sanitiza filtros de publicaciones desde la URL.
     * @description Evita valores inválidos en filtros (type/sort/distance).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array{q:string,type:string,sort:string,distance:string}
     */
    private function parseAdvertisementFilters(Request $request): array
    {
        $q = (string) $request->query('q', '');
        $type = (string) $request->query('type', 'all');
        $sort = (string) $request->query('sort', 'newest');
        $distance = (string) $request->query('distance', 'Cualquiera');

        if (!in_array($type, ['all', 'Servicio', 'Producto'], true)) $type = 'all';
        if (!in_array($sort, ['newest', 'oldest'], true)) $sort = 'newest';
        if (!in_array($distance, ['Cualquiera', '1', '5', '10', '20', '30', '50'], true)) $distance = 'Cualquiera';

        return [
            'q' => $q,
            'type' => $type,
            'sort' => $sort,
            'distance' => $distance,
        ];
    }

    /*
     |--------------------------------------------------------------------------
     | MIS PUBLICACIONES (LISTADO)
     |--------------------------------------------------------------------------
     | - Vista: resources/js/Pages/MyAdvertisements.tsx
     | - Fuente: session('created_ads', [])
     | - El filtrado por dueño lo hace el Front (auth.user.name).
     */

    /**
     * @summary Listado de "Mis publicaciones".
     * @description Renderiza la vista que muestra publicaciones del usuario (front filtra).
     *
     * @source session('created_ads', [])
     * @view resources/js/Pages/MyAdvertisements.tsx
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function myAdvertisementsIndex(Request $request): Response
    {
        $createdAds = $request->session()->get('created_ads', []);

        return Inertia::render('MyAdvertisements', [
            'createdAds' => $createdAds,
        ]);
    }

    /*
     |--------------------------------------------------------------------------
     | PUBLICACIÓN (DETALLE)
     |--------------------------------------------------------------------------
     | - Vista: resources/js/Pages/AdvertisementShow.tsx
     | - Fuente: session('created_ads', [])
     */

    /**
     * @summary Detalle de publicación.
     * @description Busca una publicación por id dentro de session('created_ads') y la envía al front.
     *
     * @source session('created_ads', [])
     * @view resources/js/Pages/AdvertisementShow.tsx
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Inertia\Response item=null si no existe.
     */
    public function advertisementShow(Request $request, int $id): Response
    {
        $createdAds = $request->session()->get('created_ads', []);
        $item = collect($createdAds)->firstWhere('id', (int) $id);

        return Inertia::render('AdvertisementShow', [
            'item' => $item ?: null,
        ]);
    }

    /*
     |--------------------------------------------------------------------------
     | PUBLICACIÓN (EDITAR FORM)
     |--------------------------------------------------------------------------
     | - Vista: resources/js/Pages/EditarPublicacion.tsx
     | - Fuente: session('created_ads', [])
     | - Guardado real: PATCH /Advertisement/{id} en routes/web.php
     */

    /**
     * @summary Formulario de edición de publicación.
     * @description Carga una publicación por id desde sesión para editarla en el front.
     *
     * @source session('created_ads', [])
     * @view resources/js/Pages/EditarPublicacion.tsx
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Inertia\Response item=null si no existe.
     */
    public function advertisementEdit(Request $request, int $id): Response
    {
        $createdAds = $request->session()->get('created_ads', []);
        $item = collect($createdAds)->firstWhere('id', (int) $id);

        return Inertia::render('EditarPublicacion', [
            'item' => $item ?: null,
        ]);
    }

    /*
     |--------------------------------------------------------------------------
     | CREAR PUBLICACIÓN (FORM)
     |--------------------------------------------------------------------------
     | - Vista: resources/js/Pages/CreateAdvertisement.tsx
     | - Si is_admin=true -> envía users (id,name) para asignar autor.
     | - Si is_admin=false -> envía users=[].
     */

    /**
     * @summary Formulario de creación de publicación.
     * @description Si el usuario tiene is_admin en sesión, carga la lista de users para asignación.
     *
     * @source session('is_admin') + tabla users (solo id,name)
     * @view resources/js/Pages/CreateAdvertisement.tsx
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function advertisementCreate(Request $request): Response
    {
        $isAdmin = (bool) $request->session()->get('is_admin', false);

        $users = [];

        if ($isAdmin) {
            $users = User::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get()
                ->toArray();
        }

        return Inertia::render('CreateAdvertisement', [
            'users' => $users,
        ]);
    }

    /*
     |--------------------------------------------------------------------------
     | DASHBOARD
     |--------------------------------------------------------------------------
     | - Vista: resources/js/Pages/Dashboard.tsx
     | - Fuente: session('created_ads', [])
     | - Construye stats por estado para "mis publicaciones" + total global active.
     */

    /**
     * @summary Vista de Dashboard.
     * @description Calcula estadísticas del usuario desde publicaciones almacenadas en sesión.
     *
     * @source session('created_ads', []) + auth user
     * @view resources/js/Pages/Dashboard.tsx
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function dashboard(Request $request): Response
    {
        $createdAds = $request->session()->get('created_ads', []);
        $stats = $this->buildDashboardStats($request, $createdAds);

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'unreadMessageCount' => $stats['badges']['unreadMessages'] ?? 0,
            'unreadNotificationCount' => $stats['badges']['unreadNotifications'] ?? 0,
        ]);
    }

    /**
     * @summary Construye estadísticas del Dashboard.
     * @description
     * - Normaliza status (active/paused/finished).
     * - Filtra publicaciones del usuario actual (por user_id si existe, si no por author).
     * - Cuenta totales por estado y total global active.
     *
     * @notes
     * - badges quedan como placeholder (0) por ahora.
     * - Los datos provienen de sesión, no de BD.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $createdAds
     * @return array<string,mixed>
     */
    private function buildDashboardStats(Request $request, array $createdAds): array
    {
        $user = $request->user();

        $normalizeStatus = function ($x) {
            $s = $x['status'] ?? 'active';
            return in_array($s, ['active', 'paused', 'finished'], true) ? $s : 'active';
        };

        $ads = array_map(function ($x) use ($normalizeStatus) {
            $x['status'] = $normalizeStatus($x);
            $x['author'] = $x['author'] ?? null;
            $x['user_id'] = $x['user_id'] ?? null;
            return $x;
        }, $createdAds);

        $myAds = array_values(array_filter($ads, function ($x) use ($user) {
            if (!$user) return false;

            if (isset($x['user_id']) && $x['user_id'] !== null) {
                return (int)$x['user_id'] === (int)$user->id;
            }

            if (isset($x['author']) && $x['author'] !== null) {
                return (string)$x['author'] === (string)$user->name;
            }

            return false;
        }));

        $countByStatus = function (array $list, string $status) {
            $c = 0;
            foreach ($list as $x) {
                if (($x['status'] ?? 'active') === $status) $c++;
            }
            return $c;
        };

        $myTotal = count($myAds);
        $myAvailable = $countByStatus($myAds, 'active');
        $mySoldOut = $countByStatus($myAds, 'finished');
        $myDisabled = $countByStatus($myAds, 'paused');

        $adsActiveTotal = $countByStatus($ads, 'active');

        return [
            'me' => [
                'name' => $user?->name,
            ],
            'counts' => [
                'myAdsTotal' => $myTotal,
                'myAdsAvailable' => $myAvailable,
                'myAdsSoldOut' => $mySoldOut,
                'myAdsDisabled' => $myDisabled,
                'adsActiveTotal' => $adsActiveTotal,
            ],
            'badges' => [
                'unreadMessages' => 0,
                'unreadNotifications' => 0,
            ],
        ];
    }
}