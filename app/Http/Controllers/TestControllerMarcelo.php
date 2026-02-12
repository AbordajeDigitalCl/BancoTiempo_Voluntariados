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
     REGISTER (Usuario) - Vista y Acción
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
     LOGIN (Usuario) - Vista y Acción
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
}


    /*
     DASHBOARD - vista dinamica
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

