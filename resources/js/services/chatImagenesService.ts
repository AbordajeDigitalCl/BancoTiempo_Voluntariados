/**
 * Gestor de mensajes con imágenes persistidas en chat
 * 
 * Almacenamiento: localStorage (persiste durante la sesión del navegador)
 * Estructura: cada conversación tiene su propia lista de mensajes con imágenes
 * 
 * Patrón similar a estadosPublicaciones.ts pero para almacenar mensajes completos
 */

import { Mensaje } from '@/mockData/datosMockChatMensajes';

interface MensajePersistido extends Mensaje {
    tempId?: number; // ID temporal para mensajes no sincronizados con backend
    ultimaActualizacion?: number;
    userId?: number; // ID del usuario que envió el mensaje
}

interface MensajesPorConversacion {
    tempMessages: MensajePersistido[]; // Mensajes creados localmente que aún no están en BD
    ultimaActualizacion: number;
}


 // Genera la clave de localStorage para una conversación
 
const generarClaveConversacion = (conversacionId: number): string => {
    return `chat_mensajes_${conversacionId}`;
};


 // Obtener datos persistidos de una conversación
 
const obtenerDatosConversacion = (conversacionId: number): MensajesPorConversacion => {
    try {
        const clave = generarClaveConversacion(conversacionId);
        const stored = localStorage.getItem(clave);
        return stored ? JSON.parse(stored) : { tempMessages: [], ultimaActualizacion: 0 };
    } catch (e) {
        console.error(`Error al leer mensajes de chat (conversación ${conversacionId}):`, e);
        return { tempMessages: [], ultimaActualizacion: 0 };
    }
};


 // Guardar datos de una conversación
 
const guardarDatosConversacion = (
    conversacionId: number,
    datos: MensajesPorConversacion
): void => {
    try {
        const clave = generarClaveConversacion(conversacionId);
        localStorage.setItem(clave, JSON.stringify(datos));
    } catch (e) {
        console.error(`Error al guardar mensajes de chat (conversación ${conversacionId}):`, e);
    }
};


 // Guardar un nuevo mensaje con imagen (no enviado al backend aún)
 
export const guardarMensajeConImagenTemporal = (
    conversacionId: number,
    mensaje: Mensaje,
    userId?: number
): void => {
    const datos = obtenerDatosConversacion(conversacionId);
    
    // Asignar un tempId único
    const tempId = Math.max(...datos.tempMessages.map(m => m.tempId || 0), 0) + 1;
    
    const mensajePersistido: MensajePersistido = {
        ...mensaje,
        tempId,
        id: -tempId, // ID negativo para marcar como temporal
        userId,
    };

    datos.tempMessages.push(mensajePersistido);
    datos.ultimaActualizacion = Date.now();
    
    guardarDatosConversacion(conversacionId, datos);
};


 // Obtener mensajes temporales de una conversación
 
export const obtenerMensajesTemporales = (conversacionId: number, usuarioActualId?: number): Mensaje[] => {
    const datos = obtenerDatosConversacion(conversacionId);
    return datos.tempMessages.map(m => ({
        ...m,
        id: m.tempId || Math.abs(m.id), // Normalizar ID
        esDelUsuarioPrincipal: usuarioActualId ? m.userId === usuarioActualId : m.esDelUsuarioPrincipal,
    }));
};


// Guardar una imagen en un mensaje temporal
 
export const guardarImagenMensaje = (
    conversacionId: number,
    tempId: number,
    base64Data: string,
    nombreArchivo?: string
): void => {
    const datos = obtenerDatosConversacion(conversacionId);
    const mensaje = datos.tempMessages.find(m => m.tempId === tempId);
    
    if (mensaje) {
        mensaje.imagenUrl = base64Data;
        mensaje.esImagen = true;
        mensaje.ultimaActualizacion = Date.now();
        guardarDatosConversacion(conversacionId, datos);
    }
};


// Obtener una imagen de un mensaje temporal
 
export const obtenerImagenMensaje = (
    conversacionId: number,
    tempId: number
): string | null => {
    const datos = obtenerDatosConversacion(conversacionId);
    const mensaje = datos.tempMessages.find(m => m.tempId === tempId);
    return mensaje?.imagenUrl || null;
};


// Obtener todos los mensajes temporales (incluyendo images)
 
export const obtenerTodasLasImagenes = (conversacionId: number): Record<number, string> => {
    const datos = obtenerDatosConversacion(conversacionId);
    const result: Record<number, string> = {};
    
    datos.tempMessages.forEach((msg) => {
        if (msg.imagenUrl && msg.tempId) {
            result[msg.tempId] = msg.imagenUrl;
        }
    });
    
    return result;
};


// Limpiar mensajes temporales de una conversación específica
 
export const limpiarMensajesTemporales = (conversacionId: number): void => {
    try {
        const clave = generarClaveConversacion(conversacionId);
        localStorage.removeItem(clave);
    } catch (e) {
        console.error(`Error al limpiar mensajes de chat (conversación ${conversacionId}):`, e);
    }
};


//Limpiar mensajes de todos los chats
 
export const limpiarTodosMensajes = (): void => {
    try {
        const claves = Object.keys(localStorage);
        claves.forEach((clave) => {
            if (clave.startsWith('chat_mensajes_')) {
                localStorage.removeItem(clave);
            }
        });
    } catch (e) {
        console.error('Error al limpiar todos los mensajes de chat:', e);
    }
};


// Eliminar un mensaje temporal específico
 
export const eliminarMensajemporal = (
    conversacionId: number,
    tempId: number
): void => {
    const datos = obtenerDatosConversacion(conversacionId);
    datos.tempMessages = datos.tempMessages.filter(m => m.tempId !== tempId);
    guardarDatosConversacion(conversacionId, datos);
};
