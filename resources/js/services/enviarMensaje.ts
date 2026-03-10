import axios from 'axios';

interface EnviarMensajeData extends Record<string, any> {
    conversacion_id: number;
    contenido: string;
    es_imagen?: boolean;
    nombre_archivo?: string;
}

interface RespuestaEnviarMensaje {
    success: boolean;
    mensaje: {
        id: number;
        contenido: string;
        esDelUsuarioPrincipal: boolean;
        fecha: string;
        hora: string;
    };
}

export async function enviarMensaje(datos: EnviarMensajeData): Promise<RespuestaEnviarMensaje> {
    try {
        const response = await axios.post('/chat/enviar-mensaje', datos);
        return response.data;
    } catch (error) {
        console.error('Error al enviar mensaje:', error);
        throw error;
    }
}
