import axios from 'axios';

interface EnviarMensajeConImagenData {
    conversacion_id: number;
    imagen_base64: string; // base64 de la imagen
}

interface RespuestaEnviarMensajeConImagen {
    success: boolean;
    mensaje: {
        id: number;
        contenido: string;
        esDelUsuarioPrincipal: boolean;
        fecha: string;
        hora: string;
        esImagen: boolean;
        imagenUrl: string;
    };
}

/**
 * Enviar mensaje con imagen al servidor
 * @param conversacionId - ID de la conversación
 * @param imagenBase64 - Imagen en formato base64
 * @returns Respuesta del servidor con el mensaje guardado
 */
export async function enviarMensajeConImagen(
    conversacionId: number,
    imagenBase64: string
): Promise<RespuestaEnviarMensajeConImagen> {
    try {
        const response = await axios.post('/chat/enviar-mensaje-imagen', {
            conversacion_id: conversacionId,
            imagen_base64: imagenBase64,
        });
        return response.data;
    } catch (error) {

        if(axios.isAxiosError(error)) {
            console.error('Mensaje: ', error.message);
            console.error('Estado HTTP: ', error.response?.status);
            console.error('Respuesta del servidor: ', error.response?.data);
            console.error('URL', error.config?.url);
            console.error('Método: ', error.config?.method);
        } else {
            console.error('Error inesperado: ', error);
        }
        //console.error('Error al enviar mensaje con imagen:', error);
        throw error;
    }
}
