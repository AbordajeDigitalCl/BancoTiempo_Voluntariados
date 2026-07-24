<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Ángel, coloque el parámetro nuevo y le puse un comentario al lado para que sepas cual es
     * Que tengas un excelente resto del día.
     * 
     * Jose Johnen 2026/03/02
     */
    public function up(): void
{
        //Falta el "CloseAt DateTime"
        Schema::create('publicacions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_usuario')->unsigned();
            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('cascade');
            $table->string('Nombre');
            $table->string('Descripcion');
            $table->tinyInteger('Tipo_Publicacion'); //Parámetro que al parecer define Oferta/Demanda
            $table->tinyInteger('Tipo_Beneficio'); //Parámetro que define Producto/Servicio 0 = servicio 1 = producto
            $table->decimal('location_lat', 10, 7)->nullable();
            $table->decimal('location_lng', 10, 7)->nullable();
            $table->string('location_address', 500)->nullable();
//  Editado por: Gloria Cornelio 
// Se migran los campos de advertisements a publicacions para unificar en una sola tabla
// advertisements queda obsoleta y será eliminada
            $table->string('title', 80)->nullable();
            $table->string('description', 1000)->nullable();
            $table->unsignedInteger('hours')->nullable();
            $table->enum('type', ['Servicio', 'Producto'])->nullable();
            $table->enum('mode', ['Oferta', 'Demanda'])->nullable();
            $table->enum('status', ['active', 'paused', 'finished'])->default('active');
            $table->string('author')->nullable();
            $table->json('image_urls')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->timestamps();
        });
        DB::unprepared("DROP PROCEDURE IF EXISTS obtener_publicaciones_cercanas;");
        DB::unprepared("
            CREATE PROCEDURE if NOT EXISTS obtener_publicaciones_cercanas(
            IN p_lat DECIMAL(10,8),
            IN p_lng DECIMAL(11,8)
        )
            BEGIN
            SELECT
            id,
            Nombre,
            Descripcion,
            location_lat,
            location_lng,
            calcular_distancia(p_lat, p_lng, location_lat, location_lng) AS distancia_km
            FROM publicacions
            WHERE location_lat IS NOT NULL
            AND location_lng IS NOT NULL
            HAVING distancia_km <= 50
            ORDER BY distancia_km ASC;
            END
            ");
        }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS obtener_publicaciones_cercanas;");
        Schema::dropIfExists('publicacions');
    }
};
