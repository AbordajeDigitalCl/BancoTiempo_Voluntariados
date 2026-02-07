import { Link } from '@inertiajs/react';

interface FilaNotificacionCRISProps {
    id: number;
    nombre: string;
    foto: string;
    servicio: string;
    mensajesNoLeidos?: number;
    onSeleccionar?: (usuarioId: number) => void;
}

export default function FilaNotificacionCRIS({
    id,
    nombre,
    foto,
    servicio,
    mensajesNoLeidos = 0,
    onSeleccionar,
}: FilaNotificacionCRISProps) {
    const handleClick = () => {
        onSeleccionar?.(id);
    };

    return (
        <div
            onClick={handleClick}
            className="p-4 border-b border-gray-200 hover:bg-gray-50 cursor-pointer transition flex items-start gap-3"
        >
            
            <div className="relative flex-shrink-0">
                <img
                    src={foto}
                    alt={nombre}
                    className="w-12 h-12 rounded-full object-cover"
                />
                {mensajesNoLeidos > 0 && (
                    <div className="absolute bottom-0 right-0 w-5 h-5 rounded-full bg-red-600 flex items-center justify-center border-2 border-white">
                        <span className="text-white text-xs font-bold">
                            {mensajesNoLeidos > 9 ? '9+' : mensajesNoLeidos}
                        </span>
                    </div>
                )}
            </div>

           
            <div className="flex-1 min-w-0">
                <p className="text-sm font-semibold text-gray-800">
                    <span className="font-bold">{nombre}</span> Adquirió{' '}
                    <Link 
                        href="/MyAdvertisements"
                        className="text-blue-600 underline hover:text-blue-800 cursor-pointer"
                        title="Ir a Mis publicaciones"
                    >
                        <span className="font-semibold text-blue-600 underline hover:text-blue-800">
                            {servicio}
                        </span>
                    </Link>
                </p>
                <p className="text-xs text-gray-500 mt-1">
                    Envíale un mensaje para ponerse de acuerdo.
                </p>
            </div>
        </div>
    );
}
