<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title', 80);
            $table->string('description', 1000);
            $table->unsignedInteger('hours');
            $table->enum('type', ['Servicio', 'Producto']);
            $table->enum('mode', ['Oferta', 'Demanda']);
            $table->enum('status', ['active', 'paused', 'finished'])->default('active');
            $table->string('author')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('image_urls')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->decimal('location_lat', 10, 7)->nullable();
            $table->decimal('location_lng', 10, 7)->nullable();
            $table->string('location_address', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};