/**
 * Mapeo simple: ID Publicación → URL de imagen
 * Las imágenes están en storage/app/uploads/ y se acceden vía /api/file/{filename}
 */

export const imagenesMap: Record<number, string> = {
    1: "/api/file/699cf2b73cd63.jpg",
    2: "/api/file/699cf31ca66e9.jpg",
    
};


export const obtenerImagenPublicacion = (publicacionId: number): string | undefined => {
    return imagenesMap[publicacionId];
};
