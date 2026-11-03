<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ChatController extends Controller
{

    /**
     * @summary Muestra la interfaz principal del chat del usuario autenticado.
     *
     * @description
     * - Obtiene las conversaciones en las que participa el usuario autenticado.
     * - Recupera el resumen de cada conversación mediante el procedimiento almacenado
     *   obtener_resumen_conversaciones_por_usuario_id.
     * - Obtiene los mensajes del usuario mediante el procedimiento almacenado
     *   obtener_mensajes_de_conversaciones_por_usuario_id.
     * - Agrupa los mensajes según el identificador de su conversación.
     * - Transforma los mensajes al formato esperado por el frontend, incluyendo:
     *   - Identificador del mensaje.
     *   - Contenido del mensaje o indicador de imagen compartida.
     *   - Identificación de los mensajes enviados por el usuario autenticado.
     *   - Fecha y hora del mensaje.
     *   - Indicador de si el mensaje contiene una imagen y su URL correspondiente.
     * - Envía las conversaciones y sus mensajes agrupados a la página Chat.
     *
     * @source Tablas conversations y mensajes; procedimientos almacenados
     *         obtener_resumen_conversaciones_por_usuario_id y
     *         obtener_mensajes_de_conversaciones_por_usuario_id.
     * @view resources/js/Pages/Chat.tsx
     * @route Definida en routes/web.php.
     *
     * @param \Illuminate\Http\Request $request Solicitud HTTP con los datos del usuario autenticado.
     *
     * @return \Inertia\Response Página Chat con el resumen de conversaciones y los mensajes agrupados por conversación.
     */
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
}
