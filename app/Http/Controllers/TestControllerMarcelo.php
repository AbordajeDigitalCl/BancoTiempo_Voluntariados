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
     register (Usuario) - Vista y Acción
     vista: resources/js/Pages/Auth/Register.tsx
    
     migracion (users):
      name: string
      Apellido_Paterno: string
      Apellido_Materno: string
      Age: date (fecha nacimiento)
      Telefono: string
      Direccion: string
      Ciudad: string
      email: string unique
      password: string + confirmation

      profile_photo: File|null
     preguntar a angel sobre la columna de foto de perfil.
    */

    public function showRegister(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function register(Request $request): RedirectResponse
    {
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
         Cuando se cree una columna (ej: profile_photo_path),
         Guardar con:
         $path = $request->file('profile_photo')->store('profiles', 'public');
         y se guarda en el user.
        */

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

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /*
     login (Usuario) - vista y accion
     Vista: resources/js/Pages/Auth/Login.tsx
    
     Shape esperado por LoginRequest:
      email: required|email
      password: required|string
      remember: boolean (opcional)
    */

    public function showLogin(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => \Illuminate\Support\Facades\Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /*
     LOGOUT (Usuario)
    */

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /*
    admin register - accion
     Vista: resources/js/Pages/AdminRegister.tsx
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
     admin login - accion
     Vista: resources/js/Pages/AdminLogin.tsx
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
     publicaciones - vista dinamica
     vista: resources/js/Pages/Advertisement.tsx

     Props que consume Advertisement.tsx:
      createdAds: array (session)
      isAdmin: boolean (session is_admin)
      filters?: { q, type, sort, distance } (opcional)

     Fuente actual:
      session('created_ads', []) -> se llena en routes/web.php en POST /Advertisement
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

    /*
       ajustes pequeños a los filtros desde la url
       evita valores inválidos
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
     mis publicaciones (LISTADO) - vista dinamica
     vista: resources/js/Pages/MyAdvertisements.tsx

     Props MyAdvertisements.tsx:
      createdAds: array (session)
      (el front filtra por auth.user.name)

     fuente actual:
     session('created_ads', []) -> se llena en routes/web.php en POST /Advertisement
     */
    public function myAdvertisementsIndex(Request $request): Response
    {
        $createdAds = $request->session()->get('created_ads', []);

        return Inertia::render('MyAdvertisements', [
            'createdAds' => $createdAds,
        ]);
    }

    /*
     publicacion (DETALLE) - vista dinamica
     vista: resources/js/Pages/AdvertisementShow.tsx

     Props esperadas:
      item: object|null (publicacion encontrada por id)
     Fuente actual:
      session('created_ads', [])
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
     publicacion (EDITAR FORM) - vista dinamica
     vista: resources/js/Pages/EditarPublicacion.tsx

     Props esperadas:
      item: object|null (publicacion encontrada por id)
     Fuente actual:
      session('created_ads', [])
      La accion de guardar cambios sigue en web.php (PATCH /Advertisement/{id})
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
     crear publicacion (FORM) - vista dinamica
     vista: resources/js/Pages/CreateAdvertisement.tsx

     Props esperadas por CreateAdvertisement.tsx:
      users: array<{ id:number, name:string }>
        - solo si el usuario es admin
        - si no , vacio -> array vacio

     fuente actual:
      tabla users (App\Models\User)
      session('is_admin') -> determina si puede crear publicaciones para otros usuarios

     uso:
      admin:
        puede crear publicacion para cualquier usuario

      usuario normal:
        solo puede crear para si mismo (selector users llega vacio)
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
     dashboard - vista dinamica
     vista: resources/js/Pages/Dashboard.tsx

     shape esperado por Dashboard.tsx:
      stats: {
        counts: {
          myAdsTotal: number
          myAdsAvailable: number (status active)
          myAdsSoldOut: number (status finished)
          myAdsDisabled: number (status paused)
          adsActiveTotal: number (total global active)
        },
        badges: {
          unreadMessages: number
          unreadNotifications: number
        }
      }

     fuente actual de publicaciones:
      session('created_ads', []) creado en routes/web.php (Advertisement.store)
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
