<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Cruds;
use App\Models\Tipo_Usuario;
use Inertia\Inertia;
use Carbon\Carbon;
/*
Todos los request public que se encuentran en este archivo:

editarPerfil()
guardarPerfil()
subirImagen()
verPerfil()
editarPublicacion()
guardarPublicacion()
chat()
enviarMensaje()
enviarMensajeConImagen()
administrarUsuarios()
crearOBuscarConversacion()
eliminarUsuario()
crearPublicacion()
subirImagenPublicacion()
obtenerImagenesPublicacion()
servirImagen()
eliminarImagenPublicacion()
*/
class TestControllerCris extends Controller
{
    use Cruds;

    public function editar(Request $request)
    {
        //dd($request);
        // $result = $this->Editar_publicacions($request);
        // $result = $this->Editar_proyectos($request);
        // $result = $this->Editar_voluntario($request, $hard_delete = true);
        // $result = $this->Editar_tipo__usuarios($request);
        // $result = $this->Editar_calificacions($request);
        // $result = $this->Editar_demandas($request);
        // $result = $this->Editar_ofertas($request);
        // $result = $this->Editar_transaccions($request);
        // $result = $this->Editar_mensaje($request);
        // $result = $this->Editar_inter__calif__publics($request);
        // $result = $this->Editar_inter__transa__publics($request);
        // $result = $this->Editar_imagens($request);

        // $result = $this->Editar_usuarios($request, $hard_delete = false);
        
        // $result = "A";
        return response()->json($result, 200);
    }

    public function obtener(Request $request)
    {
        // $result = $this->Obtener_publicacions($request, $use_for = 0, $offset = 0, $ammount = 20);
        // $result = $this->Obtener_proyectos($request, $use_for = '-', $offset = 0, $ammount = 20);
        // $result = $this->Obtener_voluntario($request, $use_for = 0, $offset = 0, $ammount = 20);
        // $result = $this->Obtener_tipo__usuarios($request, $use_for = 0, $offset = 0, $ammount = 20);
        // $result = $this->Obtener_calificacions($request, $use_for = 0, $offset = 0, $ammount = 20);
        // $result = $this->Obtener_demandas($request, $use_for = 0, $offset = 0, $ammount = 20);
        // $result = $this->Obtener_ofertas($request, $use_for = 0, $offset = 0, $ammount = 20);
        // $result = $this->Obtener_transaccions($request, $use_for = '-', $offset = 0, $ammount = 20);
        // $result = $this->Obtener_mensaje($request, $use_for = 0, $offset = 0, $ammount = 20);
        // $result = $this->Obtener_inter__calif__publics($request, $use_for = 0, $offset = 0, $ammount = 20);
        // $result = $this->Obtener_inter__transa__publics($request, $use_for = '-', $offset = 0, $ammount = 20);
        // $result = $this->Obtener_imagens($request, $use_for = 0, $offset = 0, $ammount = 20);

        // $result = $this->Obtener_usuarios($request, $use_for = '-', $offset = 0, $ammount = 20);
        
        // $result = $this->Revisar_Acceso(1, 1);
        // $result = "A";
        // return response()->json(is_numeric($request->input('Nombre_Tipo_Usuario')), 200);
        return response()->json($result, 200);
    }
    
    // Mostrar formulario para editar perfil del usuario actual o de otro usuario (solo admins)
    // Archivos vinculados: Web.php, EditarPerfil.tsx, VerPerfil.tsx, FormEditarPerfil.tsx, PerfilInfo.tsx, FilaUsuario.tsx, guardarDatosPerfil.ts, usarEstadoFormulario.ts, datosMockEditarPerfil.ts, EntradaContrasena.tsx (indirecta), coloresEstado.ts (Solo es mapeo de colores del estado), PerfilAdmin.tsx (Indirecto).
    // Visuales conectadas: Administración de usuarios, editar perfil y ver perfil.
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

    // Guarda cambios del perfil del usuario en BD (valida permisos, contraseña, imagen anterior)
    // Archivos vinculados: web.php, guardarDatosPerfil.ts, subirImagen.ts, FormEditarPerfil.tsx, EditarPerfil.tsx, usarEstadoFormulario.ts, datosMockEditarPerfil.ts, EntradaContrasena.tsx (Indirecta), coloresEstado.ts (Mapeo de colores de estado, nada más).
    // Visuales conectadas: Editar perfil, y ver perfil (Página que tiene link con editar perfil)
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

    // Subir imagen de perfil para usuario específico. Valida autenticación, retorna CSRF token
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

    // Mostrar perfil de usuario (propio o de otro si es admin). Incluye publicaciones si no es admin
    // Archivos vinculados: web.php, VerPerfil.tsx, PerfilInfo.tsx, ServiceCardListview.tsx, ServiciosDropDown.tsx, OrdenarDropDown.tsx, OfertaDropDown.tsx, MenuHamburguesa.tsx, GaleriaImagenesPublicacion.tsx, AuthenticatedLayour.tsx, Dashboard.tsx, datosMockPerfil.ts, EncabezadoMenu.tsx, datosMockEncabezadoMenu.ts, usarFiltroServicio.ts, AuthenticatedLayout.tsx, coloresEstado.ts (Solo es mapeo de colores, nada más), ServiceView.tsx.
    // Visuales conectadas: Ver perfil. (Normal y Admin)
    //Tipo_beneficio 0= Servicio, 1= Producto
    // TODO: IMPLEMENTAR POPULARIDAD - Actualmente 'popularidad' se devuelve null en todas las publicaciones.
    // Debe calcularse desde la tabla 'calificacions' contando el promedio de calificaciones por publicación.
    // Esto permitirá que el dropdown "Popular" en VerPerfil.tsx funcione correctamente.
    // Ubicación del placeholder: VerPerfil.tsx líneas 28 y 78 (especialmente línea 78 en el ordenamiento).
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

