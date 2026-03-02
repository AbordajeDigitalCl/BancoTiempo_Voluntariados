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
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publicacions');
    }
};
