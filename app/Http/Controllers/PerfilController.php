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

    /**
     * @summary Sube y registra una imagen de perfil.
     * @description Comprueba que el usuario esté autenticado, valida el
     * archivo recibido y lo almacena en el directorio storage/app/uploads.
     * Posteriormente, registra la imagen en la base de datos asociándola
     * al usuario indicado en la URL mediante el método saveImagen().
     * Finalmente, devuelve los datos de la imagen junto con un nuevo token
     * CSRF en una respuesta JSON.
     * 
     * @source Directorio storage/app/uploads y método saveImagen().
     * @route Ruta de subida de impagenes definida en routes/web.php
     * @param \Illuminate\Http\Request $request
     * @param int $usuario_id Identificador del usuario al que se asociará la
     * imágen.
     * @return \Illuminate\Http\JsonResponse
     * 
     * @throws \Symphony\Component\Http\Kernel\Exception\HttpException Si no
     * existe un usuario autenticado (401).
     */
    // Archivos vinculados: web.php, subirImagen.ts, actualizarCSRFToken.ts, FormEditarPerfil.tsx, EditarPerfil.tsx, usarEstadoFormulario.ts, datosMockEditarPerfil.ts.
    // Visuales conectadas: Editar Perfil.
    public function subirImagen(Request $request, $usuario_id)
    {
        $usuarioActual = $request->user();
        if (!$usuarioActual) {
            abort(401, 'No autenticado');
        }

        // Valida
        $validated = $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Guarda
        $image = $request->file('image');
        $filename = uniqid() . '.' . $image->extension();
        
        try {
            // Crear carpeta si no existe
            $uploadDir = storage_path('app/uploads');
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }
            
            // Guarda el archivo
            $image->move($uploadDir, $filename);
            
            \Log::info('Imagen perfil guardada exitosamente', [
                'filename' => $filename,
                'usuario_id' => $usuario_id,
                'path' => $uploadDir . DIRECTORY_SEPARATOR . $filename,
                'exists' => file_exists($uploadDir . DIRECTORY_SEPARATOR . $filename),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error al guardar imagen perfil', [
                'error' => $e->getMessage(),
                'filename' => $filename,
                'usuario_id' => $usuario_id,
                'uploadDir' => $uploadDir ?? 'undefined',
            ]);
            return response()->json(['error' => 'Error al guardar imagen: ' . $e->getMessage()], 500);
        }

        // Guardar en BD con el usuario_id de la URL
        $imagenGuardada = $this->saveImagen($filename, (int) $usuario_id);

        // Devolver imagen + CSRF token actualizado
        $imagenGuardada['csrf_token'] = csrf_token();
        
        return response()->json($imagenGuardada);
    }


    /**
     * @summary Muestra el perfil de un usuario.
     *
     * @description
     * - Determina si se mostrará el perfil del usuario autenticado o el de otro usuario.
     * - Restringe el acceso a perfiles ajenos a los administradores.
     * - Comprueba que el usuario solicitado exista y determina si tiene permisos de administrador.
     * - Obtiene la imagen de perfil más reciente o utiliza una imagen de reemplazo si no existe.
     * - Si el usuario consultado no es administrador, obtiene sus publicaciones mediante el procedimiento almacenado
     *   obtener_publicaciones_perfil_por_usuario_id y adapta sus datos al formato esperado por el frontend.
     * - Incluye los datos del usuario, sus publicaciones y los indicadores necesarios para mostrar el perfil propio o ajeno.
     * - La popularidad de las publicaciones está pendiente de implementar y actualmente se devuelve como null.
     *
     * @source Tablas users, Tipo_Usuario e imagens; procedimiento almacenado
     *         obtener_publicaciones_perfil_por_usuario_id.
     * @view resources/js/Pages/VerPerfil.tsx
     * @route Definida en routes/web.php.
     *
     * @param \Illuminate\Http\Request $request Solicitud HTTP con el usuario autenticado y la sesión actual.
     * @param int|null $id ID del usuario cuyo perfil se desea consultar; si se omite, se muestra el perfil propio.
     *
     * @return \Inertia\Response Página VerPerfil con los datos del usuario, sus publicaciones y los indicadores de permisos.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Si un usuario no administrador intenta acceder
     *         al perfil de otra persona (403).
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException Si el usuario solicitado no existe (404).
     */
    public function verPerfil(Request $request, $id = null)
    {
        $usuario_logueado = $request->user();
        $isAdminLogueado = (bool) $request->session()->get('is_admin', false);
        
        // Determina qué usuario ver
        if ($id) {
            // Si el usuario intenta ver un perfil diferente al suyo y no es admin, error
            if (!$isAdminLogueado && $id != $usuario_logueado->id) {
                abort(403, 'Solo administradores pueden ver otros perfiles');
            }
            $usuarioAVer = \DB::table('users')->find($id);
            if (!$usuarioAVer) {
                abort(404, 'Usuario no encontrado');
            }
        } else {
            // Ver perfil propio
            $usuarioAVer = $usuario_logueado;
        }

        // Verificar si el usuario a ver es admin
        $esAdminUsuarioAVer = \App\Models\Tipo_Usuario::where('id_usuario', $usuarioAVer->id)->exists();

        // Obtener imagen de perfil
        $imagenUsuario = \DB::table('imagens')
            ->where('id_usuario_origen', $usuarioAVer->id)
            ->where('Tipo', 0)
            ->orderBy('created_at', 'desc')
            ->first();

        $fotoUrl = $imagenUsuario 
            ? '/imagen/' . $imagenUsuario->Direccion 
            : 'https://picsum.photos/200?random=' . $usuarioAVer->id;

       
        $publicaciones = [];
        if (!$esAdminUsuarioAVer) {

            /*
            // Subquery para obtener la primera imagen de cada publicación
            $imagenSubquery = \DB::table('inter__imagen__public')
                ->join('imagens', 'inter__imagen__public.id_imagen', '=', 'imagens.id')
                ->where('imagens.Tipo', 1)
                ->selectRaw('inter__imagen__public.id_publicacion, MAX(imagens.Direccion) as Direccion')
                ->groupBy('inter__imagen__public.id_publicacion');

            $publicaciones = \App\Models\Publicacion::where('publicacions.id_usuario', $usuarioAVer->id)
                ->where('publicacions.visible', 1)
                ->leftJoin('ofertas', 'publicacions.id', '=', 'ofertas.id_publicacion')
                ->leftJoin('demandas', 'publicacions.id', '=', 'demandas.id_publicacion')
                ->leftJoinSub($imagenSubquery, 'imagen_primera', function($join) {
                    $join->on('publicacions.id', '=', 'imagen_primera.id_publicacion');
                })
                ->select(
                    'publicacions.id',
                    'publicacions.Nombre',
                    'publicacions.Descripcion',
                    'publicacions.Tipo_Publicacion',
                    'publicacions.Tipo_Beneficio',
                    'publicacions.created_at',
                    'ofertas.Precio',
                    'demandas.Presupuesto',
                    'imagen_primera.Direccion as imagenDireccion'
                )
                */

            $publicaciones = \DB::select(
                'CALL obtener_publicaciones_perfil_por_usuario_id(?)',
                [$usuarioAVer->id]
            );

            

            $publicaciones = collect($publicaciones)
                //->get()
                ->map(fn($pub) => [
                    'id' => $pub->id,
                    'titulo' => $pub->Nombre,
                    'descripcion' => $pub->Descripcion,
                    'costo' => $pub->Precio !== null ? ($pub->Precio ?? 0) : ($pub->Presupuesto ?? 0),
                    'tipo' => (int) $pub->Tipo_Beneficio === 0 ? 'Servicio' : 'Producto',
                    'ofertaDemanda' => $pub->Precio !== null ? 'Oferta' : 'Demanda',
                    'imagen' => $pub->imagenDireccion ? '/imagen/' . $pub->imagenDireccion : 'https://picsum.photos/200?random=' . $pub->id,
                    'estado' => 'Activo',
                    'fecha' => Carbon::parse($pub->created_at)->format('Y-m-d'),
                    'popularidad' => null,
                ])
                ->toArray();
        }

        return Inertia::render('VerPerfil', [
            'datosUsuario' => [
                'id' => $usuarioAVer->id,
                'nombre' => $usuarioAVer->name ?? '',
                'apellidoPaterno' => $usuarioAVer->Apellido_Paterno ?? '',
                'apellidoMaterno' => $usuarioAVer->Apellido_Materno ?? '',
                'ciudad' => $usuarioAVer->Ciudad ?? '',
                'telefono' => $usuarioAVer->Telefono ?? '',
                'direccion' => $usuarioAVer->Direccion ?? '',
                'email' => $usuarioAVer->email ?? '',
                'fechaNacimiento' => $usuarioAVer->Age ?? '',
                'saldoHoras' => $usuarioAVer->saldo ?? 0,
                'foto' => $fotoUrl,
            ],
            'publicaciones' => $publicaciones,
            'isAdmin' => $isAdminLogueado,
            'esAdminUsuarioAVer' => $esAdminUsuarioAVer,
            'esPerfilPropio' => !$id, 
        ]);
    }
}