    // Mostrar formulario para editar publicación. Obtiene datos de BD (filtro Tipo=1 para imágenes de publicación, no se editó nada en el backend respecto a esto.)
    // Archivos vinculados: web.php, estadosPublicaciones.ts, EditarMiPublicacionCRIS.tsx, formEditarMiPublicacionCRIS.tsx, ServicioProductoCard.tsx, ServicioProductoCardSelectable.tsx, ServiceCardListview.tsx, ServiceCardListviewSelectable.tsx, guardarPublicacion.ts, servicioImagenesPublicacion.ts, subirImagenPublicacion.ts, MyAdvertisements.tsx, VerPerfil.tsx, EditarPublicacion.tsx, AuthenticatedLayout.tsx, usarFiltroServicio.ts, SeleccionarPublicacionEditarCRIS.tsx.
    // Visuales conectadas: Editar publicación, ver perfil (Link a Editar publicación).
    public function editarPublicacion(Request $request, $id)
    {
        $user = $request->user();
        $isAdmin = (bool) $request->session()->get('is_admin', false);
        $returnTo = $request->query('return_to', '/verperfil');
        
        /*
        // Subquery para obtener la primera imagen de la publicación
        $imagenSubquery = \DB::table('inter__imagen__public')
            ->join('imagens', 'inter__imagen__public.id_imagen', '=', 'imagens.id')
            ->where('imagens.Tipo', 1)
            ->selectRaw('inter__imagen__public.id_publicacion, MAX(imagens.Direccion) as Direccion, MAX(imagens.id) as id')
            ->groupBy('inter__imagen__public.id_publicacion');

        $publicacion = \App\Models\Publicacion::where('publicacions.id', $id);
        
        // Solo usuarios normales solo pueden editar sus propias publicaciones
        // Los admins pueden editar cualquier publicación
        if (!$isAdmin) {
            $publicacion = $publicacion->where('id_usuario', $user->id);
        }
        
        $publicacion = $publicacion
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
                'ofertas.Precio',
                'demandas.Presupuesto',
                'imagen_primera.Direccion as imagenDireccion',
                'imagen_primera.id as imagenId'
            )
            ->first();
        */

        $publicacion = collect(\DB::select(
            'CALL obtener_publicacion_para_editar(?,?,?)',
            [$id,
            $user->id,
            $isAdmin ? 1 : 0
            ]
        ))
        ->first();

        if (!$publicacion) {
            abort(404);
        }

        // CORREGIDO: Determinar Tipo_Publicacion consultando las tablas
        // Tipo_Publicacion: 0=Oferta (Precio), 1=Demanda (Presupuesto)
        $esOferta = $publicacion->Precio !== null;
        $tipoPublicacionCalculado = $esOferta ? 0 : 1;
        $saldo = $esOferta ? ($publicacion->Precio ?? 0) : ($publicacion->Presupuesto ?? 0);
        
        // Convertir números a strings de tipo para el componente frontend
        $tipoBeneficioText = (int) $publicacion->Tipo_Beneficio === 0 ? 'Servicio' : 'Producto';
        $tipoOfertaDemandaText = $esOferta ? 'Oferta' : 'Demanda';

        // NUEVO: Obtener URL de imagen real o usar placeholder
        // Usar endpoint alternativo /imagen/ para mayor compatibilidad
        $imagenUrl = $publicacion->imagenDireccion 
            ? '/imagen/' . $publicacion->imagenDireccion
            : 'https://picsum.photos/200?random=' . $publicacion->id;

        $datosPublicacion = [
            'id' => $publicacion->id,
            'titulo' => $publicacion->Nombre,
            'descripcion' => $publicacion->Descripcion,
            'costo' => $saldo,
            'tipo' => $tipoBeneficioText, 
            'ofertaDemanda' => $tipoOfertaDemandaText, 
            'imagen' => $imagenUrl,
            'imagenId' => $publicacion->imagenId, 
            'estado' => 'Activo',
            'tipoPublicacion' => $tipoPublicacionCalculado, 
            'tipoBeneficio' => (int) $publicacion->Tipo_Beneficio, 
            'Precio' => $esOferta ? $saldo : null,
            'Presupuesto' => !$esOferta ? $saldo : null,
        ];

        return Inertia::render('EditarMiPublicacionCRIS', [
            'datosPublicacion' => $datosPublicacion,
            'returnTo' => $returnTo,
        ]);
    }

