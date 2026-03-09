<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{

    public function up(): void {
        Schema::create('inter__imagen__public', function(Blueprint $table) {
            $table->id();
            $table->bigInteger('id_publicacion')->unsigned();
            $table->foreign('id_publicacion')->references('id')->on('publicacions')->onDelete('cascade');
            $table->bigInteger('id_imagen')->unsigned();
            $table->foreign('id_imagen')->references('id')->on('imagens')->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    public function down(): void{
        Schema::dropIfExists("inter__imagen_public");
    }

};
