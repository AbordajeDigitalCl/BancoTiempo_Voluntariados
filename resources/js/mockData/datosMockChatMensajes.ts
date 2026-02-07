export interface Mensaje {
    id: number;
    contenido: string;
    esDelUsuarioPrincipal: boolean;
    fecha: string; 
    hora: string; 
    esImagen?: boolean;
    imagenUrl?: string;
}

export interface ConversacionDetalle {
    conversacionId: number;
    mensajes: Mensaje[];
}

export const mockMensajesConversaciones: ConversacionDetalle[] = [
    {
        conversacionId: 1,
        mensajes: [
            {
                id: 1,
                contenido: 'Hola, ¿cómo estás?',
                esDelUsuarioPrincipal: true,
                fecha: '01/02/2026',
                hora: '10:30',
            },
            {
                id: 2,
                contenido: 'Bien, eso si, te tengo que contar',
                esDelUsuarioPrincipal: false,
                fecha: '01/02/2026',
                hora: '10:35',
            },
            {
                id: 3,
                contenido: 'Lamentablemente no pude atender al servicio hoy, porque no tengo tiempo suficiente...',
                esDelUsuarioPrincipal: false,
                fecha: '03/02/2026',
                hora: '14:30',
            },
        ],
    },
    {
        conversacionId: 2,
        mensajes: [
            {
                id: 1,
                contenido: '¿Pudiste probar el servicio?',
                esDelUsuarioPrincipal: true,
                fecha: '10/01/2026',
                hora: '09:00',
            },
            {
                id: 2,
                contenido: 'Sí, lo estoy probando',
                esDelUsuarioPrincipal: false,
                fecha: '10/01/2026',
                hora: '09:15',
            },
            {
                id: 3,
                contenido: 'Me avisas si tienes dudas, o si no funciona algo.',
                esDelUsuarioPrincipal: true,
                fecha: '12/01/2026',
                hora: '15:20',
            },
        ],
    },
    {
        conversacionId: 3,
        mensajes: [
            {
                id: 1,
                contenido: 'Hola Juan, ¿Te funciona bien la tarja ahora?',
                esDelUsuarioPrincipal: true,
                fecha: '05/01/2026',
                hora: '11:00',
            },
            {
                id: 2,
                contenido: 'Sí, funciona impecable',
                esDelUsuarioPrincipal: false,
                fecha: '05/01/2026',
                hora: '11:30',
            },
            {
                id: 3,
                contenido: 'Gracias por el cambio de tarja',
                esDelUsuarioPrincipal: false,
                fecha: '10/01/2026',
                hora: '16:45',
            },
        ],
    },
];
