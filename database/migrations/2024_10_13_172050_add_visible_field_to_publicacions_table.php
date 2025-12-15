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
        Schema::table('publicacions', function (Blueprint $table) {
            $table->boolean('visible')->default(true)->after('Tipo_Publicacion'); // Adding the new boolean column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publicacions', function (Blueprint $table) {
            $table->dropColumn('visible'); // Dropping the new column in case of rollback
        });
    }
};
