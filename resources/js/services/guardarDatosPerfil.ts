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

export function guardarDatosPerfil(datos: DatosPerfil, usuarioId: number | null = null, returnTo: string = 'verperfil') {
    const url = usuarioId ? `/EditarPerfil/${usuarioId}?return_to=${returnTo}` : '/EditarPerfil';
    
    router.post(url, {
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
    });
}
