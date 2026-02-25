<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Cruds;
use Inertia\Inertia;


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
    
    //Este cógido pertenece a Cris
    /*
    Comentario
    De
    prueba
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
            'returnTo' => $returnTo,
        ]);
    }

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
        
        return redirect()->route('verperfil');
    }

    public function verPerfil(Request $request)
    {
        $user = $request->user();
        $isAdmin = (bool) $request->session()->get('is_admin', false);

        // Si NO es admin, obtener sus publicaciones visibles con precios/presupuestos. Si es admin dejar array vacío
        $publicaciones = [];
        if (!$isAdmin) {
            $publicaciones = \App\Models\Publicacion::where('id_usuario', $user->id)
                ->where('visible', 1)
                ->leftJoin('ofertas', 'publicacions.id', '=', 'ofertas.id_publicacion')
                ->leftJoin('demandas', 'publicacions.id', '=', 'demandas.id_publicacion')
                ->select(
                    'publicacions.id',
                    'publicacions.Nombre',
                    'publicacions.Descripcion',
                    'publicacions.Tipo_Publicacion',
                    'publicacions.created_at',
                    'ofertas.Precio',
                    'demandas.Presupuesto'
                )
                ->get()
                ->map(fn($pub) => [
                    'id' => $pub->id,
                    'titulo' => $pub->Nombre,
                    'descripcion' => $pub->Descripcion,
                    'costo' => $pub->Precio !== null ? ($pub->Precio ?? 0) : ($pub->Presupuesto ?? 0),
                    'tipo' => $pub->Tipo_Publicacion == 0 ? 'Servicio' : 'Producto',
                    'ofertaDemanda' => $pub->Precio !== null ? 'Oferta' : 'Demanda',
                    'imagen' => 'https://picsum.photos/200?random=' . $pub->id,
                    'estado' => 'Activo',
                    'fecha' => $pub->created_at->format('Y-m-d'),
                ])
                ->toArray();
        }

        return Inertia::render('VerPerfil', [
            'datosUsuario' => [
                'id' => $user->id,
                'nombre' => $user->name ?? '',
                'apellidoPaterno' => $user->Apellido_Paterno ?? '',
                'apellidoMaterno' => $user->Apellido_Materno ?? '',
                'ciudad' => $user->Ciudad ?? '',
                'telefono' => $user->Telefono ?? '',
                'direccion' => $user->Direccion ?? '',
                'email' => $user->email ?? '',
                'fechaNacimiento' => $user->Age ?? '',
                'saldoHoras' => $user->saldo ?? 0,
                'foto' => 'https://picsum.photos/200?random=' . $user->id,
            ],
            'publicaciones' => $publicaciones,
            'isAdmin' => $isAdmin,
        ]);
    }

    public function editarPublicacion(Request $request, $id)
    {
        $user = $request->user();

        $publicacion = \App\Models\Publicacion::where('publicacions.id', $id)
            ->where('id_usuario', $user->id)
            ->leftJoin('ofertas', 'publicacions.id', '=', 'ofertas.id_publicacion')
            ->leftJoin('demandas', 'publicacions.id', '=', 'demandas.id_publicacion')
            ->select(
                'publicacions.id',
                'publicacions.Nombre',
                'publicacions.Descripcion',
                'publicacions.Tipo_Publicacion',
                'ofertas.Precio',
                'demandas.Presupuesto'
            )
            ->first();

        if (!$publicacion) {
            abort(404);
        }

        // MODIFICADO: Determinar si es Oferta o Demanda consultando las tablas (no por Tipo_Publicacion)
        // Oferta: existe entrada en tabla 'ofertas' con Precio
        // Demanda: existe entrada en tabla 'demandas' con Presupuesto
        // Esto es independiente de Tipo_Publicacion que ahora solo representa Servicio (0) o Producto (1)
        $esOferta = $publicacion->Precio !== null;
        $tipoText = $esOferta ? 'Oferta' : 'Demanda';
        $costo = $esOferta ? ($publicacion->Precio ?? 0) : ($publicacion->Presupuesto ?? 0);

        $datosPublicacion = [
            'id' => $publicacion->id,
            'titulo' => $publicacion->Nombre,
            'descripcion' => $publicacion->Descripcion,
            'costo' => $costo,
            'tipo' => $publicacion->Tipo_Publicacion == 0 ? 'Servicio' : 'Producto', // MODIFICADO: Ahora 0=Servicio, 1=Producto
            'ofertaDemanda' => $tipoText, // MODIFICADO: Se obtiene de las tablas ofertas/demandas, no de Tipo_Publicacion
            'imagen' => 'https://picsum.photos/200?random=' . $publicacion->id,
            'estado' => 'Activo',
            'tipoPublicacion' => $publicacion->Tipo_Publicacion,
        ];

        return Inertia::render('EditarMiPublicacionCRIS', [
            'datosPublicacion' => $datosPublicacion,
        ]);
    }

    public function guardarPublicacion(Request $request, $id)
    {
        $user = $request->user();

        $publicacion = \App\Models\Publicacion::where('id', $id)
            ->where('id_usuario', $user->id)
            ->first();

        if (!$publicacion) {
            abort(404);
        }

        // MODIFICADO: Agregado 'tipo' a la validación (ahora es independiente)
        // 'tipo' es el Tipo_Publicacion (0=Servicio, 1=Producto)
        // 'ofertaDemanda' es si es Oferta o Demanda (determinado por Precio vs Presupuesto)
        $rules = [
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string', 'max:255'],
            'costo' => ['required', 'integer', 'min:0'],
            'tipo' => ['required', 'integer', 'in:0,1'], // NUEVO: 0=Servicio, 1=Producto
            'ofertaDemanda' => ['required', 'in:Oferta,Demanda'],
        ];

        $validated = $request->validate($rules);

        // MODIFICADO: Obtener tipo anterior (Servicio/Producto) y estado anterior (Oferta/Demanda)
        $tipoAnterior = $publicacion->Tipo_Publicacion;
        $tipoNuevo = (int) $validated['tipo']; // NUEVO: recibir tipo directamente
        
        // MODIFICADO: Determinar si era Oferta o Demanda antes consultando las tablas
        $esOfertaAntes = \DB::table('ofertas')
            ->where('id_publicacion', $publicacion->id)
            ->exists();
        $esDemandaAntes = \DB::table('demandas')
            ->where('id_publicacion', $publicacion->id)
            ->exists();
        
        $esOfertaAhora = $validated['ofertaDemanda'] === 'Oferta';
        // MODIFICADO: Detectar cambios INDEPENDIENTES
        $cambioDeTipo = $tipoAnterior !== $tipoNuevo; // Cambio de Servicio/Producto
        $cambioDeOfertaDemanda = ($esOfertaAntes && !$esOfertaAhora) || (!$esOfertaAntes && $esOfertaAhora); // Cambio de Oferta/Demanda

        // MODIFICADO: Actualizar datos de publicación (Tipo_Publicacion es independiente)
        // Tipo_Publicacion (Servicio/Producto) no afecta la tabla de ofertas/demandas
        $publicacion->update([
            'Nombre' => $validated['titulo'],
            'Descripcion' => $validated['descripcion'],
            'Tipo_Publicacion' => $tipoNuevo, // MODIFICADO: Cambiar tipo independientemente
        ]);

        // MODIFICADO: Manejar SOLO el cambio de Oferta a Demanda o vice versa
        // Este cambio es INDEPENDIENTE del cambio de Tipo_Publicacion
        if ($cambioDeOfertaDemanda) {
            if ($esOfertaAntes) {
                // MODIFICADO: Era Oferta, ahora es Demanda
                // Eliminar de ofertas y crear en demandas
                \DB::table('ofertas')->where('id_publicacion', $publicacion->id)->delete();
                \DB::table('demandas')->insert([
                    'id_publicacion' => $publicacion->id,
                    'Presupuesto' => $validated['costo'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                // MODIFICADO: Era Demanda, ahora es Oferta
                // Eliminar de demandas y crear en ofertas
                \DB::table('demandas')->where('id_publicacion', $publicacion->id)->delete();
                \DB::table('ofertas')->insert([
                    'id_publicacion' => $publicacion->id,
                    'Precio' => $validated['costo'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } else {
            // MODIFICADO: No cambió Oferta/Demanda, solo actualizar el costo en su tabla correspondiente
            if ($esOfertaAhora) {
                \DB::table('ofertas')
                    ->where('id_publicacion', $publicacion->id)
                    ->update(['Precio' => $validated['costo']]);
            } else {
                \DB::table('demandas')
                    ->where('id_publicacion', $publicacion->id)
                    ->update(['Presupuesto' => $validated['costo']]);
            }
        }

        return redirect()->route('verperfil');
    }

    //Renderiza/Obtiene las conversaciones
    public function chat(Request $request)
    {
        $user = $request->user();

        $conversaciones = \DB::table('conversations')
            ->where(function ($query) use ($user) {
                $query->where('user_one_id', $user->id)
                    ->orWhere('user_two_id', $user->id);
            })
            ->get();

        $conversacionesFormato = $conversaciones->map(function ($conv) use ($user) {
            // Identificar quién es el "otro usuario" en la conversación (no el logueado)
            $otroUsuarioId = $conv->user_one_id === $user->id ? $conv->user_two_id : $conv->user_one_id;
            $otroUsuario = \DB::table('users')->find($otroUsuarioId);

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
                })->count();

            $fechaUltimo = $ultimoMensaje ? \Carbon\Carbon::parse($ultimoMensaje->created_at)->format('d/m/Y') : '';

            return [
                'id' => $conv->id,
                'usuarioId' => $otroUsuarioId,
                'nombre' => $otroUsuario->name . ' ' . $otroUsuario->Apellido_Paterno,
                'foto' => 'https://picsum.photos/200?random=' . $otroUsuarioId,
                'ultimoMensaje' => $ultimoMensaje ? $ultimoMensaje->Mensaje : '',
                'esMensajeDelUsuarioPrincipal' => $ultimoMensaje ? $ultimoMensaje->id_usuario_origen === $user->id : false,
                'fechaUltimoMensaje' => $fechaUltimo,
                'mensajesNoLeidos' => $mensajesNoLeidos,
            ];
        })->values()->toArray();

        $mensajesAgrupados = [];
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
                // Convertir fecha de BD (timestamp) a objeto Carbon para formatearla como d/m/Y y H:i
                $fechaMensaje = \Carbon\Carbon::parse($msg->created_at);

                return [
                    'id' => $msg->id,
                    'contenido' => $msg->Mensaje,
                    'esDelUsuarioPrincipal' => $msg->id_usuario_origen === $user->id,
                    'fecha' => $fechaMensaje->format('d/m/Y'),
                    'hora' => $fechaMensaje->format('H:i'),
                    'esImagen' => $msg->Tipo_Mensaje === 1,
                    'imagenUrl' => null, // Las imágenes son temporales, no se recuperan después de reiniciar
                ];
            })->toArray();

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

    // Se envía mensaje y redirige a /chat
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

        \DB::table('mensajes')->insert([
            'id_usuario_origen' => $user->id,
            'id_usuario_destino' => $otroUsuarioId,
            'Mensaje' => $validated['contenido'],
            'Tipo_Mensaje' => 0, // Solo mensajes de texto
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        
        return redirect('/chat');
    }

    public function administrarUsuarios(Request $request)
    {
        $isAdmin = (bool) $request->session()->get('is_admin', false);

        if (!$isAdmin) {
            abort(403, 'Solo administradores pueden acceder a esta página');
        }

        $usuarios = \DB::table('users')
            ->orderBy('id', 'asc')
            ->get()
            ->map(fn($user) => [
                'id' => $user->id,
                'nombre' => $user->name ?? '',
                'apellidoPaterno' => $user->Apellido_Paterno ?? '',
                'apellidoMaterno' => $user->Apellido_Materno ?? '',
                'ciudad' => $user->Ciudad ?? '',
                'direccion' => $user->Direccion ?? '',
                'email' => $user->email ?? '',
                'foto' => 'https://picsum.photos/200?random=' . $user->id,
            ])
            ->toArray();

        return Inertia::render('AdministrarUsuarios', [
            'usuarios' => $usuarios,
            'isAdmin' => $isAdmin,
        ]);
    }

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

    // NUEVO: Método para crear publicaciones directamente desde API (específico para pruebas en Insomnia)
    // Cambio arquitectónico: Tipo_Publicacion ahora representa Servicio (0) o Producto (1) - INDEPENDIENTE de Oferta/Demanda
    // Oferta/Demanda se determina por el campo enviado: Precio = Oferta, Presupuesto = Demanda
    // En futuro: cuando la UI maneje creación de publicaciones, este método seguirá siendo válido para la BD,
    // pero los validadores específicos de Insomnia (Precio vs Presupuesto) pueden simplificarse en la interfaz
    public function crearPublicacion(Request $request)
    {
        $user = $request->user();

        // MODIFICADO: Validación agregada para soportar Tipo_Publicacion independiente (0=Servicio, 1=Producto)
        // NUEVO: Precio y Presupuesto ahora son opcionales (nullable) y mutuamente excluyentes
        // Solo uno de ellos puede estar presente para determinar si es Oferta o Demanda
        $rules = [
            'id_usuario' => ['nullable', 'integer', 'exists:users,id'],
            'Nombre' => ['required', 'string', 'max:255'],
            'Descripcion' => ['required', 'string', 'max:255'],
            'Tipo_Publicacion' => ['required', 'integer', 'in:0,1'], // MODIFICADO: 0=Servicio, 1=Producto
            'visible' => ['required', 'integer', 'in:0,1'],
            'Precio' => ['nullable', 'integer', 'min:0'], // NUEVO: Para Ofertas
            'Presupuesto' => ['nullable', 'integer', 'min:0'], // NUEVO: Para Demandas
        ];

        $validated = $request->validate($rules);

        // Si no especifica id_usuario, usa el usuario logueado
        $idUsuario = $validated['id_usuario'] ?? $user->id;

        // NUEVO: Determinar si es Oferta o Demanda basándose en cuál campo está presente en el request
        // Esto reemplaza la lógica anterior que dependía de Tipo_Publicacion
        $tieneprecio = $request->filled('Precio');
        $tienePresupuesto = $request->filled('Presupuesto');

        // NUEVO: Validación: debe proporcionar exactamente UNO de los dos campos
        if (!$tieneprecio && !$tienePresupuesto) {
            return response()->json(['message' => 'Debe proporcionar Precio (Oferta) o Presupuesto (Demanda)'], 400);
        }

        if ($tieneprecio && $tienePresupuesto) {
            return response()->json(['message' => 'No puede proporcionar ambos Precio y Presupuesto'], 400);
        }

        // MODIFICADO: Crear publicación con Tipo_Publicacion independiente (ahora no determina Oferta/Demanda)
        $publicacionId = \DB::table('publicacions')->insertGetId([
            'id_usuario' => $idUsuario,
            'Nombre' => $validated['Nombre'],
            'Descripcion' => $validated['Descripcion'],
            'Tipo_Publicacion' => $validated['Tipo_Publicacion'], // 0=Servicio, 1=Producto (NUEVO significado)
            'visible' => $validated['visible'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // NUEVO: Crear entrada en ofertas o demandas según corresponda
        // Lógica: Si existe 'Precio' en request -> inserta en tabla ofertas
        //         Si existe 'Presupuesto' en request -> inserta en tabla demandas
        if ($tieneprecio) {
            \DB::table('ofertas')->insert([
                'id_publicacion' => $publicacionId,
                'Precio' => $validated['Precio'], // NUEVO: obtenido del request
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            \DB::table('demandas')->insert([
                'id_publicacion' => $publicacionId,
                'Presupuesto' => $validated['Presupuesto'], // NUEVO: obtenido del request
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // MODIFICADO: Obtener la publicación completa para retornar
        // Se especifica 'publicacions.id' explícitamente para evitar ambigüedad en los joins
        $publicacion = \DB::table('publicacions')
            ->where('publicacions.id', $publicacionId) // MODIFICADO: especificar tabla para el where
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
}

