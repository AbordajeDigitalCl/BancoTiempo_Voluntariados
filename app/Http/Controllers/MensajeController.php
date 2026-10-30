<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MensajeController extends Controller
{

    /**
     * @summary Obtiene los mensajes de una conversación específica.
     *
     * @description
     * - Valida que se haya proporcionado un identificador de conversación existente.
     * - Recupera la conversación solicitada y comprueba que el usuario autenticado sea uno de sus participantes.
     * - Impide el acceso a conversaciones que no pertenecen al usuario autenticado.
     * - Obtiene los mensajes mediante el procedimiento almacenado obtener_mensajes_conversacion,
     *   utilizando los identificadores de ambos participantes.
     * - Transforma los mensajes al formato esperado por el frontend, incluyendo:
     *   - Identificador y contenido del mensaje.
     *   - Indicador de si el mensaje fue enviado por el usuario autenticado.
     *   - Fecha y hora del mensaje.
     *   - Indicador de si contiene una imagen y su URL correspondiente.
     * - Devuelve los mensajes en formato JSON junto con el identificador de la conversación.
     *
     * @source Tabla conversations; procedimiento almacenado obtener_mensajes_conversacion;
     *         tabla mensajes.
     * @route Definida en routes/web.php.
     *
     * @param \Illuminate\Http\Request $request Solicitud HTTP con el identificador de la conversación.
     *
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el estado de la operación,
     *         el identificador de la conversación y sus mensajes.
     *
     * @throws \Illuminate\Validation\ValidationException Si el identificador no es válido o la conversación no existe.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Si el usuario no tiene acceso a la conversación (403).
     */
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


    /**
     * @summary Envía un mensaje de texto dentro de una conversación.
     *
     * @description
     * - Obtiene al usuario autenticado y valida los datos recibidos: identificador de conversación y contenido del mensaje.
     * - Comprueba que la conversación exista y que el usuario autenticado sea uno de sus participantes.
     * - Identifica al otro participante para establecerlo como destinatario del mensaje.
     * - Registra el mensaje en la tabla mensajes, indicando que es de tipo texto (Tipo_Mensaje = 0).
     * - Dispara el evento ChatMessageCreated para notificar al destinatario y actualizar la información de mensajes no leídos en tiempo real.
     * - Si la petición espera una respuesta JSON, devuelve los datos del mensaje recién creado, incluyendo su contenido, fecha y hora.
     * - Si se trata de una petición tradicional, redirige al usuario a la página del chat.
     *
     * @source Modelo Conversation, tabla conversations, tabla mensajes y evento ChatMessageCreated.
     * @route Definida en routes/web.php.
     * @frontend enviarMensaje.ts, AreaChat.tsx, Chat.tsx y GloboMensaje.tsx.
     * @view resources/js/Pages/Chat.tsx
     *
     * @param \Illuminate\Http\Request $request Solicitud HTTP con el identificador de conversación y el contenido del mensaje.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     *         Devuelve los datos del mensaje en formato JSON o redirige a /chat.
     *
     * @throws \Illuminate\Validation\ValidationException Si los datos recibidos no cumplen las reglas de validación.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Si la conversación no existe (404)
     *         o el usuario no tiene acceso a ella (403).
     */
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

    
}
