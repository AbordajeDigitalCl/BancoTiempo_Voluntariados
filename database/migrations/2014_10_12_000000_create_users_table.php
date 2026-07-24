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
        //CP y Identificacion, ¿Qué son?
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('Apellido_Paterno');
            $table->string('Apellido_Materno');
            $table->date('Age');
            $table->string('Telefono');
            $table->string('Direccion');
            $table->string('Ciudad');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
       DB::unprepared("DROP PROCEDURE IF EXISTS calcular_distancia;");
        DB::unprepared("
       CREATE FUNCTION IF NOT EXISTS calcular_distancia(
    p_lat    DECIMAL(10,8),
    p_lng    DECIMAL(11,8),
    loc_lat  DECIMAL(10,8),
    loc_lng  DECIMAL(11,8)
)
RETURNS DECIMAL(10,2)
DETERMINISTIC
BEGIN
    DECLARE distancia DECIMAL(10,2);

    SET distancia = (
        6371 * ACOS(
            LEAST(1.0, GREATEST(-1.0,
                COS(RADIANS(p_lat))
                * COS(RADIANS(loc_lat))
                * COS(RADIANS(loc_lng) - RADIANS(p_lng))
                + SIN(RADIANS(p_lat))
                * SIN(RADIANS(loc_lat))
            ))
        )
    );

    RETURN distancia;
END
 ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS calcular_distancia;");
        Schema::dropIfExists('users');
    }
};
