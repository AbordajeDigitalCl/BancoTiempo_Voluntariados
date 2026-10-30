<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminUsuariosController extends Controller
{
    /**
     * @summary Muestra la página de administración de usuarios.
     * @description
     * - Verifica que el usuario autenticado tenga permisos de administrador mediante la variable de sesión `is_admin`.
     * - Rechaza el acceso con un error HTTP 403 si el usuario no tiene dichos permisos.
     * - Consulta los usuarios registrados en la base de datos, incluyendo sus datos personales y de contacto.
     * - Obtiene la dirección de la imagen de perfil más reciente de cada usuario (Tipo = 0).
     * - Asigna una imagen de reemplazo cuando el usuario no tiene una fotografía de perfil.
     * - Comprueba si cada usuario tiene privilegios de administrador mediante la tabla `Tipo_Usuario`.
     * - Transforma los resultados al formato esperado por el componente de React.
     * - Renderiza la página `AdministrarUsuarios` mediante Inertia, enviando la lista de usuarios y el estado de administrador.
     *
     * @source
     * - Tabla `users`: información de los usuarios registrados.
     * - Tabla `imagens`: direcciones de las imágenes de perfil.
     * - Tabla `Tipo_Usuario`: identificación de los usuarios administradores.
     *
     * @view resources/js/Pages/AdministrarUsuarios.tsx
     * @frontend
     * - AdministrarUsuarios.tsx
     * - FilaUsuario.tsx
     * - AuthenticatedLayout.tsx
     * - AdminHome.tsx
     * - VerPerfilAdmin.tsx
     *
     * @param Request $request Petición HTTP que permite acceder a los datos de sesión del usuario.
     * @return \Inertia\Response Página de administración de usuarios con sus datos.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Si el usuario no tiene permisos de administrador (403).
     */
    public function administrarUsuarios(Request $request)
    {
        $isAdmin = (bool) $request->session()->get('is_admin', false);

        if (!$isAdmin) {
            abort(403, 'Solo administradores pueden acceder a esta página');
        }

        /*
        $usuarios = \DB::table('users')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function($user) {
                // Obtener la imagen más reciente del usuario 
                // Filtrar por Tipo=0 (imágenes de perfil, no de publicación)
                $imagenUsuario = \DB::table('imagens')
                    ->where('id_usuario_origen', $user->id)
                    ->where('Tipo', 0) // Solo imágenes de perfil
                    ->orderBy('created_at', 'desc')
                    ->first();

                // URL de la foto: si existe, usar la real, si no usar placeholder
                $fotoUrl = $imagenUsuario 
                    ? '/imagen/' . $imagenUsuario->Direccion 
                    : 'https://picsum.photos/200?random=' . $user->id;

                // Verificar si es administrador consultando la tabla Tipo_Usuario
                $esAdmin = (bool) Tipo_Usuario::where('id_usuario', $user->id)->exists();

                return [
                    'id' => $user->id,
                    'nombre' => $user->name ?? '',
                    'apellidoPaterno' => $user->Apellido_Paterno ?? '',
                    'apellidoMaterno' => $user->Apellido_Materno ?? '',
                    'ciudad' => $user->Ciudad ?? '',
                    'direccion' => $user->Direccion ?? '',
                    'email' => $user->email ?? '',
                    'foto' => $fotoUrl,
                    'esAdmin' => (int) $esAdmin, // Convertir a int para evitar problemas de serialización
                ];
            })
            ->toArray();
        */

        $usuarios = \DB::table('users as u')
        ->select([
            'u.id',
            'u.name',
            'u.Apellido_Paterno',
            'u.Apellido_Materno',
            'u.Ciudad',
            'u.Direccion',
            'u.email',
        ])
        ->selectSub(
            \DB::table('imagens as i')
            ->select('i.Direccion')
            ->where('i.Tipo', 0)
            ->orderByDesc('i.id')
            ->limit(1),
            'fotoDireccion'
        )
        ->selectRaw('
            EXISTS(
                SELECT 1
                FROM Tipo_Usuario tu
                WHERE tu.id_usuario = u.id
            ) AS esAdmin
        ')
        ->orderBy('u.id', 'asc')
        ->get()
        ->map(function ($user) {
            $fotoUrl = $user->fotoDireccion
                ? '/imagen/' . $user->fotoDireccion
                : 'https://picsum.photos/200?random=' . $user->id;

            return [
                'id' => $user->id,
                'nombre' => $user->name ?? '',
                'apellidoPaterno' => $user->Apellido_Paterno ?? '',
                'apellidoMaterno' => $user->Apellido_Materno ?? '',
                'ciudad' => $user->Ciudad ?? '',
                'direccion' => $user->Direccion ?? '',
                'email' => $user->email ?? '',
                'foto' => $fotoUrl,
                'esAdmin' => (int) $user->esAdmin,
            ];
        })
        ->toArray();

            
        return Inertia::render('AdministrarUsuarios', [
            'usuarios' => $usuarios,
            'isAdmin' => $isAdmin,
        ]);
    }
}
