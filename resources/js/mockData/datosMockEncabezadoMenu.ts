export interface UsuarioHeader {
    id: number;
    nombre: string;
    apellido: string;
    email: string;
    foto: string;
}

export interface OpcionMenu {
    id: number;
    label: string;
    href?: string;
    action?: string; 
    icono: 'mensajes' | 'notificaciones' | 'usuario' | 'logout';
}

export const mockUsuarioHeader: UsuarioHeader = {
    id: 1,
    nombre: "Juan",
    apellido: "Perez",
    email: "juan.perez@email.com",
    foto: "https://picsum.photos/40?random=1",
};

export const mockOpcionesMenu: OpcionMenu[] = [
    {
        id: 1,
        label: "Mensajes",
        href: "/chat",
        icono: 'mensajes',
    },
    {
        id: 2,
        label: "Notificaciones",
        href: "/notificaciones",
        icono: 'notificaciones',
    },
    {
        id: 3,
        label: "Juan Perez",
        href: "/VerPerfil",
        icono: 'usuario',
    },
    {
        id: 4,
        label: "Cerrar Sesión",
        action: "logout",
        icono: 'logout',
    },
];
