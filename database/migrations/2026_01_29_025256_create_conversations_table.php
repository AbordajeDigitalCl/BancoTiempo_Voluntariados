<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_one_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_two_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_one_id', 'user_two_id']);
        });


        DB::unprepared("DROP PROCEDURE IF EXISTS existe_conversacion_por_participantes_id;");
        DB::unprepared("
            CREATE PROCEDURE if NOT EXISTS existe_conversacion_por_participantes_id(
                IN p_user_one_id BIGINT UNSIGNED,
                IN p_user_two_id BIGINT UNSIGNED
            )
            BEGIN
	            SELECT EXISTS(
		            SELECT 1
                    FROM conversations
                    WHERE (
			            user_one_id = p_user_one_id AND
                        user_two_id = p_user_two_id
                    ) OR (
			            user_one_id = p_user_two_id AND
                        user_two_id = p_user_one_id
                    )
                ) AS existe;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS existe_conversacion_por_participantes_id;");
        Schema::dropIfExists('conversations');
    }
};
