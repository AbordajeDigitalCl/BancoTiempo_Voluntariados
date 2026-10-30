<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConversacionController extends Controller
{

    /**
     * @summary Crea una conversación entre dos usuarios o reutiliza una existente.
     * @description
     * - Obtiene al usuario autenticado que inicia la operación.
     * - Valida que se haya proporcionado un identificador de usuario válido y que exista en la tabla `users`.
     * - Impide que un usuario intente iniciar una conversación consigo mismo.
     * - Ejecuta el procedimiento almacenado `existe_conversacion_por_participantes_id` para comprobar si ya existe una conversación entre ambos usuarios.
     * - Si no existe una conversación, registra una nueva en la tabla `conversations`, asociando a ambos participantes.
     * - Redirige al usuario a la página del chat.
     *
     * @source
     * - Tabla `users`: validación del usuario destinatario.
     * - Tabla `conversations`: almacenamiento de las conversaciones.
     * - Procedimiento almacenado `existe_conversacion_por_participantes_id`: búsqueda de conversaciones existentes entre dos participantes.
     *
     * @route Definida en routes/web.php.
     * @frontend
     * - crearOBuscarConversacion.ts
     * - FilaUsuario.tsx
     * - AdministrarUsuarios.tsx
     *
     * @param Request $request Petición HTTP con el identificador del usuario con quien se desea conversar.
     * @return \Illuminate\Http\RedirectResponse Redirección a la página del chat.
     * @throws \Illuminate\Validation\ValidationException Si el identificador del usuario no es válido.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Si se intenta crear una conversación consigo mismo (400).
     */
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

}
