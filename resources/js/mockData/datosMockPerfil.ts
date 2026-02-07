export interface Servicio {
    id: number;
    titulo: string;
    descripcion: string;
    costo: string; 
    tipo: 'Servicio' | 'Producto';
    ofertaDemanda: 'Oferta' | 'Demanda';
    imagen: string;
    estado: 'Activo' | 'Desactivado' | 'Agotado';
    fecha: string;
}

export interface PerfilData {
    id: number;
    nombre: string;
    titulo: string; 
    foto: string;
    ciudad: string;
    telefono: string;
    direccion: string;
    email: string;
    fechaNacimiento: string;
    saldoHoras: number;
    fechaExpiracion: string;
}


export const mockProfileData: PerfilData = {
    id: 1,
    nombre: "Juan Perez N.",
    titulo: "Godín que cambia cosas",
    foto: "https://picsum.photos/200?random=1",
    ciudad: "Santiago, Chile",
    telefono: "+56 9 1234 5678",
    direccion: "Avenida Principal 123, Santiago",
    email: "juan.perez@email.com",
    fechaNacimiento: "15/03/1985",
    saldoHoras: 38,
    fechaExpiracion: "30/04/2025",
};


export const mockServiciosData: Servicio[] = [
    {
        id: 1,
        titulo: "Servicio afinación y balanceo",
        descripcion: "Este servicio incluye una revisión detallada del motor y el sistema de suspensión. Asegura un mejor rendimiento del vehículo y extiende la vida útil de los componentes mecánicos. Ideal para mantenimiento preventivo trimestral.",
        costo: "5 horas",
        tipo: "Servicio",
        ofertaDemanda: "Oferta",
        imagen: "https://picsum.photos/200?random=2",
        estado: "Activo",
        fecha: "2025-01-20",
    },
    {
        id: 2,
        titulo: "Reparación de tuberías",
        descripcion: "Servicio completo de reparación y mantenimiento de sistemas de tuberías. Incluye diagnóstico, reparación y pruebas de funcionamiento. Disponible para emergencias.",
        costo: "3 horas",
        tipo: "Servicio",
        ofertaDemanda: "Oferta",
        imagen: "https://picsum.photos/200?random=3",
        estado: "Activo",
        fecha: "2025-01-15",
    },
    {
        id: 3,
        titulo: "Ayuda con cambio de tarja de cocina",
        descripcion: "Tengo un problema con la tarja de mi cocina y necesito ayuda para cambiarla.",
        costo: "2 horas",
        tipo: "Servicio",
        ofertaDemanda: "Demanda",
        imagen: "https://picsum.photos/200?random=4",
        estado: "Desactivado",
        fecha: "2025-01-18",
    },
    {
        id: 4,
        titulo: "BMX Bicicleta en buen estado",
        descripcion: "Bicicleta a todo terreno diseñada para un manejo y control bien equilibrado para el ciclista.",
        costo: "40 horas",
        tipo: "Producto",
        ofertaDemanda: "Oferta",
        imagen: "https://picsum.photos/200?random=5",
        estado: "Agotado",
        fecha: "2025-01-17",
    },
    {
        id: 5,
        titulo: "Objeto inanimado que hace ruido al apretar",
        descripcion: "Juguete con forma de pato con la boca grande que emite sonidos al apretar, fotos no disponibles hasta nuevo aviso.",
        costo: "1 hora",
        tipo: "Producto",
        ofertaDemanda: "Oferta",
        imagen: "https://picsum.photos/200?random=6",
        estado: "Activo",
        fecha: "2025-01-30",
    },
];
