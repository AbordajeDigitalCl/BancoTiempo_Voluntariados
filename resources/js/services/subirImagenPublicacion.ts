import { actualizarCSRFToken } from './actualizarCSRFToken';

// Servicio para subir imagen a una publicación
// Similar a subirImagen.ts pero para publicaciones
// Obtiene token fresco antes de upload, sube imagen, y sincroniza token después
export async function subirImagenPublicacion(archivo: File, publicacionId: number): Promise<any> {
    try {
        // Obtiene un token CSRF completamente fresco del servidor
        const refreshResponse = await fetch('/refresh-csrf', {
            method: 'GET',
            credentials: 'include',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!refreshResponse.ok) {
            throw new Error('No se pudo obtener token CSRF fresco');
        }

        const refreshData = await refreshResponse.json();
        let csrfToken = refreshData.csrf_token;

        // Actualizar el token en todas partes
        if (csrfToken) {
            actualizarCSRFToken(csrfToken);
        }

        // Hace el upload con el token fresco
        const formData = new FormData();
        formData.append('image', archivo);
        formData.append('_token', csrfToken);

        const endpoint = `/subir-imagen-publicacion/${publicacionId}`;

        const response = await fetch(endpoint, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: formData,
        });

        const responseText = await response.text();

        if (!response.ok) {
            console.error('Error Upload Response:', response.status, responseText);
            throw new Error(`Error ${response.status}: ${responseText.substring(0, 100)}`);
        }

        let jsonResponse;
        try {
            jsonResponse = JSON.parse(responseText);
        } catch (parseError) {
            console.error('JSON Parse Error:', parseError, 'Response:', responseText);
            throw new Error('Respuesta inválida del servidor');
        }

        // Si el servidor no devuelve imagenUrl, construirla
        if (!jsonResponse.imagenUrl) {
            jsonResponse.imagenUrl = '/imagen/' + jsonResponse.Direccion;
        }

        // Obtiene otro token fresco para el siguiente POST (guardarPublicacion)
        try {
            const finalRefreshResponse = await fetch('/refresh-csrf', {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (finalRefreshResponse.ok) {
                const finalRefreshData = await finalRefreshResponse.json();
                if (finalRefreshData.csrf_token) {
                    actualizarCSRFToken(finalRefreshData.csrf_token);
                }
            }
        } catch (refreshError) {
            console.warn('Warning refreshing final CSRF token:', refreshError);
        }

        return jsonResponse;
    } catch (error) {
        console.error('Upload Error:', error);
        throw error;
    }
}
