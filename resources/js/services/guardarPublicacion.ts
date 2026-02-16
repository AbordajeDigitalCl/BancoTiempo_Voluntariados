import { router } from '@inertiajs/react';

interface DatosPublicacion {
    titulo: string;
    descripcion: string;
    costo: number;
    ofertaDemanda: string;
}

export function guardarPublicacion(id: number, datos: DatosPublicacion) {
    router.post(`/Editar-Publicacion/${id}`, {
        titulo: datos.titulo,
        descripcion: datos.descripcion,
        costo: datos.costo,
        ofertaDemanda: datos.ofertaDemanda,
    });
}
