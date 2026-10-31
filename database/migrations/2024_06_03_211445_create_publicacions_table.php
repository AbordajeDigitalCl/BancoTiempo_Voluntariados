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
            $table->enum('status', ['active', 'paused', 'finished', 'deleted'])->default('active');
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

        /*
        * Se agregó este procedimiento a la base de datos para incluir los filtros de la consulta de
        * publicaciones.
        */
        DB::unprepared("DROP PROCEDURE IF EXISTS obtener_publicaciones_cercanas_filtradas;");
        DB::unprepared("
            CREATE PROCEDURE if NOT EXISTS obtener_publicaciones_cercanas_filtradas(
            IN p_q VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_type VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_distance VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            IN p_sort VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
                AND (
				    p_q = ''
                    OR Nombre LIKE CONCAT('%', p_q, '%')
                    OR Descripcion LIKE CONCAT('%', p_q,'%')
                )
                AND (
				    p_type = 'all'
                    OR publicacions.type = p_type
                )
                HAVING
                    p_distance = 'Cualquiera'
                    OR distancia_km <= CAST(p_distance AS DECIMAL(10,2))
                ORDER BY 
				CASE WHEN p_sort = 'newest' THEN created_at END DESC,
                CASE WHEN p_sort = 'oldest' THEN created_at END ASC;
            END
        ");

        DB::unprepared("DROP PROCEDURE IF EXISTS obtener_publicaciones_perfil_por_usuario_id;");
        DB::unprepared("
            CREATE PROCEDURE IF NOT EXISTS obtener_publicaciones_perfil_por_usuario_id(
                IN p_usuario_id BIGINT
            )
            BEGIN
                SELECT
                    p.id,
                    p.Nombre,
                    p.Descripcion,
                    p.Tipo_Publicacion,
                    p.Tipo_Beneficio,
                    p.created_at,
                    o.Precio,
                    d.Presupuesto,
                    img.Direccion AS imagenDireccion
                FROM publicacions p
                LEFT JOIN ofertas o
                    ON p.id = o.id_publicacion
                LEFT JOIN demandas d
                    ON p.id = d.id_publicacion
                LEFT JOIN(
                    SELECT
                        ip.id_publicacion,
                        MAX(i.Direccion) AS Direccion
                        FROM inter__imagen__public ip
                        INNER JOIN imagens i
                            ON ip.id_imagen = i.id
                        WHERE i.Tipo = 1
                        GROUP BY ip.id_publicacion
                ) img
                    ON p.id = img.id_publicacion
                WHERE p.id_usuario = p_usuario_id
                AND p.visible = 1;
            END
        ");

        DB::unprepared("DROP PROCEDURE IF EXISTS obtener_publicacion_para_editar;");
        DB::unprepared("
            CREATE PROCEDURE IF NOT EXISTS obtener_publicacion_para_editar(
                IN p_publicacion_id BIGINT,
                IN p_usuario_id BIGINT,
                IN p_es_admin BOOLEAN
            )
            BEGIN
                SELECT
                    p.id,
                    p.Nombre,
                    p.Descripcion,
                    p.Tipo_Publicacion,
                    p.Tipo_Beneficio,
                    o.Precio,
                    d.Presupuesto,
                    img.Direccion AS imagenDireccion,
                    img.id AS imagenId
                FROM publicacions p
                LEFT JOIN ofertas AS o 
                    ON p.id = o.id_publicacion 
                LEFT JOIN demandas AS d 
                    ON p.id = d.id_publicacion
                LEFT JOIN (
                    SELECT
                    ip.id_publicacion,
                    MAX(i.Direccion) AS Direccion,
                    MAX(i.id) AS id
                    FROM inter__imagen__public AS ip
                    INNER JOIN imagens i 
                        ON ip.id_imagen = i.id
                    WHERE i.Tipo = 1
                    GROUP BY ip.id_publicacion
                ) img
                    ON p.id = img.id_publicacion
                WHERE p.id = p_publicacion_id
                AND (
                    p_es_admin = TRUE
                    OR p.id_usuario = p_usuario_id
                )
                LIMIT 1;
            END
        ");

        Schema::table('publicacions', function(Blueprint $table) {
            $table->index(
                ['id_usuario', 'created_at'],
                'idx_publicacions_usuario_created'
            );
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS obtener_publicaciones_cercanas;");
        DB::unprepared("DROP PROCEDURE IF EXISTS obtener_publicaciones_cercanas_filtradas;");
        DB::unprepared("DROP PROCEDURE IF EXISTS obtener_publicaciones_perfil_por_usuario_id;");
        DB::unprepared("DROP PROCEDURE IF EXISTS obtener_publicacion_para_editar;");
        Schema::dropIfExists('publicacions');
        Schema::table('publicacions', function(Blueprint $table) {
            $table->dropIndex('idx_publicacions_usuario_created');
        });
    }
};
