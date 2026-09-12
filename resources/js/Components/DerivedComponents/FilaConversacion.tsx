import { Conversacion } from '@/mockData/datosMockMensajes';

interface FilaConversacionProps {
    conversacion: Conversacion;
    seleccionada?: boolean;
    onSeleccionar?: (conversacionId: number) => void;
}

export default function FilaConversacion({
    conversacion,
    seleccionada = false,
    onSeleccionar,
}: FilaConversacionProps) {
    
    const mensajeTruncado =
        conversacion.ultimoMensaje.length > 35
            ? `${conversacion.ultimoMensaje.substring(0, 35)}...`
            : conversacion.ultimoMensaje;

    return (
        <div
            onClick={() =>onSeleccionar?.(conversacion.id)}
            className={`flex items-start gap-3 p-3 border-b border-gray-200 cursor-pointer transition ${
                seleccionada
                    ? 'bg-blue-50 border-l-4 border-l-blue-500'
                    : 'hover:bg-gray-50'
            }`}
        >
            {/* Sin badge de mensajes no leídos - el círculo rojo no se muestra */}
            <img
                src={conversacion.foto}
                alt={conversacion.nombre}
                className="w-12 h-12 rounded-full flex-shrink-0 object-cover"
            />

            
            <div className="flex-1 min-w-0">
                
                <div className="flex justify-between items-start gap-2">
                    <h3 className="font-semibold text-gray-800 text-sm md:text-base truncate">
                        {conversacion.nombre}
                    </h3>
                    <span className="text-gray-500 text-xs flex-shrink-0">
                        {conversacion.fechaUltimoMensaje}
                    </span>
                    {conversacion.mensajesNoLeidos > 0 && (
                        <span className="inline-flex min-w-5 h-5 items-center justify-center rounded-full bg-red-600 px-1.5 text-[11px] font-semibold text-white">
                            {conversacion.mensajesNoLeidos}
                        </span>
                    )}
                </div>

               
                <p
                    className={`text-xs md:text-sm mt-1 truncate ${
                        conversacion.esMensajeDelUsuarioPrincipal
                            ? 'text-gray-600'
                            : 'text-gray-700 font-medium'
                    }`}
                >
                    {conversacion.esMensajeDelUsuarioPrincipal && 'Tú: '}
                    {mensajeTruncado}
                </p>
            </div>
        </div>
    );
}
