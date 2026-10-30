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

    /**
     * @summary Actualiza los datos del perfil de un usuario.
     * @description Determina qué usuario se actualizará y comprueba los permisos
     * necesarios para modificar su perfil. Valida los datos recibidos, verifica
     * la confirmación de la contraseña cuando se proporciona una nueva, gestiona
     * la eliminación de la imagen de perfil anterior y actualiza la información
     * del usuario en la base de datos.
     * Finalmente, redirige al usuario según el contexto desde el que se realizó
     * la edición.
     *
     * @source Sesión (`is_admin`), tabla `users` y tabla `imagens`.
     * @route Ruta de actualización del perfil definida en routes/web.php.
     * @param  \Illuminate\Http\Request  $request
     * @param  int|null  $id Identificador del usuario que se desea actualizar. Si es null, se actualiza el perfil del usuario autenticado.
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Si un usuario sin permisos intenta modificar otro perfil (403).
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException Si el usuario solicitado no existe (404).
     */
    public function guardarPerfil(Request $request, $id = null)
    {
        $isAdmin = (bool) $request->session()->get('is_admin', false);
        $usuarioActual = $request->user();
        $usuarioEditando = $usuarioActual;
        $returnTo = $request->query('return_to', 'verperfil');

        
        if ($id !== null) {
            if (!$isAdmin) {
                abort(403, 'No tienes permiso para editar perfiles de otros usuarios');
            }
            $usuarioEditando = \DB::table('users')->find($id);
            if (!$usuarioEditando) {
                abort(404, 'Usuario no encontrado');
            }
        }

        $rules = [
            'nombre' => ['required', 'string', 'max:255'],
            'apellidoPaterno' => ['nullable', 'string', 'max:255'],
            'apellidoMaterno' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $usuarioEditando->id],
            'fechaNacimiento' => ['nullable', 'string'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:255'],
            'contrasena' => ['nullable', 'string', 'min:8'],
            'confirmarContrasena' => ['nullable', 'string'],
            'id_imagen' => ['nullable', 'integer', 'exists:imagens,id'],
        ];

        if ($request->filled('contrasena')) {
            $rules['confirmarContrasena'][] = 'required';
        }

        $validated = $request->validate($rules);

        if ($request->filled('contrasena') && $request->filled('confirmarContrasena')) {
            if ($validated['contrasena'] !== $validated['confirmarContrasena']) {
                return back()->withErrors(['contrasena' => 'Las contraseñas no coinciden.']);
            }
        }

        // Si se envió una nueva imagen, eliminar la anterior del usuario (solo imágenes de perfil, Tipo=0)
        if ($request->filled('id_imagen')) {
            $imagenAnterior = \DB::table('imagens')
                ->where('id_usuario_origen', $usuarioEditando->id)
                ->where('Tipo', 0) // Solo imágenes de perfil
                ->where('id', '!=', $validated['id_imagen'])
                ->first();
            
            if ($imagenAnterior) {
                \DB::table('imagens')->where('id', $imagenAnterior->id)->delete();
            }
        }

        $updateData = [
            'name' => $validated['nombre'],
            'Apellido_Paterno' => $validated['apellidoPaterno'] ?? '',
            'Apellido_Materno' => $validated['apellidoMaterno'] ?? '',
            'email' => $validated['email'],
            'Age' => $validated['fechaNacimiento'] ?? '',
            'Telefono' => $validated['telefono'] ?? '',
            'Direccion' => $validated['direccion'] ?? '',
            'Ciudad' => $validated['ciudad'] ?? '',
        ];

        if ($request->filled('contrasena')) {
            $updateData['password'] = bcrypt($validated['contrasena']);
        }

        \DB::table('users')->where('id', $usuarioEditando->id)->update($updateData);

        
        if ($returnTo === 'administrar') {
            return redirect()->route('administrar-usuarios');
        }
        
        // Si se estaba editando otro usuario, redirigir a su perfil. Si no, al perfil propio.
        if ($id !== null) {
            return redirect("/verperfil/{$usuarioEditando->id}");
        }
        return redirect()->route('verperfil');
    }
}
