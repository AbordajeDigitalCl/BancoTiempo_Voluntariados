<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_usuario_origen')->unsigned();
            $table->foreign('id_usuario_origen')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('id_usuario_destino')->unsigned();
            $table->foreign('id_usuario_destino')->references('id')->on('users')->onDelete('cascade');
            $table->string('Mensaje');
            $table->tinyInteger('Tipo_Mensaje');
            $table->timestamps();
        });

        DB::unprepared("DROP PROCEDURE IF EXISTS obtener_mensajes_conversacion;");
        DB::unprepared("
            CREATE PROCEDURE IF NOT EXISTS obtener_mensajes_conversacion(
	            IN p_user_one_id BIGINT,
                IN p_user_two_id BIGINT
            )
            BEGIN
	            SELECT *
                FROM mensajes
	            WHERE(
		            id_usuario_origen = p_user_one_id
                    AND id_usuario_destino = p_user_two_id
                )
                OR (
		            id_usuario_origen = p_user_two_id
                    AND id_usuario_destino = p_user_one_id
                )
                ORDER BY created_at ASC;
            END
            ");
        DB::unprepared("DROP PROCEDURE IF EXISTS obtener_mensajes_de_conversaciones_por_usuario_id;");
        DB::unprepared("
            CREATE PROCEDURE IF NOT EXISTS obtener_mensajes_de_conversaciones_por_usuario_id(
                IN p_usuario_id BIGINT
            )
            BEGIN
                SELECT
                    c.id AS conversation_id,
                    m.id,
                    m.id_usuario_origen,
                    m.id_usuario_destino,
                    m.Mensaje,
                    m.Tipo_Mensaje,
                    m.created_at
                FROM conversations c
                INNER JOIN mensajes m
                    ON(
                        m.id_usuario_origen = c.user_one_id
                        AND m.id_usuario_destino = c.user_two_id
                    )
                    OR (
                        m.id_usuario_origen = c.user_two_id
                        AND m.id_usuario_destino = c.user_one_id
                    )
                WHERE
                    c.user_one_id = p_usuario_id
                    OR c.user_two_id = p_usuario_id
                ORDER BY
                    c.id,
                    m.created_at ASC,
                    m.id ASC;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS obtener_mensajes_conversacion;");
        DB::unprepared("DROP PROCEDURE IF EXSITS obtener_mensajes_de_conversaciones_por_usuario_id;");
        Schema::dropIfExists('mensajes');
    }
};
