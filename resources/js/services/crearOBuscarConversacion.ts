import { router } from '@inertiajs/react';

interface CrearOBuscarConversacionData extends Record<string, any> {
    usuario_id: number;
}

export async function crearOBuscarConversacion(datos: CrearOBuscarConversacionData): Promise<void> {
    router.post('/administrar-usuarios/crear-conversacion', datos);
}
