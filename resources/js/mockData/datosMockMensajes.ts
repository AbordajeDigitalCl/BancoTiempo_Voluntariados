import { mockUsuariosAdmin } from '@/mockData/datosMockUsuarios';

export interface Conversacion {
    id: number;
    usuarioId: number;
    nombre: string;
    foto: string;
    ultimoMensaje: string;
    tipoUltimoMensaje: number;
    esMensajeDelUsuarioPrincipal: boolean;
    fechaUltimoMensaje: string;
    mensajesNoLeidos: number;
}

export const mockConversaciones: Conversacion[] = [
    {
        id: 1,
        usuarioId: mockUsuariosAdmin[0].id,
        nombre: mockUsuariosAdmin[0].nombre,
        foto: mockUsuariosAdmin[0].foto,
        ultimoMensaje: 'Lamentablemente no pude probar el producto, porque no tengo tiempo suficiente...',
        tipoUltimoMensaje: 0,
        esMensajeDelUsuarioPrincipal: false,
        fechaUltimoMensaje: '03/02/2026',
        mensajesNoLeidos: 2,
    },
    {
        id: 2,
        usuarioId: mockUsuariosAdmin[1].id,
        nombre: mockUsuariosAdmin[1].nombre,
        foto: mockUsuariosAdmin[1].foto,
        ultimoMensaje: 'Me avisas si tienes dudas, o si no funciona algo.',
        tipoUltimoMensaje: 0,
        esMensajeDelUsuarioPrincipal: true,
        fechaUltimoMensaje: '12/01/2026',
        mensajesNoLeidos: 0,
    },
    {
        id: 3,
        usuarioId: mockUsuariosAdmin[2].id,
        nombre: mockUsuariosAdmin[2].nombre,
        foto: mockUsuariosAdmin[2].foto,
        ultimoUltimoMensaje: 'Gracias por el cambio de tarja',
        tipoMensaje: 0,
        esMensajeDelUsuarioPrincipal: false,
        fechaUltimoMensaje: '10/01/2026',
        mensajesNoLeidos: 0,
    },
];
