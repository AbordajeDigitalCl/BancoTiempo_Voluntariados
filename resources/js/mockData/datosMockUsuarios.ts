export interface Usuario {
    id: number;
    nombre: string;
    foto: string;
    ofertas: number;
    demandas: number;
    direccion: string;
}

export const mockUsuariosAdmin: Usuario[] = [
    {
        id: 1,
        nombre: "Carlos González",
        foto: "https://picsum.photos/50?random=1",
        ofertas: 12,
        demandas: 5,
        direccion: "Santiago, Providencia 456",
    },
    {
        id: 2,
        nombre: "María Rodríguez",
        foto: "https://picsum.photos/50?random=2",
        ofertas: 8,
        demandas: 3,
        direccion: "Talagante, Centro 123",
    },
    {
        id: 3,
        nombre: "Juan López",
        foto: "https://picsum.photos/50?random=3",
        ofertas: 15,
        demandas: 7,
        direccion: "Valparaíso, Cerro Castillo 789",
    },
    {
        id: 4,
        nombre: "Ana Martínez",
        foto: "https://picsum.photos/50?random=4",
        ofertas: 6,
        demandas: 11,
        direccion: "Viña del Mar, Av. Marina 321",
    },
    {
        id: 5,
        nombre: "Pedro Sánchez",
        foto: "https://picsum.photos/50?random=5",
        ofertas: 20,
        demandas: 4,
        direccion: "La Serena, Coquimbo 654",
    },
    {
        id: 6,
        nombre: "Laura García",
        foto: "https://picsum.photos/50?random=6",
        ofertas: 9,
        demandas: 9,
        direccion: "Concepción, Los Ángeles 987",
    },
    {
        id: 7,
        nombre: "Roberto Fernández",
        foto: "https://picsum.photos/50?random=7",
        ofertas: 11,
        demandas: 6,
        direccion: "Puerto Montt, Chinquihue 234",
    },
    {
        id: 8,
        nombre: "Sofía Díaz",
        foto: "https://picsum.photos/50?random=8",
        ofertas: 7,
        demandas: 8,
        direccion: "Temuco, Padre las Casas 567",
    },
];
