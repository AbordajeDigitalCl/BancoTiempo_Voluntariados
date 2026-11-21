
//Servicio para manejar la visibilidad de las publicaciones

export async function cambiarVisibilidadPublicacion(
    publicacionId: number
): Promise<{ visible: boolean }> {

    try {
        // 1. Obtener un token CSRF fresco
        const refreshResponse = await fetch('/refresh-csrf', {
            method: 'GET',
            credentials: 'include',
        });
        const refreshData = await refreshResponse.json();
        const csrfToken = refreshData.csrf_token;

        // 2. Enviar la petición para cambiar la visibilidad
        const response = await fetch(
            `/publicaciones/${publicacionId}/visibilidad`,
            {
                method: 'PATCH',
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            }
        );

        if (!response.ok) {
            const errorText = await response.text();

            console.error(
                'Visibility error response:',
                response.status,
                errorText
            );

            throw new Error(
                `Error ${response.status}: ${errorText.substring(0, 100)}`
            );
        }

        // 3. Recuperar el nuevo estado de visibilidad
        const data = await response.json();

        return {
            visible: data.visible,
        };

    } catch (error) {
        console.error(
            'Error en cambiarVisibilidadPublicacion:',
            error
        );

        throw error;
    }
}