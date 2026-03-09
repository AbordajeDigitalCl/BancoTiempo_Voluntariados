import { router } from '@inertiajs/react';

interface DatosPerfil {
    nombre: string;
    apellidoPaterno: string;
    apellidoMaterno: string;
    email: string;
    contrasena: string;
    confirmarContrasena: string;
    fechaNacimiento: string;
    telefono: string;
    direccion: string;
    ciudad: string;
}

export function guardarDatosPerfil(datos: DatosPerfil, usuarioId: number | null = null, returnTo: string = 'verperfil', idImagen?: number) {
    const url = usuarioId ? `/EditarPerfil/${usuarioId}?return_to=${returnTo}` : `/EditarPerfil?return_to=${returnTo}`;
    
    // Preparar datos del perfil con id_imagen si fue cargada nueva imagen
    const datosEnvio: any = {
        nombre: datos.nombre,
        apellidoPaterno: datos.apellidoPaterno,
        apellidoMaterno: datos.apellidoMaterno,
        email: datos.email,
        contrasena: datos.contrasena,
        confirmarContrasena: datos.confirmarContrasena,
        fechaNacimiento: datos.fechaNacimiento,
        telefono: datos.telefono,
        direccion: datos.direccion,
        ciudad: datos.ciudad,
    };
    
    if (idImagen) {
        datosEnvio.id_imagen = idImagen;
    }

    // Usar router.post() de Inertia que maneja sesión, CSRF, y redirects automáticamente.
    // router.post() es más confiable que fetch() aquí porque Inertia maneja toda la lógica de headers y redirects.
    router.post(url, datosEnvio, {
        onError: (errors) => {
            console.error('Error al guardar perfil:', errors);
        }
    });
}
