<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tipo_Usuario;
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
use Illuminate\Support\Facades\Validator;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'Apellido_Paterno' => 'required|string|max:255',
            'Apellido_Materno' => 'required|string|max:255',
            'Age' => 'required|string|max:255',
            'Telefono' => 'required|string|max:15',
            'Direccion' => 'required|string|max:255',
            'Ciudad' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
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

        return redirect(RouteServiceProvider::HOME);
    }

    /**
     * Handle an incoming registration request for a third party.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storeOther(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'Apellido_Paterno' => 'required|string|max:255',
            'Apellido_Materno' => 'required|string|max:255',
            'Age' => 'required|string|max:255',
            'Telefono' => 'required|string|max:15',
            'Direccion' => 'required|string|max:255',
            'Ciudad' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
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

        //Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
        //return 2;
    }

    /**
     * Handle an incoming registration request from an API/Rest call.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storeApi(Request $request)
    {
        // $request->validate([
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'Apellido_Paterno' => 'required|string|max:255',
            'Apellido_Materno' => 'required|string|max:255',
            'Age' => 'required|string|max:255',
            'Telefono' => 'required|string|max:15',
            'Direccion' => 'required|string|max:255',
            'Ciudad' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            //password_confirmation : asi se llama el campo de confirmacion
        ]);
        // Check validation failure
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json($error, 500);

            // ->withErrors($validator)
            // ->withInput();
        }


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
        Tipo_Usuario::create([
            'id_usuario' => $user->id,
            'Nombre_Tipo_Usuario' => "cliente",
            'Etiqueta' => 1
        ]);

        event(new Registered($user));

        //Auth::login($user);

        return response()->json($user, 200);
    }
}
