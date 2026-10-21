<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_one_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_two_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_one_id', 'user_two_id']);
        });


        DB::unprepared("DROP PROCEDURE IF EXISTS existe_conversacion_por_participantes_id;");
        DB::unprepared("
            CREATE PROCEDURE if NOT EXISTS existe_conversacion_por_participantes_id(
                IN p_user_one_id BIGINT UNSIGNED,
                IN p_user_two_id BIGINT UNSIGNED
            )
            BEGIN
	            SELECT EXISTS(
		            SELECT 1
                    FROM conversations
                    WHERE (
			            user_one_id = p_user_one_id AND
                        user_two_id = p_user_two_id
                    ) OR (
			            user_one_id = p_user_two_id AND
                        user_two_id = p_user_one_id
                    )
                ) AS existe;
            END
        ");
        DB::unprepared('DROP PROCEDURE IF EXISTS obtener_resumen_conversaciones');
        DB::unprepared("
            CREATE PROCEDURE IF NOT EXISTS obtener_resumen_conversaciones(
                IN p_usuario_id BIGINT
            )
            BEGIN
                SELECT
                    c.id AS id,
                    /*Identificar al otro usuario*/
                    CASE
                        WHEN c.user_one_id = p_usuario_id
                            THEN c.user_two_id
                        ELSE c.user_one_id
                    END AS usuarioId,
                    
                    /*Nombre completo*/
                    CONCAT_WS(
                        ' ',
                        u.name,
                        u.Apellido_Paterno
                    ) AS nombre,
                    
                    /*Foto de perfil más reciente*/
                    COALESCE(
                        CONCAT('/imagen/', img.Direccion),
                        CONCAT(
                            'https://picsum.photos/200?random=',
                            u.id
                        )
                    ) AS foto,
                    
                    /*Contenido del último mensaje*/
                    COALESCE(lm.Mensaje, '') AS ultimoMensaje,
                    
                    /*Tipo del último mensaje; 3 si no hay mensajes*/
                    COALESCE(lm.Tipo_Mensaje, 3) AS tipoUltimoMensaje,
                    
                    /*¿El último mensaje lo envió el usuario conectado?*/
                    COALESCE(
                        lm.id_usuario_origen = p_usuario_id,
                        0
                    ) AS esMensajeDelUsuarioPrincipal,
                    /*Fecha del último mensaje*/
                    COALESCE(
                        DATE_FORMAT(lm.created_at, '%d/%m/%Y'),
                        ''
                    ) AS fechaUltimoMensaje,
                    
                    /*Mensajes no leídos recibidos por el usuario conectado*/
                    (
                        SELECT COUNT(*)
                        FROM mensajes mn
                        WHERE mn.id_usuario_destino = p_usuario_id
                            AND mn.id_usuario_origen =
                                CASE
                                    WHEN c.user_one_id = p_usuario_id
                                        THEN c.user_two_id
                                    ELSE c.user_one_id
                                END
                            AND mn.read_at IS NULL
                    ) AS mensajesNoLeidos
                    
                FROM conversations c
                    
                /*Obtener al otro usuario*/
                INNER JOIN users u
                    ON u.id = CASE
                        WHEN c.user_one_id = p_usuario_id
                            THEN c.user_two_id
                        ELSE c.user_one_id
                    END
                /*Obtener el último mensaje una sola vez*/
                LEFT JOIN LATERAL(
                    SELECT
                        m.Mensaje,
                        m.Tipo_Mensaje,
                        m.id_usuario_origen,
                        m.created_at
                    FROM mensajes m
                    WHERE
                        (
                            m.id_usuario_origen = c.user_one_id
                            AND m.id_usuario_destino = c.user_two_id
                        )
                        OR
                        (
                            m.id_usuario_origen = c.user_two_id
                            AND m.id_usuario_destino = c.user_one_id
                        )
                    ORDER BY created_at DESC, m.id DESC
                    LIMIT 1
                ) lm ON TRUE
                
                /*Obtener la foto de perfil más reciente*/
                LEFT JOIN LATERAL(
                    SELECT i.Direccion
                    FROM imagens i
                    WHERE i.id_usuario_origen = u.id
                        AND i.Tipo = 0
                    ORDER BY i.created_at DESC, i.id DESC
                    LIMIT 1
                ) img ON TRUE
                
                /*Solo conversaciones del usuario conectado*/
                WHERE c.user_one_id = p_usuario_id
                    OR c.user_two_id = p_usuario_id
                ORDER BY c.id DESC;				
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS existe_conversacion_por_participantes_id;");
        DB::unprepared("DROP PROCEDURE IF EXISTS obtener_resumen_conversaciones;");
        Schema::dropIfExists('conversations');
    }
};
