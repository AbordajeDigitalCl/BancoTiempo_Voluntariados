// Servicio para actualizar el CSRF token en Inertia después de operaciones que lo regeneran (como upload de imagen).
// Inertia cachea el CSRF token, por lo que necesita ser actualizado manualmente después de que el servidor lo renueva.

export function actualizarCSRFToken(nuevoToken: string) {
    // Actualizar en el meta tag
    const csrfMetaTag = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement;
    if (csrfMetaTag) {
        csrfMetaTag.content = nuevoToken;
    }
    
    // Forzar actualización de Inertia si está disponible
    // Inertia obtiene el CSRF token de window._token o del meta tag
    if (typeof window !== 'undefined') {
        (window as any)._token = nuevoToken;
    }
}
