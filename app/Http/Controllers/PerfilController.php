<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PerfilController extends Controller
{
    /**
     * @summary Muestra el formulario de edición de perfil.
     * @description Permite al usuario autenticado editar su propio perfil o,
     * si tiene permisos de administrador, seleccionar y editar el perfil
     * de otro usuario.
     * Comprueba los permisos de acceso, verifica la existencia del usuario
     * solicitado, obtiene su fotografía de perfil y prepara sus datos
     * para el frontend.
     * 
     * @source Sesión (is_admin), tabla users y tabla imagens
     * @view resources/js/Pages/EditarPerfil.tsx
     * @param \Illuminate\Http\Request $request
     * @param int|null $id Identificador del usuario que se desea editar.
     * Si es null, se utiliza el usuario autenticado.
     * @return \Inertia\Response
     */
    public function editarPerfil(Request $request, $id = null)
    {
        $isAdmin = (bool) $request->session()->get('is_admin', false);
        $usuarioEditando = $request->user();
        $returnTo = $request->query('return_to', 'verperfil');

        
        if ($id !== null) {
            if (!$isAdmin) {
                abort(403, 'No tienes permiso para editar perfiles de otros usuarios');
            }
            // Buscar el usuario a editar en BD. Si no existe lanza error 404
            $usuarioEditando = \DB::table('users')->find($id);
            if (!$usuarioEditando) {
                abort(404, 'Usuario no encontrado');
            }
        }

        // Obtener imagen de perfil del usuario
        $imagenUsuario = \DB::table('imagens')
            ->where('id_usuario_origen', $usuarioEditando->id)
            ->where('Tipo', 0)
            ->orderBy('created_at', 'desc')
            ->first();

        $fotoUrl = $imagenUsuario 
            ? '/imagen/' . $imagenUsuario->Direccion 
            : 'https://picsum.photos/200?random=' . $usuarioEditando->id;

        return Inertia::render('EditarPerfil', [
            'datosUsuario' => [
                'id' => $usuarioEditando->id ?? null,
                'nombre' => $usuarioEditando->name ?? '',
                'apellidoPaterno' => $usuarioEditando->Apellido_Paterno ?? '',
                'apellidoMaterno' => $usuarioEditando->Apellido_Materno ?? '',
                'email' => $usuarioEditando->email ?? '',
                'contrasena' => '',
                'confirmarContrasena' => '',
                'fechaNacimiento' => $usuarioEditando->Age ?? '',
                'telefono' => $usuarioEditando->Telefono ?? '',
                'direccion' => $usuarioEditando->Direccion ?? '',
                'ciudad' => $usuarioEditando->Ciudad ?? '',
            ],
            'fotoInicial' => $fotoUrl,
            'returnTo' => $returnTo,
        ]);
    }
}
