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
            tipo_mensaje: 0,
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


    /**
     * @summary Envía una imagen como mensaje dentro de una conversación.
     *
     * @description
     * - Obtiene al usuario autenticado y valida el identificador de la conversación y los datos de la imagen en Base64.
     * - Comprueba que la conversación exista y que el usuario autenticado sea uno de sus participantes.
     * - Identifica al otro participante para establecerlo como destinatario del mensaje.
     * - Procesa la cadena Base64, eliminando el prefijo de datos si está presente.
     * - Decodifica la imagen y verifica su tipo MIME, permitiendo únicamente JPEG, PNG, GIF y WebP.
     * - Comprueba que el tamaño de la imagen no supere los 5 MB.
     * - Genera un nombre único para el archivo y lo almacena en storage/app/uploads.
     * - Registra el mensaje en la tabla mensajes, utilizando Tipo_Mensaje = 1 para identificarlo como imagen.
     * - Dispara el evento ChatMessageCreated para notificar al destinatario de la nueva imagen en tiempo real.
     * - Devuelve una respuesta JSON con los datos del mensaje y la URL de la imagen almacenada.
     *
     * @source Modelo Conversation, tabla conversations, tabla mensajes, directorio storage/app/uploads
     *         y evento ChatMessageCreated.
     * @route Definida en routes/web.php.
     * @frontend enviarMensajeConImagen.ts, AreaChat.tsx, Chat.tsx y GloboMensaje.tsx.
     *
     * @param \Illuminate\Http\Request $request Solicitud HTTP con el identificador de conversación y la imagen en Base64.
     *
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado del envío y los datos del mensaje creado.
     *
     * @throws \Illuminate\Validation\ValidationException Si faltan los datos obligatorios.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Si la conversación no existe (404)
     *         o el usuario no tiene acceso a ella (403).
     */
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
            tipo_mensaje:1,
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


    /**
     * Marca como leídos los mensajes recibidos de otro participante en una conversación.
     *
     * - Obtiene el usuario autenticado mediante la solicitud.
     * - Valida que se proporcione un ID de conversación existente.
     * - Comprueba que el usuario autenticado sea uno de los participantes.
     * - Identifica al otro participante de la conversación.
     * - Actualiza la fecha de lectura (read_at) de los mensajes recibidos
     *   que todavía no han sido marcados como leídos.
     * - Calcula el total global de mensajes no leídos del usuario autenticado.
     * - Devuelve una respuesta JSON con el resultado de la operación.
     *
     * Tablas involucradas:
     * - conversations: identifica a los participantes de la conversación.
     * - mensajes: actualiza el estado de lectura y calcula los mensajes pendientes.
     *
     * @param \Illuminate\Http\Request $request Solicitud con el ID de la conversación.
     *
     * @return \Illuminate\Http\JsonResponse
     *         Devuelve el ID de la conversación, la cantidad de mensajes actualizados
     *         y el total global de mensajes no leídos.
     *
     * @throws \Illuminate\Validation\ValidationException Si el ID de conversación
     *         no es válido o no existe.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Si el usuario
     *         no tiene autorización para acceder a la conversación (403).
     */
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
}
