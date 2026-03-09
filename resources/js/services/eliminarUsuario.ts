import { router } from '@inertiajs/react';

interface EliminarUsuarioData extends Record<string, any> {
    usuario_id: number;
}

export async function eliminarUsuario(datos: EliminarUsuarioData): Promise<void> {
    router.delete('/administrar-usuarios/eliminar-usuario', {
        data: datos,
    });
}
