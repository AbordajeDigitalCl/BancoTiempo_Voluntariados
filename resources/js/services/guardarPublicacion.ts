import { router } from '@inertiajs/react';

// MODIFICADO: Agregado 'tipo' al interfaz (ahora Tipo_Publicacion es independiente)
interface DatosPublicacion {
    titulo: string;
    descripcion: string;
    costo: number;
    tipo: string; // NUEVO: 'Servicio' o 'Producto' (se convierte a 0 o 1)
    ofertaDemanda: string;
}

// MODIFICADO: Función ahora envía 'tipo' al servidor
export function guardarPublicacion(id: number, datos: DatosPublicacion) {
    router.post(`/Editar-Publicacion/${id}`, {
        titulo: datos.titulo,
        descripcion: datos.descripcion,
        costo: datos.costo,
        tipo: datos.tipo === 'Servicio' ? 0 : 1, // MODIFICADO: Convertir Servicio/Producto a 0/1
        ofertaDemanda: datos.ofertaDemanda, // MODIFICADO: Independiente del tipo
    });
}
