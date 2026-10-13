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
            CREATE PROCEDURE if NOT EXISTS obtener_mensajes_conversacion(
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS obtener_mensajes_conversacion;");
        Schema::dropIfExists('mensajes');
    }
};
