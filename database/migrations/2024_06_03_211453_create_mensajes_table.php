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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensajes');
    }
};
