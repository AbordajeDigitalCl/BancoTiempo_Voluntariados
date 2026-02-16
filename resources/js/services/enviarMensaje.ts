import { router } from '@inertiajs/react';

interface EnviarMensajeData extends Record<string, any> {
    conversacion_id: number;
    contenido: string;
    es_imagen?: boolean;
    nombre_archivo?: string;
}

export async function enviarMensaje(datos: EnviarMensajeData) {
    return new Promise((resolve, reject) => {
        router.post('/chat/enviar-mensaje', datos, {
            onSuccess: () => resolve(null),
        });
    });
}
