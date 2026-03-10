<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Conversation;
use Illuminate\Support\Facades\DB;

class ConversationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Crea una conversación de prueba entre Jose (ID 1) y Juan (ID 2)
     */
    public function run(): void
    {
        $jose = DB::table('users')->find(1);
        $juan = DB::table('users')->where('email', 'juan@test.com')->first();

        if (!$jose || !$juan) {
            $this->command->warn('No se encontraron los usuarios Jose o Juan.');
            return;
        }

        $conversacion = Conversation::updateOrCreate(
            [
                'user_one_id' => $jose->id,
                'user_two_id' => $juan->id,
            ],
            [
                'user_one_id' => $jose->id,
                'user_two_id' => $juan->id,
            ]
        );


        DB::table('mensajes')->insert([
            'id_usuario_origen' => $juan->id,
            'id_usuario_destino' => $jose->id,
            'Mensaje' => 'Hola, te quisiera preguntar por el servicio que me ofreciste',
            'Tipo_Mensaje' => 0, 
            'created_at' => now()->subMinutes(5),
            'updated_at' => now()->subMinutes(5),
        ]);


        DB::table('mensajes')->insert([
            'id_usuario_origen' => $jose->id,
            'id_usuario_destino' => $juan->id,
            'Mensaje' => 'Hola, si, todavía está disponible',
            'Tipo_Mensaje' => 0, 
            'created_at' => now()->subMinutes(2),
            'updated_at' => now()->subMinutes(2),
        ]);

        $this->command->info('Se creó 1 conversación con 2 mensajes de prueba entre Jose y Juan.');
    }
}
