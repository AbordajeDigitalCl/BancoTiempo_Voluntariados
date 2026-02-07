import { mockConversaciones } from '@/mockData/datosMockMensajes';
import { mockServiciosData } from '@/mockData/datosMockPerfil';

export interface NotificacionMock {
    id: number;
    nombre: string;
    foto: string;
    servicio: string;
    mensajesNoLeidos: number;
}

export const mockNotificaciones: NotificacionMock[] = mockConversaciones.map((conv) => {
    const servicioAsignado = mockServiciosData[conv.id - 1] || mockServiciosData[0];
    
    return {
        id: conv.id,
        nombre: conv.nombre,
        foto: conv.foto,
        servicio: servicioAsignado.titulo,
        mensajesNoLeidos: conv.mensajesNoLeidos,
    };
});
