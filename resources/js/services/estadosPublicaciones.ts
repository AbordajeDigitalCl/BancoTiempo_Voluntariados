/**
 * Gestor de estados de publicaciones (ON/OFF)
 * 
 * Estados:
 * - ON (true): La publicación está "Activo"
 * - OFF (false): La publicación está "Desactivado" (si es Servicio) o "Agotado" (si es Producto)
 * 
 * Almacenamiento: localStorage (persiste durante la sesión del navegador)
 * Se usa en vistas de Perfil y Edición de Publicaciones
 */

const STORAGE_KEY = 'publicaciones_estados';

interface EstadosMap {
    [publicacionId: number]: boolean; // true = ON (Activo), false = OFF (Desactivado/Agotado)
}


 // Obtener todos los estados de publicaciones desde localStorage
 
const obtenerTodosLosEstados = (): EstadosMap => {
    try {
        const stored = localStorage.getItem(STORAGE_KEY);
        return stored ? JSON.parse(stored) : {};
    } catch (e) {
        console.error('Error al leer estados de publicaciones:', e);
        return {};
    }
};


 // Guardar todos los estados en localStorage
 
const guardarEstados = (estados: EstadosMap): void => {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(estados));
    } catch (e) {
        console.error('Error al guardar estados de publicaciones:', e);
    }
};

/**
 * Obtener el estado ON/OFF de una publicación específica
 * Por defecto devuelve true (Activo) si no existe
 */
export const obtenerEstadoPublicacion = (publicacionId: number): boolean => {
    const estados = obtenerTodosLosEstados();
    return estados[publicacionId] !== undefined ? estados[publicacionId] : true; // true = ON (Activo)
};


 // Cambiar el estado ON/OFF de una publicación
 
export const cambiarEstadoPublicacion = (publicacionId: number, nuevoEstado: boolean): void => {
    const estados = obtenerTodosLosEstados();
    estados[publicacionId] = nuevoEstado;
    guardarEstados(estados);
};

/**
 * Obtener el texto del estado dependiendo del tipo de publicación
 * ON (true) = "Activo"
 * OFF (false) = "Desactivado" (Servicio) o "Agotado" (Producto)
 */
export const obtenerTextoEstado = (publicacionId: number, tipo: 'Servicio' | 'Producto'): 'Activo' | 'Desactivado' | 'Agotado' => {
    const estadoOn = obtenerEstadoPublicacion(publicacionId);
    
    if (estadoOn) {
        return 'Activo';
    }
    
    // Si está OFF (false), devolver Desactivado o Agotado según el tipo
    return tipo === 'Servicio' ? 'Desactivado' : 'Agotado';
};


 // Limpiar todos los estados (útil para logout o reset)
 
export const limpiarTodosLosEstados = (): void => {
    try {
        localStorage.removeItem(STORAGE_KEY);
    } catch (e) {
        console.error('Error al limpiar estados de publicaciones:', e);
    }
};


 // Obtener mapa completo de estados (para debugging)
 
export const obtenerMapaCompleto = (): EstadosMap => {
    return obtenerTodosLosEstados();
};
