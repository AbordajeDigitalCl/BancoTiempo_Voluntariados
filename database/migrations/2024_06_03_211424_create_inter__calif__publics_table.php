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
        Schema::create('inter__calif__publics', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_calificacion')->unsigned();
            $table->foreign('id_calificacion')->references('id')->on('calificacions')->onDelete('cascade');
            $table->bigInteger('id_publicacion')->unsigned();
            $table->foreign('id_publicacion')->references('id')->on('publicacions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inter__calif__publics');
    }
};
