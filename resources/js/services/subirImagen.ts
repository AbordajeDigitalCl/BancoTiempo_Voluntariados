import { actualizarCSRFToken } from './actualizarCSRFToken';

// Obtener un CSRF token completamente FRESCO antes de cada upload.

export async function subirImagen(archivo: File, usuarioId?: number): Promise<any> {
    try {
        //Obtenie un token CSRF completamente fresco del servidor
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

        // Añadir el token CSRF fresco que acabamos de obtener
        formData.append('_token', csrfToken);

        const endpoint = usuarioId ? `/subir-imagen/${usuarioId}` : '/upload';

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

        // Obtenie otro token fresco para el siguiente POST (guardarPerfil)
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
