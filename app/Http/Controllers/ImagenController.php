<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImagenController extends Controller
{

    /**
     * Obtiene y sirve una imagen almacenada en el directorio de archivos subidos.
     *
     * - Construye la ruta física del archivo dentro de storage/app/uploads/.
     * - Registra información de depuración y advertencias en el log.
     * - Comprueba si el archivo existe; si no, devuelve una respuesta HTTP 404.
     * - Si existe, devuelve el archivo mediante una respuesta HTTP que permite
     *   identificar su tipo MIME.
     *
     * Los archivos pueden corresponder a imágenes de perfil, publicaciones
     * o mensajes del chat.
     *
     * Archivos relacionados:
     * - routes/web.php: definición de la ruta que permite solicitar imágenes.
     * - routes/api.php: posibles rutas que utilizan esta funcionalidad.
     * - servicioImagenesPublicacion.ts: solicitudes de imágenes de publicaciones.
     * - ImageZoomModal.tsx: visualización ampliada de imágenes.
     *
     * @param string $filename Nombre del archivo que se desea mostrar.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     *         Devuelve el archivo solicitado o una respuesta JSON con estado 404
     *         si no se encuentra.
     */
    public function servirImagen($filename)
    {
        $path = storage_path('app/uploads/' . $filename);
        
        \Log::info('Servir imagen - Debug', [
            'filename' => $filename,
            'path' => $path,
            'exists' => file_exists($path),
        ]);
        
        if (!file_exists($path)) {
            \Log::warning('Imagen no encontrada', [
                'filename' => $filename,
                'path' => $path,
                'uploadDir' => storage_path('app/uploads'),
            ]);
            return response()->json(['error' => 'Imagen no encontrada: ' . $filename], 404);
        }
        
        return response()->file($path);
    }
}
