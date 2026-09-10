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
        Schema::table('mensajes', function (Blueprint $table) {
            //
            $table->timestamp('read_at')->nullable()->after('updated_at');

            //Para conteo global no leìdos por usuario destino
            $table->index(['id_usuario_destino', 'read_at'], 'mensajes_destino_read_at_idx');

            //Para queries de conversación
            $table->index(['id_usuario_origen', 'id_usuario_destino', 'created_at'],
            'mensajes_origen_destino_created_idx');
        });
/*
        //Marca historicos como leídos
        DB::table('mensajes')
        ->whereNull('read_at')
        ->udpate(['read_at'=>now()]);
*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mensajes', function (Blueprint $table) {
            //

            $table->dropIndex('mensajes_destino_read_at_idx');

            $table->dropIndex('mensajes_origen_destino_created_idx');

            $table->dropColumn('read_at');
        });
    }
};
