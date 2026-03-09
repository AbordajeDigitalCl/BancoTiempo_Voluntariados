import { router } from '@inertiajs/react';

// CORREGIDO: Usar estructura correcta para el nuevo sistema
// Tipo_Publicacion: 0=Oferta, 1=Demanda
// Tipo_Beneficio: 0=Servicio, 1=Producto
interface DatosPublicacion {
    titulo: string;
    descripcion: string;
    costo: number;
    tipo: string; // 'Servicio' o 'Producto'
    ofertaDemanda: string; // 'Oferta' o 'Demanda'
}


export function guardarPublicacion(id: number, datos: DatosPublicacion, returnTo: string = '/verperfil') {
    
    const tipoBeneficio = datos.tipo === 'Servicio' ? 0 : 1;
    

    const tipoPublicacion = datos.ofertaDemanda === 'Oferta' ? 0 : 1;
    
    // Construir el body con los campos que el backend espera
    const body: any = {
        Nombre: datos.titulo,
        Descripcion: datos.descripcion,
        Tipo_Beneficio: tipoBeneficio,
        Tipo_Publicacion: tipoPublicacion,
        return_to: returnTo, // Incluir en el body en lugar de query param
    };
    
    // Agregar Precio o Presupuesto según sea Oferta o Demanda
    if (tipoPublicacion === 0) {
        // Es Oferta: enviar Precio
        body.Precio = datos.costo;
    } else {
        // Es Demanda: enviar Presupuesto
        body.Presupuesto = datos.costo;
    }
    
    router.post(`/Editar-Publicacion/${id}`, body);
}
