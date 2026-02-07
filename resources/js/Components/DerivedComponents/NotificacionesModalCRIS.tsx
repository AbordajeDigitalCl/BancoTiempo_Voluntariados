import { useState, useEffect } from 'react';
import FilaNotificacionCRIS from './FilaNotificacionCRIS';

interface Notificacion {
    id: number;
    nombre: string;
    foto: string;
    servicio: string;
    mensajesNoLeidos?: number;
}

interface NotificacionesModalCRISProps {
    isOpen: boolean;
    notificaciones: Notificacion[];
    onClose: () => void;
    onSeleccionarUsuario: (usuarioId: number) => void;
    onMarcadasComoLeidas?: (usuarioId: number) => void;
}

export default function NotificacionesModalCRIS({
    isOpen,
    notificaciones,
    onClose,
    onSeleccionarUsuario,
    onMarcadasComoLeidas,
}: NotificacionesModalCRISProps) {
    const [notificacionesLocales, setNotificacionesLocales] = useState(notificaciones);

    useEffect(() => {
        setNotificacionesLocales(notificaciones);
    }, [notificaciones]);

    const handleSeleccionarUsuario = (usuarioId: number) => {
        
        setNotificacionesLocales((prev) =>
            prev.map((notif) =>
                notif.id === usuarioId ? { ...notif, mensajesNoLeidos: 0 } : notif
            )
        );

  
        onSeleccionarUsuario(usuarioId);
        onMarcadasComoLeidas?.(usuarioId);
        onClose();
    };

    if (!isOpen) return null;

    return (
        <>
            
            <div
                className="fixed inset-0 z-40"
                onClick={onClose}
            />

            
            <div className="absolute top-12 right-0 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50 max-h-96 overflow-y-auto">
               
                <div className="p-4 border-b border-gray-200 sticky top-0 bg-white">
                    <h2 className="text-sm font-semibold text-gray-800">
                        Notificaciones
                    </h2>
                </div>

               
                {notificacionesLocales.length > 0 ? (
                    <div>
                        {notificacionesLocales.map((notif) => (
                            <FilaNotificacionCRIS
                                key={notif.id}
                                id={notif.id}
                                nombre={notif.nombre}
                                foto={notif.foto}
                                servicio={notif.servicio}
                                mensajesNoLeidos={notif.mensajesNoLeidos}
                                onSeleccionar={handleSeleccionarUsuario}
                            />
                        ))}
                    </div>
                ) : (
                    <div className="p-8 text-center text-gray-500 text-sm">
                        No tienes notificaciones
                    </div>
                )}
            </div>
        </>
    );
}
