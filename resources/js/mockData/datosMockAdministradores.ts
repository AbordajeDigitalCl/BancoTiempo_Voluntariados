/**
 * Mock data para administradores del sistema
 * Array con los IDs de usuarios que son administradores
 */

export const ADMINISTRADORES_IDS: number[] = [2, 4, 5];

/**
 * Función auxiliar para verificar si un usuario es administrador
 */
export const esAdministrador = (usuarioId: number): boolean => {
  return ADMINISTRADORES_IDS.includes(usuarioId);
};
