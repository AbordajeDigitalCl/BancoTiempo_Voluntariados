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
    public function editarPerfil(Request $request)
    {
        $user = $request->user();

        return Inertia::render('EditarPerfil', [
            'datosUsuario' => [
                'nombre' => $user->name ?? '',
                'apellidoPaterno' => $user->Apellido_Paterno ?? '',
                'apellidoMaterno' => $user->Apellido_Materno ?? '',
                'email' => $user->email ?? '',
                'contrasena' => '',
                'confirmarContrasena' => '',
                'fechaNacimiento' => $user->Age ?? '',
                'telefono' => $user->Telefono ?? '',
                'direccion' => $user->Direccion ?? '',
                'ciudad' => $user->Ciudad ?? '',
            ]
        ]);
    }

    public function guardarPerfil(Request $request)
    {
        $user = $request->user();

        $rules = [
            'nombre' => ['required', 'string', 'max:255'],
            'apellidoPaterno' => ['nullable', 'string', 'max:255'],
            'apellidoMaterno' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
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

        $user->update($updateData);

        return redirect()->route('verperfil');
    }

    public function verPerfil(Request $request)
    {
        $user = $request->user();

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
                'costo' => $pub->Tipo_Publicacion == 0 ? 
                    ($pub->Precio ?? 0) : 
                    ($pub->Presupuesto ?? 0),
                'tipo' => $pub->Tipo_Publicacion == 0 ? 'Servicio' : 'Producto',
                'ofertaDemanda' => $pub->Tipo_Publicacion == 0 ? 'Oferta' : 'Demanda',
                'imagen' => 'https://picsum.photos/200?random=' . $pub->id,
                'estado' => 'Activo',
                'fecha' => $pub->created_at->format('Y-m-d'),
            ])
            ->toArray();

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

        $datosPublicacion = [
            'id' => $publicacion->id,
            'titulo' => $publicacion->Nombre,
            'descripcion' => $publicacion->Descripcion,
            'costo' => $publicacion->Tipo_Publicacion == 0 ? 
                ($publicacion->Precio ?? 0) : 
                ($publicacion->Presupuesto ?? 0),
            'tipo' => $publicacion->Tipo_Publicacion == 0 ? 'Servicio' : 'Producto',
            'ofertaDemanda' => $publicacion->Tipo_Publicacion == 0 ? 'Oferta' : 'Demanda',
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

        $rules = [
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string', 'max:255'],
            'costo' => ['required', 'integer', 'min:0'],
            'ofertaDemanda' => ['required', 'in:Oferta,Demanda'],
        ];

        $validated = $request->validate($rules);

        $tipoAnterior = $publicacion->Tipo_Publicacion;
        $tipoNuevo = $validated['ofertaDemanda'] === 'Oferta' ? 0 : 1;
        $cambioDeTipo = $tipoAnterior !== $tipoNuevo;

        $publicacion->update([
            'Nombre' => $validated['titulo'],
            'Descripcion' => $validated['descripcion'],
            'Tipo_Publicacion' => $tipoNuevo,
        ]);

        if ($cambioDeTipo) {
            if ($tipoAnterior === 0) {
                \DB::table('ofertas')->where('id_publicacion', $publicacion->id)->delete();
                \DB::table('demandas')->insert([
                    'id_publicacion' => $publicacion->id,
                    'Presupuesto' => $validated['costo'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                \DB::table('demandas')->where('id_publicacion', $publicacion->id)->delete();
                \DB::table('ofertas')->insert([
                    'id_publicacion' => $publicacion->id,
                    'Precio' => $validated['costo'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } else {
            if ($tipoNuevo === 0) {
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
                $fechaMensaje = \Carbon\Carbon::parse($msg->created_at);

                return [
                    'id' => $msg->id,
                    'contenido' => $msg->Tipo_Mensaje === 1 ? '[Imagen compartida]' : $msg->Mensaje,
                    'esDelUsuarioPrincipal' => $msg->id_usuario_origen === $user->id,
                    'fecha' => $fechaMensaje->format('d/m/Y'),
                    'hora' => $fechaMensaje->format('H:i'),
                    'esImagen' => $msg->Tipo_Mensaje === 1,
                    'imagenUrl' => $msg->Tipo_Mensaje === 1 ? '/storage/uploads/' . $msg->Mensaje : null,
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

    public function enviarMensaje(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'conversacion_id' => ['required', 'integer'],
            'contenido' => ['required', 'string', 'max:255'],
            'es_imagen' => ['nullable', 'boolean'],
            'nombre_archivo' => ['nullable', 'string'],
        ]);

        $conversacion = \App\Models\Conversation::find($validated['conversacion_id']);

        if (!$conversacion) {
            return response()->json(['error' => 'Conversación no encontrada'], 404);
        }

        if ($conversacion->user_one_id !== $user->id && $conversacion->user_two_id !== $user->id) {
            return response()->json(['error' => 'No tienes acceso a esta conversación'], 403);
        }

        $otroUsuarioId = $conversacion->user_one_id === $user->id ? $conversacion->user_two_id : $conversacion->user_one_id;

        $tipoMensaje = $validated['es_imagen'] ? 1 : 0;
        $contenido = $validated['es_imagen'] ? ($validated['nombre_archivo'] ?? $validated['contenido']) : $validated['contenido'];

        \DB::table('mensajes')->insert([
            'id_usuario_origen' => $user->id,
            'id_usuario_destino' => $otroUsuarioId,
            'Mensaje' => $contenido,
            'Tipo_Mensaje' => $tipoMensaje,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->noContent();
    }

}