    // Guardar cambios de publicación. Valida cambios entre Oferta/Demanda, actualiza tablas relacionadas
    // Archivos vinculados: web.php, estadosPublicaciones.ts, guardarPublicacion.ts, subirImagenPublicacion.ts, FormEditarMiPublicacionCRIS.tsx, ServicioProductoCard.tsx, ServicioProductoCardSelectable.tsx, ServiceCardListview.tsx, EditarMiPublicacionCRIS.tsx.
    // Visuales conectadas: Editar publicación.
    public function guardarPublicacion(Request $request, $id)
    {
        $user = $request->user();
        $isAdmin = (bool) $request->session()->get('is_admin', false);

        $query = \App\Models\Publicacion::where('id', $id);
        
        // Solo usuarios normales solo pueden editar sus propias publicaciones
        // Los admins pueden editar cualquier publicación
        if (!$isAdmin) {
            $query = $query->where('id_usuario', $user->id);
        }
        
        $publicacion = $query->first();

        dd($publicacion);

        if (!$publicacion) {
            abort(404);
        }

        // CORREGIDO: Usar el sistema correcto
        // Tipo_Publicacion: Cambia el tipo de saldo dependiendo del tipo.
        // Tipo_Beneficio: 0=Servicio, 1=Producto (cambio independiente)
        $rules = [
            'Nombre' => ['required', 'string', 'max:255'],
            'Descripcion' => ['required', 'string', 'max:255'],
            'Tipo_Beneficio' => ['required', 'integer', 'in:0,1'], // 0=Servicio, 1=Producto
            'Tipo_Publicacion' => ['required', 'integer', 'in:0,1'], // 0=Oferta, 1=Demanda
            'Precio' => ['nullable', 'integer', 'min:0'], // Para Ofertas
            'Presupuesto' => ['nullable', 'integer', 'min:0'], // Para Demandas
        ];

        $validated = $request->validate($rules);

        $tipoPublicacionAnterior = (int) $publicacion->Tipo_Publicacion;
        $tipoPublicacionNuevo = (int) $validated['Tipo_Publicacion'];
        
        $tienePrecio = $request->filled('Precio');
        $tienePresupuesto = $request->filled('Presupuesto');

        if ($tipoPublicacionNuevo === 0) {
            if (!$tienePrecio) {
                return response()->json(['message' => 'Oferta (Tipo_Publicacion=0) requiere campo "Precio"'], 400);
            }
            if ($tienePresupuesto) {
                return response()->json(['message' => 'Oferta (Tipo_Publicacion=0) NO puede tener "Presupuesto"'], 400);
            }
        } else {
            if (!$tienePresupuesto) {
                return response()->json(['message' => 'Demanda (Tipo_Publicacion=1) requiere campo "Presupuesto"'], 400);
            }
            if ($tienePrecio) {
                return response()->json(['message' => 'Demanda (Tipo_Publicacion=1) NO puede tener "Precio"'], 400);
            }
        }

        // Detectar cambios
        $cambioDeOfertaDemanda = $tipoPublicacionAnterior !== $tipoPublicacionNuevo;
        $cambioDeTipoBeneficio = (int) $publicacion->Tipo_Beneficio !== (int) $validated['Tipo_Beneficio'];

        // Actualizar datos de la publicación
        $publicacion->update([
            'Nombre' => $validated['Nombre'],
            'Descripcion' => $validated['Descripcion'],
            'Tipo_Beneficio' => $validated['Tipo_Beneficio'], // Cambio entre Servicio/Producto
            'Tipo_Publicacion' => $tipoPublicacionNuevo, // Cambio entre Oferta/Demanda
        ]);

        if ($cambioDeOfertaDemanda) {
            if ($tipoPublicacionAnterior === 0) {
                // Cambia el tipo oferta a demanda, y luego Elimina de ofertas, crea en demandas
                \DB::table('ofertas')->where('id_publicacion', $publicacion->id)->delete();
                \DB::table('demandas')->insert([
                    'id_publicacion' => $publicacion->id,
                    'Presupuesto' => $validated['Presupuesto'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                // Cambia el tipo demanda a oferta, y luego Elimina de demandas, crea en ofertas
                \DB::table('demandas')->where('id_publicacion', $publicacion->id)->delete();
                \DB::table('ofertas')->insert([
                    'id_publicacion' => $publicacion->id,
                    'Precio' => $validated['Precio'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } else {
            //Actualiza el saldo en su tabla correspondiente
            if ($tipoPublicacionNuevo === 0) {
                // Es Oferta: actualizar Precio
                \DB::table('ofertas')
                    ->where('id_publicacion', $publicacion->id)
                    ->update(['Precio' => $validated['Precio']]);
            } else {
                // Es Demanda: actualizar Presupuesto
                \DB::table('demandas')
                    ->where('id_publicacion', $publicacion->id)
                    ->update(['Presupuesto' => $validated['Presupuesto']]);
            }
        }

        $returnTo = $request->input('return_to', '/verperfil');
        
        // Log para debug
        \Log::info('guardarPublicacion - return_to from body', [
            'return_to' => $returnTo,
            'full_url' => $request->fullUrl(),
            'request_body' => $request->all(),
        ]);

        // Inertia maneja correctamente los redirects
        return redirect($returnTo);
    }

    // Obtener todas las conversaciones y mensajes del usuario actual (incluye URLs de imágenes si Tipo_Mensaje=1)
    // Archivos vinculados: web.php, Chat.tsx, AreaChat.tsx, chatImagenesService.ts, AuthenticatedLayout.tsx, datosMockChatMensajes.ts, FilaConversacion.tsx, datosMockUsuarios.ts, datosMockMensajes.ts, Dashboard.tsx, EncabezadoMenu.tsx.
    // Visueales conectadas: Chat.
    public function chat(Request $request)
    {
        $user = $request->user();

        $conversaciones = \DB::table('conversations')
            ->where(function ($query) use ($user) {
                $query->where('user_one_id', $user->id)
                    ->orWhere('user_two_id', $user->id);
            })
            ->get();

        /*
        $conversacionesFormato = $conversaciones->map(function ($conv) use ($user) {
            // Identificar quién es el "otro usuario" en la conversación (no el logueado)
            $otroUsuarioId = $conv->user_one_id === $user->id ? $conv->user_two_id : $conv->user_one_id;
            $otroUsuario = \DB::table('users')->find($otroUsuarioId);

            // MODIFICADO: Obtener la imagen real del usuario de la tabla imagens
            // Si no existe imagen real, usar placeholder (Picsum photos random)
            // Filtrar por Tipo=0 (imágenes de perfil, no de publicación)
            $imagenUsuario = \DB::table('imagens')
                ->where('id_usuario_origen', $otroUsuarioId)
                ->where('Tipo', 0) // Solo imágenes de perfil
                ->orderBy('created_at', 'desc')
                ->first();

            $fotoUrl = $imagenUsuario 
                ? '/imagen/' . $imagenUsuario->Direccion 
                : 'https://picsum.photos/200?random=' . $otroUsuarioId;

            $ultimoMensaje = \DB::table('mensajes')
                ->where(function ($query) use ($conv) {
                    $query->where('id_usuario_origen', $conv->user_one_id)
                        ->where('id_usuario_destino', $conv->user_two_id);
                })->orWhere(function ($query) use ($conv) {
                    $query->where('id_usuario_origen', $conv->user_two_id)
                        ->where('id_usuario_destino', $conv->user_one_id);
                })->orderBy('created_at', 'desc')->first();
            
            $mensajesNoLeidos = \DB::table('mensajes')
                ->where('id_usuario_destino', $user->id)
                ->where(function ($query) use ($conv) {
                    $query->where('id_usuario_origen', $conv->user_one_id)
                        ->orWhere('id_usuario_origen', $conv->user_two_id);
                })
                ->whereNull('read_at')
                ->count();

            $fechaUltimo = $ultimoMensaje ? \Carbon\Carbon::parse($ultimoMensaje->created_at)->format('d/m/Y') : '';

            return [
                'id' => $conv->id,
                'usuarioId' => $otroUsuarioId,
                'nombre' => $otroUsuario->name . ' ' . $otroUsuario->Apellido_Paterno,
                'foto' => $fotoUrl,
                'ultimoMensaje' => $ultimoMensaje ? $ultimoMensaje->Mensaje : '',
                'tipoUltimoMensaje' => $ultimoMensaje ? $ultimoMensaje->Tipo_Mensaje: 3,
                'esMensajeDelUsuarioPrincipal' => $ultimoMensaje ? $ultimoMensaje->id_usuario_origen === $user->id : false,
                'fechaUltimoMensaje' => $fechaUltimo,
                'mensajesNoLeidos' => $mensajesNoLeidos,
            ];
        })->values()->toArray();
        */

        $conversacionesFormato = \DB::select(
            'CALL obtener_resumen_conversaciones_por_usuario_id(?)',
            [$user->id]
        );

        // Se obtienen todos los mensajes con una sola consulta al SP
        $mensajesPorConversacion = collect(
            \DB::select(
                'CALL obtener_mensajes_de_conversaciones_por_usuario_id(?)',
                [$user->id]
            )

        )->groupBy('conversation_id');

        $mensajesAgrupados = [];

        /*
        foreach ($conversaciones as $conv) {
            $mensajes = \DB::table('mensajes')
                ->where(function ($query) use ($conv) {
                    $query->where('id_usuario_origen', $conv->user_one_id)
                        ->where('id_usuario_destino', $conv->user_two_id);
                })->orWhere(function ($query) use ($conv) {
                    $query->where('id_usuario_origen', $conv->user_two_id)
                        ->where('id_usuario_destino', $conv->user_one_id);
                })->orderBy('created_at', 'asc')->get();

            $mensajesFormato = $mensajes->map(function ($msg) use ($user) {
                // Convertir fecha de BD (timestamp) a objeto Carbon para formatearla como dd/mm/yyyy y Horas:minutos:segundos
                $fechaMensaje = \Carbon\Carbon::parse($msg->created_at);

                // Determinar imagenUrl basado en Tipo_Mensaje
                $imagenUrl = null;
                if ($msg->Tipo_Mensaje === 1) {
                    // Es una imagen: el campo Mensaje contiene el nombre del archivo
                    $imagenUrl = '/imagen/' . $msg->Mensaje;
                }

                return [
                    'id' => $msg->id,
                    'contenido' => $msg->Tipo_Mensaje === 1 ? '[Imagen compartida]' : $msg->Mensaje,
                    'esDelUsuarioPrincipal' => $msg->id_usuario_origen === $user->id,
                    'fecha' => $fechaMensaje->format('d/m/Y'),
                    'hora' => $fechaMensaje->format('H:i:s'), // Incluir segundos para ordenamiento
                    'esImagen' => $msg->Tipo_Mensaje === 1,
                    'imagenUrl' => $imagenUrl,
                ];
            })->toArray();

            $mensajesAgrupados[] = [
                'conversacionId' => $conv->id,
                'mensajes' => $mensajesFormato,
            ];
        }
        */

        foreach($conversaciones as $conv) {
            $mensajes = $mensajesPorConversacion->get($conv->id, collect());

            $mensajesFormato = $mensajes->map(function($msg) use ($user) {
                $fechaMensaje = \Carbon\Carbon::parse($msg->created_at);

                $esImagen = (int) $msg->Tipo_Mensaje === 1;

                return [
                    'id' => $msg->id,
                    'contenido' => $esImagen ? '[Imagen compartida]' : $msg->Mensaje,
                    'esDelUsuarioPrincipal' => (int) $msg->id_usuario_origen === (int) $user->id,
                    'fecha' => $fechaMensaje->format('d/m/Y'),
                    'hora' => $fechaMensaje->format('H:i:s'),
                    'esImagen' => $esImagen,
                    'imagenUrl' => $esImagen ? '/imagen/' . $msg->Mensaje : null,
                ];
            })->values()->toArray();

            $mensajesAgrupados[] = [
                'conversacionId' => $conv->id,
                'mensajes' => $mensajesFormato,
            ];
        }

        return Inertia::render('Chat', [
            'conversaciones' => $conversacionesFormato,
            'mensajesPorConversacion' => $mensajesAgrupados,
        ]);
    }

    // Obtener mensajes de una conversación en particular
    public function obtenerMensajesConversacion(Request $request) {

        
        $user = $request->user();

        $validated = $request->validate([
            'conversacion_id' => ['required', 'integer', 'exists:conversations,id'],
        ]);

        $conversacionId = (int) $validated['conversacion_id'];

        
        $conversacion = \DB::table('conversations')
        ->where('id', $conversacionId)
        ->first();      

        if(!$conversacion) {
            abort(404, 'Conversacion no encontrada');
        }

        $esParticipante = 
        ((int) $conversacion->user_one_id === (int) $user->id)
        ||
        ((int) $conversacion->user_two_id === (int) $user->id);

        if(!$esParticipante) {
            abort(403, 'No tienes acceso a esta conversacion');
        }

        
        /*
        $mensajes = \DB::table('mensajes')
        ->where(function ($query) use ($conversacion) {
            $query->where('id_usuario_origen', $conversacion->user_one_id)
            ->where('id_usuario_destino', $conversacion->user_two_id);            
        })
        ->orWhere(function ($query) use ($conversacion) {
            $query->where('id_usuario_origen', $conversacion->user_two_id)
            ->where('id_usuario_destino', $conversacion->user_one_id);
        })
        ->orderBy('created_at', 'asc')
        ->get()
        */
        $mensajes_raw = \DB::select(
            'CALL obtener_mensajes_conversacion(?,?)',
            [$conversacion->user_one_id,
            $conversacion->user_two_id
            ]
        );

        $mensajes = collect($mensajes_raw)
        ->map(function($msg) use ($user) {
            $fecha = \Carbon\Carbon::parse($msg->created_at);

            return [
                'id' => (int) $msg->id,
                'contenido' => (int) $msg->Tipo_Mensaje === 1 ? '[Imagen compartida]' : (string) $msg->Mensaje,
                'esDelUsuarioPrincipal' => (int) $msg->id_usuario_origen === (int) $user->id,
                'fecha' => $fecha->format('d/m/Y'),
                'hora' => $fecha->format('H:i:s'),
                'esImagen' => (int) $msg->Tipo_Mensaje === 1,
                'imagenUrl' => (int) $msg->Tipo_Mensaje === 1 ? '/imagen/' . $msg->Mensaje : null,
            ];
        })
        ->values();

        return response()->json([
            'success' => true,
            'conversacion_id' => $conversacionId,
            'mensajes' => $mensajes,
        ]);

    }

    // Se envía mensaje y redirige a /chat
    // Guardar mensaje de texto en BD y retornar JSON con datos completos.
    // Archivos vinculados: web.php, enviarMensaje.ts, AreaChat.tsx, Chat.tsx, GloboMensaje.tsx.
    // Visuales conectadas: Chat.
    public function enviarMensaje(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'conversacion_id' => ['required', 'integer'],
            'contenido' => ['required', 'string', 'max:255'],
        ]);

        $conversacion = \App\Models\Conversation::find($validated['conversacion_id']);

        if (!$conversacion) {
            abort(404, 'Conversacion no encontrada');
        }

        if ($conversacion->user_one_id !== $user->id && $conversacion->user_two_id !== $user->id) {
            abort(403, 'No tienes acceso a esta conversacion');
        }

        $otroUsuarioId = $conversacion->user_one_id === $user->id ? $conversacion->user_two_id : $conversacion->user_one_id;

        $mensajeId = \DB::table('mensajes')->insertGetId([
            'id_usuario_origen' => $user->id,
            'id_usuario_destino' => $otroUsuarioId,
            'Mensaje' => $validated['contenido'],
            'Tipo_Mensaje' => 0, // Solo mensajes de texto
            'created_at' => now(),
            'updated_at' => now(),
            'read_at'=>null,
        ]);

        

        //evento de notificacion de mensajes no leídos
        event(new \App\Events\ChatMessageCreated(
            messageId: (int) $mensajeId,
            conversationId: (int) $conversacion->id,
            senderId: (int) $user->id,
            receiverId: (int) $otroUsuarioId,
            preview: (string) $validated['contenido'],
            isImage: false,
            createdAt: now()->toISOString(),
        ));

        // Obtener fecha y hora actuales
        $ahora = now();
        $fecha = $ahora->format('d/m/Y');
        $hora = $ahora->format('H:i:s');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'mensaje' => [
                    'id' => $mensajeId,
                    'contenido' => $validated['contenido'],
                    'esDelUsuarioPrincipal' => true,
                    'fecha' => $fecha,
                    'hora' => $hora,
                ]
            ]);
        }

        // Si es una petición tradicional, redirigir
        return redirect('/chat');
    }

    // Guardar imagen en BD. Acepta base64 (con/sin "data:image/...;base64," prefix), valida tipo y tamaño (5MB max)
    // Archivos vinculados: web.php, enviarMensajeConImagen.ts, AreaChat.tsx, Chat.tsx, GloboMensaje.tsx.
    // Visuales conectadas: Chat.
    public function enviarMensajeConImagen(Request $request)
    {
        $user = $request->user();

        
        $validated = $request->validate([
            'conversacion_id' => ['required', 'integer'],
            'imagen_base64' => ['required', 'string'], 
        ]);

        

        $conversacion = \App\Models\Conversation::find($validated['conversacion_id']);

        if (!$conversacion) {
            abort(404, 'Conversacion no encontrada');
        }

        if ($conversacion->user_one_id !== $user->id && $conversacion->user_two_id !== $user->id) {
            abort(403, 'No tienes acceso a esta conversacion');
        }

        $otroUsuarioId = $conversacion->user_one_id === $user->id ? $conversacion->user_two_id : $conversacion->user_one_id;

        // Procesa
        $base64Image = $validated['imagen_base64'];
        
        // Extrae la parte de datos
        if (strpos($base64Image, 'base64,') !== false) {
            list(, $base64Image) = explode('base64,', $base64Image);
        }

        $imageData = base64_decode($base64Image);
        
        // Valida que sea imagen
        $finfo = finfo_open();
        $mime = finfo_buffer($finfo, $imageData, FILEINFO_MIME_TYPE);
        finfo_close($finfo);

        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            return response()->json(['error' => 'Archivo no es una imagen válida'], 400);
        }

        if (strlen($imageData) > 5 * 1024 * 1024) {
            return response()->json(['error' => 'Imagen muy grande (máximo 5MB)'], 400);
        }

        $filename = uniqid() . '.' . match($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        try {
            $uploadDir = storage_path('app/uploads');
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }
            
            file_put_contents($uploadDir . DIRECTORY_SEPARATOR . $filename, $imageData);
            
            if (!file_exists($uploadDir . DIRECTORY_SEPARATOR . $filename)) {
                return response()->json(['error' => 'Error al guardar imagen'], 500);
            }
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al guardar imagen: ' . $e->getMessage()], 500);
        }

        // Guardar en BD
        $mensajeId = \DB::table('mensajes')->insertGetId([
            'id_usuario_origen' => $user->id,
            'id_usuario_destino' => $otroUsuarioId,
            'Mensaje' => $filename, 
            'Tipo_Mensaje' => 1, // 1 = imagen
            'created_at' => now(),
            'updated_at' => now(),
            'read_at'=> null,
        ]);

        //evento de notificacion de mensajes no leídos
        event(new \App\Events\ChatMessageCreated(
            messageId: (int) $mensajeId,
            conversationId: (int) $conversacion->id,
            senderId: (int) $user->id,
            receiverId: (int) $otroUsuarioId,
            //preview: (string) $validated['contenido'],
            preview: 'Imagen',
            isImage: true,
            createdAt: now()->toISOString(),
        ));

        
        $ahora = now();
        $fecha = $ahora->format('d/m/Y');
        $hora = $ahora->format('H:i:s');

        
        return response()->json([
            'success' => true,
            'mensaje' => [
                'id' => $mensajeId,
                'contenido' => '[Imagen compartida]',
                'esDelUsuarioPrincipal' => true,
                'fecha' => $fecha,
                'hora' => $hora,
                'esImagen' => true,
                'imagenUrl' => '/imagen/' . $filename,
            ]
        ]);
    }

    // Mostrar página de administración de usuarios
    // Archivos vinculados: web.php, AdministrarUsuarios.tsx, FilaUsuario.tsx, AuthenticatedLayout.tsx, AdminHome.tsx, VerPerfilAdmin.tsx, datosMockAdministradores.ts, datosMockUsuarios.ts.
    // Visuales conectadas: Administrar usuarios
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

    // Crear o buscar conversación existente entre dos usuarios. Retorna ID de conversación
    // Archivos vinculados: web.php, crearOBuscarConversacion.ts, FilaUsuario.tsx, AdministrarUsuarios.tsx.
    // Visuales conectadas: Administrar Usuarios, chat (Link "Iniciar conversación o buscar")
    public function crearOBuscarConversacion(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'usuario_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $otroUsuarioId = $validated['usuario_id'];

        // No permitir crear conversación consigo mismo
        if ($user->id === $otroUsuarioId) {
            abort(400, 'No puedes crear conversacion contigo mismo');
        }

        // Buscar si ya existe una conversación
        /*
        $conversacionExistente = \DB::table('conversations')
            ->where(function ($query) use ($user, $otroUsuarioId) {
                $query->where('user_one_id', $user->id)
                    ->where('user_two_id', $otroUsuarioId);
            })
            ->orWhere(function ($query) use ($user, $otroUsuarioId) {
                $query->where('user_one_id', $otroUsuarioId)
                    ->where('user_two_id', $user->id);
            })
            ->first();
        */

        $conversacionExistente = \DB::SELECT(
            'CALL existe_conversacion_por_participantes_id(?, ?)',
            [
                $user->id,
                $otroUsuarioId
            ]
        );
        

        if (!$conversacionExistente) {
            // Crear nueva conversación
            \DB::table('conversations')->insert([
                'user_one_id' => $user->id,
                'user_two_id' => $otroUsuarioId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        
        return redirect('/chat');
    }

    // Eliminar usuario del sistema (solo admins). Valida y elimina de BD y tabla Tipo_Usuario.
    // Archivos vinculados: web.php, eliminarUsuario.ts, FilaUsuario.tsx, AdministrarUsuarios.tsx.
    // Visuales conectadas: Administrar Usuarios.
    public function eliminarUsuario(Request $request)
    {
        $isAdmin = (bool) $request->session()->get('is_admin', false);

        if (!$isAdmin) {
            abort(403, 'No tienes permiso');
        }

        $validated = $request->validate([
            'usuario_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $usuarioId = $validated['usuario_id'];
        $user = $request->user();

        
        $esSelfDelete = $user->id === $usuarioId;

        // Eliminar usuario (las conversaciones persisten)
        \DB::table('users')->where('id', $usuarioId)->delete();

        // Si se eliminó a sí mismo, redirigir a home
        if ($esSelfDelete) {
            return redirect('/');
        }

        // Si eliminó a otro usuario, redirigir a Administrar Usuarios
        return redirect()->route('administrar-usuarios');
    }

    // Método para crear publicaciones directamente desde API (específico para pruebas en Insomnia)
    //Enlace utilizado: "http://127.0.0.1:8000/api/post". 
    //Archivo vinculado: api.php.
    // Visuales conectadas: Ninguna (endpoint API para pruebas).
    public function crearPublicacion(Request $request)
    {
        $user = $request->user();

        // CORREGIDO: Tipo_Publicacion es SIEMPRE Oferta/Demanda (0=Oferta, 1=Demanda)
        // NUEVO: Tipo_Beneficio determina si es Servicio/Producto (0=Servicio, 1=Producto)
        // El saldo que se envíe DEBE coincidir con Tipo_Publicacion:
        // - Si Tipo_Publicacion=0 (Oferta) → DEBE enviar "Precio"
        // - Si Tipo_Publicacion=1 (Demanda) → DEBE enviar "Presupuesto"
        $rules = [
            'id_usuario' => ['nullable', 'integer', 'exists:users,id'],
            'Nombre' => ['required', 'string', 'max:255'],
            'Descripcion' => ['required', 'string', 'max:255'],
            'Tipo_Publicacion' => ['required', 'integer', 'in:0,1'], 
            'Tipo_Beneficio' => ['required', 'integer', 'in:0,1'], 
            'visible' => ['required', 'integer', 'in:0,1'],
            'Precio' => ['nullable', 'integer', 'min:0'], 
            'Presupuesto' => ['nullable', 'integer', 'min:0'], 
        ];

        $validated = $request->validate($rules);

        // Si no especifica id_usuario, usa el usuario logueado
        $idUsuario = $validated['id_usuario'] ?? $user->id;

        // TODO: VALIDACIÓN - Bloquear que administradores creen publicaciones
        // Verificar si el usuario es administrador (tiene registro en tipo__usuarios)
        // CORRECCIÓN, en Insomnia todavía se pueden crear publicaciones al no existir un diferenciador entre admins y usuarios de momento.
        $esAdmin = \App\Models\Tipo_Usuario::where('id_usuario', $idUsuario)->exists();
        if ($esAdmin) {
            return response()->json([
                'message' => 'Los administradores no pueden crear publicaciones',
                'error' => 'admin_cannot_publish'
            ], 403);
        }

        
        $tipoPublicacion = (int) $validated['Tipo_Publicacion'];
        $tienePrecio = $request->filled('Precio');
        $tienePresupuesto = $request->filled('Presupuesto');


        if ($tipoPublicacion === 0) {
            if (!$tienePrecio) {
                return response()->json(['message' => 'Oferta (Tipo_Publicacion=0) requiere campo "Precio"'], 400);
            }
            if ($tienePresupuesto) {
                return response()->json(['message' => 'Oferta (Tipo_Publicacion=0) NO puede tener "Presupuesto"'], 400);
            }
        }


        if ($tipoPublicacion === 1) {
            if (!$tienePresupuesto) {
                return response()->json(['message' => 'Demanda (Tipo_Publicacion=1) requiere campo "Presupuesto"'], 400);
            }
            if ($tienePrecio) {
                return response()->json(['message' => 'Demanda (Tipo_Publicacion=1) NO puede tener "Precio"'], 400);
            }
        }

        // Crear publicación con Tipo_Publicacion (Oferta/Demanda) y Tipo_Beneficio (Servicio/Producto)
        $publicacionId = \DB::table('publicacions')->insertGetId([
            'id_usuario' => $idUsuario,
            'Nombre' => $validated['Nombre'],
            'Descripcion' => $validated['Descripcion'],
            'Tipo_Publicacion' => $tipoPublicacion,
            'Tipo_Beneficio' => $validated['Tipo_Beneficio'], 
            'visible' => $validated['visible'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Crear entrada en ofertas o demandas según Tipo_Publicacion
        if ($tipoPublicacion === 0) {
            // Es Oferta: crear en tabla ofertas con Precio
            \DB::table('ofertas')->insert([
                'id_publicacion' => $publicacionId,
                'Precio' => $validated['Precio'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            // Es Demanda: crear en tabla demandas con Presupuesto
            \DB::table('demandas')->insert([
                'id_publicacion' => $publicacionId,
                'Presupuesto' => $validated['Presupuesto'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Obtener la publicación completa para retornar
        // Se especifica 'publicacions.id' explícitamente para evitar ambigüedad en los joins
        $publicacion = \DB::table('publicacions')
            ->where('publicacions.id', $publicacionId) 
            ->leftJoin('ofertas', 'publicacions.id', '=', 'ofertas.id_publicacion')
            ->leftJoin('demandas', 'publicacions.id', '=', 'demandas.id_publicacion')
            ->select(
                'publicacions.id',
                'publicacions.Nombre',
                'publicacions.Descripcion',
                'publicacions.Tipo_Publicacion',
                'publicacions.visible',
                'publicacions.created_at',
                'ofertas.Precio',
                'demandas.Presupuesto'
            )
            ->first();

        return response()->json($publicacion, 201);
    }

    // NUEVO: Método para subir imagen a una publicación
    // Subir imagen de publicación. Guarda archivo en storage y crea associación en inter__imagen__public
    // Archivos vinculados: web.php, actualizarCSRFToken.ts, estadosPublicaciones.ts, subirImagenPublicacion.ts, servicioImagenesPublicacion.ts, FormEditarMiPublicacionCRIS.tsx, EditarMiPublicacionCRIS.tsx, AjustadorImagenPublicacion.tsx.
    // Visuales conectadas: Editar publicación, ver perfil.
    public function subirImagenPublicacion(Request $request, $publicacion_id)
    {
        $usuarioActual = $request->user();
        if (!$usuarioActual) {
            abort(401, 'No autenticado');
        }

        $isAdmin = (bool) $request->session()->get('is_admin', false);
        
        $query = \DB::table('publicacions')
            ->where('id', $publicacion_id);
        
        // Solo usuarios normales solo pueden subir imágenes a sus propias publicaciones
        // Los admins pueden subir a cualquier publicación
        if (!$isAdmin) {
            $query = $query->where('id_usuario', $usuarioActual->id);
        }
        
        $publicacion = $query->first();

        if (!$publicacion) {
            abort(403, 'No tienes permiso para subir imágenes a esta publicación');
        }

        
        $validated = $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Guardar la imagen en storage
        $image = $request->file('image');
        $filename = uniqid() . '.' . $image->extension();
        
        try {
            
            $uploadDir = storage_path('app/uploads');
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }
            
            
            $image->move($uploadDir, $filename);
            
            \Log::info('Imagen guardada exitosamente', [
                'filename' => $filename,
                'path' => $uploadDir . DIRECTORY_SEPARATOR . $filename,
                'exists' => file_exists($uploadDir . DIRECTORY_SEPARATOR . $filename),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error al guardar imagen', [
                'error' => $e->getMessage(),
                'filename' => $filename,
                'uploadDir' => $uploadDir ?? 'undefined',
            ]);
            return response()->json(['error' => 'Error al guardar imagen: ' . $e->getMessage()], 500);
        }

        
        $imagenGuardada = $this->saveImagen($filename, $usuarioActual->id);

        // Cambiar Tipo a 1 (imagen de publicación)
        \DB::table('imagens')
            ->where('id', $imagenGuardada->id)
            ->update(['Tipo' => 1]);

        // Verificar cuántas imágenes ya tiene esta publicación, máximo 5.
        /*
        $conteoImagenes = \DB::table('inter__imagen__public')
            ->where('id_publicacion', $publicacion_id)
            ->join('imagens', 'inter__imagen__public.id_imagen', '=', 'imagens.id')
            ->where('imagens.Tipo', 1)  // Solo buscar imágenes de tipo publicación
            ->count();
        */
        $conteoImagenes = \DB::select(
            'CALL contar_imagenes_publicacion_por_tipo(?)',
            [$publicacion_id]
        );
        
        if ($conteoImagenes >= 5) {
           
            \Storage::disk('uploads')->delete($filename);
            \DB::table('imagens')->where('id', $imagenGuardada->id)->delete();
            
            return response()->json(['error' => 'La publicación ya tiene el máximo de imágenes (5)'], 422);
        }

        // Crear nueva asociación en `inter__imagen__public` sin borrar las anteriores
        
        \DB::table('inter__imagen__public')->insert([
            'id_publicacion' => $publicacion_id,
            'id_imagen' => $imagenGuardada->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        \Log::info('Nueva imagen agregada a publicacion', [
            'id_publicacion' => $publicacion_id,
            'id_imagen' => $imagenGuardada->id,
            'total_imagenes' => $conteoImagenes + 1,  
        ]);

        // Devolver imagen + CSRF token actualizado
        $response = [
            'id' => $imagenGuardada->id,
            'Direccion' => $imagenGuardada->Direccion,
            'imagenUrl' => '/imagen/' . $imagenGuardada->Direccion, 
            'csrf_token' => csrf_token(),
        ];

        return response()->json($response);
    }

    // Obtener todas las imágenes de una publicación (0-5) 
    // SACADO MOMENTÁNEAMENTE: Función comentada debido a que no se encontró uso de momento.
    // Ya hay otros archivos que logran su uso como servicioImagenesPublicacion.ts, subirImagenPublicacion.ts, la función de subirImagenPublicacion() dentro de TestControllerCris.php
    // Entonces se decició comentar esta función solo en caso que se requiera usar.
    // Las visuales que deberían estar conectadas son: Editar publicación y ver perfil.
    /*
    public function obtenerImagenesPublicacion($publicacion_id)
    {
        $imagenes = \DB::table('inter__imagen__public')
            ->where('id_publicacion', $publicacion_id)
            ->join('imagens', 'inter__imagen__public.id_imagen', '=', 'imagens.id')
            ->where('imagens.Tipo', 1) 
            ->select(
                'imagens.id as id_imagen',
                'imagens.Direccion',
                'inter__imagen__public.created_at'
            )
            ->orderBy('inter__imagen__public.created_at', 'asc')
            ->limit(5)
            ->get()
            ->map(function($img) {
                return [
                    'id' => $img->id_imagen,
                    'url' => '/imagen/' . $img->Direccion,
                    'direccion' => $img->Direccion,
                    'fecha' => $img->created_at,
                ];
            });

        return response()->json([
            'imagenes' => $imagenes,
            'total' => count($imagenes),
        ]);
    }
    */

    // Método alternativo para servir imágenes directamente desde aquí
    // Servir imagen almacenada en /storage/app/uploads/ con tipo MIME correcto
    // Archivos vinculados: web.php, api.php (Parcialmente), servicioImagenesPublicacion.ts, ImageZoomModal.tsx (Indirecta, muestra imagen)
    // Visuales conectadas: Administrar usuarios, Ver perfil, editar perfil, chat, editar pubicacion (Básicamente, esta función permite que se vean las imágenes).
    public function servirImagen($filename)
    {
        $path = storage_path('app/uploads/' . $filename);
        
        \Log::info('Servir imagen - Debug', [
            'filename' => $filename,
            'path' => $path,
            'exists' => file_exists($path),
        ]);
        
        if (!file_exists($path)) {
            \Log::warning('Imagen no encontrada', [
                'filename' => $filename,
                'path' => $path,
                'uploadDir' => storage_path('app/uploads'),
            ]);
            return response()->json(['error' => 'Imagen no encontrada: ' . $filename], 404);
        }
        
        return response()->file($path);
    }

    //metodo marcar leidos
    public function marcarLeidos(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'conversacion_id'=>['required', 'integer', 'exists:conversations,id'],
        ]);
        
        $conversacionId = (int) $validated['conversacion_id'];

        $conversacion = \DB::table('conversations')
        ->where('id', $conversacionId)
        ->first();

        if(!$conversacion) {
            abort(404, 'Converasacion no encontrada');
        }

        $esParticipante = 
        ((int) $conversacion->user_one_id === (int) $user->id || 
        (int) $conversacion->user_two_id === (int) $user->id);

        if(!$esParticipante) {
            abort(403, 'No tienes acceso a esta conversacion');
        }

        //Identificar al otro participante
        $otroUsuarioId = ((int) $conversacion->user_one_id === (int) $user->id)
        ? (int) $conversacion->user_two_id : (int) $conversacion->user_one_id;

        $ahora = now();

        $updatedCount = \DB::table('mensajes')
        ->where('id_usuario_origen', $otroUsuarioId)
        ->where('id_usuario_destino', (int) $user->id)
        ->whereNull('read_at')
        ->update([
            'read_at'=>$ahora,
            'updated_at'=> $ahora,
        ]);


        //conteo global de no leidos (badge total)
        $unreadTotal = \DB::table('mensajes')
        ->where('id_usuario_destino', (int) $user->id)
        ->whereNull('read_at')
        ->count();

        return response()->json([
            'success' => true,
            'conversation_id' => $conversacionId,
            'updated_count' => $updatedCount,
            'unread_total' => $unreadTotal,
        ]);
    }

    // Método para eliminar una imagen de una publicación
    // Desasociar imagen de publicación en inter__imagen__public (no elimina archivo del storage)
    // Archivos vinculados: web.php, servicioImagenesPublicacion.ts, eliminarImagenPublicacion.tsx, CambiarEliminarImagenPublicacion.tsx, FormEditarMiPublicacionCRIS.tsx, EditarMiPublicacionCRIS.tsx.
    // Visuales conectadas: Editar publicación, ver perfil (Muestra el resultado del cambio).
    public function eliminarImagenPublicacion(Request $request, $publicacion_id, $imagen_id)
    {
        $usuarioActual = $request->user();
        if (!$usuarioActual) {
            abort(401, 'No autenticado');
        }

        $isAdmin = (bool) $request->session()->get('is_admin', false);
        
        $query = \DB::table('publicacions')
            ->where('id', $publicacion_id);
        
        // Solo usuarios normales solo pueden eliminar imágenes de sus propias publicaciones
        // Los admins pueden eliminar de cualquier publicación
        if (!$isAdmin) {
            $query = $query->where('id_usuario', $usuarioActual->id);
        }
        
        $publicacion = $query->first();

        if (!$publicacion) {
            abort(403, 'No tienes permiso para modificar esta publicación');
        }

        
        $asociacion = \DB::table('inter__imagen__public')
            ->where('id_publicacion', $publicacion_id)
            ->where('id_imagen', $imagen_id)
            ->join('imagens', 'inter__imagen__public.id_imagen', '=', 'imagens.id')
            ->where('imagens.Tipo', 1)  // Solo imágenes de publicación
            ->first();

        if (!$asociacion) {
            abort(404, 'Imagen no encontrada en esta publicación');
        }

        
        \DB::table('inter__imagen__public')
            ->where('id_publicacion', $publicacion_id)
            ->where('id_imagen', $imagen_id)
            ->delete();

        \Log::info('Imagen desasociada de publicación', [
            'publicacion_id' => $publicacion_id,
            'imagen_id' => $imagen_id,
            'usuario_id' => $usuarioActual->id,
        ]);

        return response()->json(['message' => 'Imagen eliminada correctamente'], 200);
    }

    /*
    ════════════════════════════════════════════════════════════════════════════════════
    FUNCIONES COMENTADAS: Sistema de Calificaciones (Esqueleto en desarrollo)
    ════════════════════════════════════════════════════════════════════════════════════
    Estas funciones son PLACEHOLDERS y están comentadas porque:
    1. El diseño de las tablas calificacions e inter__calif__publics aún no está terminado
    2. Se dejarán aquí como estructura para cuando BD esté lista

    Se espero que se descomenten estas funciones cuando:
    - La tabla calificacions y inter__calif__publics tengan sus datos claros
    - Se defina la lógica exacta de cálculo de popularidad
    - Se activen las rutas en routes/web.php
    ════════════════════════════════════════════════════════════════════════════════════

    public function calificaciones(Request $request, $publicacion_id)
    {
        try {
            $calificacionesPublicacion = \DB::table('inter__calif__publics')
                ->where('id_publicacion', $publicacion_id)
                ->join('calificacions', 'inter__calif__publics.id_calificacion', '=', 'calificacions.id')
                ->select(
                    'calificacions.id',
                    'calificacions.Puntuacion',
                    'calificacions.Comentario',
                    'calificacions.id_usuario_origen',
                    'calificacions.created_at'
                )
                ->get();

            \Log::info('calificaciones() - Función esqueleto llamada', [
                'publicacion_id' => $publicacion_id,
                'calificaciones_count' => $calificacionesPublicacion->count(),
            ]);

            return response()->json([
                'message' => 'Función calificaciones() aún no implementada - En desarrollo',
                'data' => null,
                'status' => 'placeholder'
            ], 200);
        } catch (\Exception $e) {
            \Log::error('calificaciones() - Error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener calificaciones'], 500);
        }
    }

    public function asociarcalificaciones(Request $request, $publicacion_id)
    {
        try {
            $usuario = $request->user();
            if (!$usuario) {
                return response()->json(['error' => 'No autenticado'], 401);
            }

            $publicacion = \DB::table('publicacions')->find($publicacion_id);
            if (!$publicacion) {
                return response()->json(['error' => 'Publicación no encontrada'], 404);
            }

            \Log::info('asociarcalificaciones() - Función esqueleto llamada', [
                'usuario_id' => $usuario->id,
                'publicacion_id' => $publicacion_id,
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Función asociarcalificaciones() aún no implementada - En desarrollo',
                'data' => null,
                'status' => 'placeholder'
            ], 200);
        } catch (\Exception $e) {
            \Log::error('asociarcalificaciones() - Error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error al asociar calificación'], 500);
        }
    }
    */
}



// NOTA: EL archivo app.blade.php fue modificado para conectarlo con actualizarCSRToken.ts y crearOBuscarConversacion.ts que son archivos conectados a algunas de las funciones en este controlador.
// Con el único fin de actualizar el token y no se tenga errores al manejar los datos.
// El archivo Publicacion.php también fue ligeramente modificado para añadir el parámetro de tipo_beneficio en la lista. 