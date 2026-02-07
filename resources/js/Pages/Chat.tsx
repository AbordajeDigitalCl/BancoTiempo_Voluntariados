import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import FilaConversacion from '@/Components/DerivedComponents/FilaConversacion';
import AreaChat from '@/Components/DerivedComponents/AreaChat';
import BellNotificationButton from '@/Components/DerivedComponents/BellNotificationButton';
import NotificacionesModalCRIS from '@/Components/DerivedComponents/NotificacionesModalCRIS';
import { mockConversaciones } from '@/mockData/datosMockMensajes';
import { mockMensajesConversaciones } from '@/mockData/datosMockChatMensajes';
import { mockNotificaciones } from '@/mockData/datosMockNotificacionesCRIS';
import { useState, useMemo, useEffect } from 'react';
import { useInfiniteScroll } from '@/hooks/useInfiniteScroll';

const CONVERSACIONES_PER_PAGE = 10;

export default function Chat({ auth }: any) {
    const [conversacionSeleccionada, setConversacionSeleccionada] = useState<number | null>(null);
    const [conversacionesConEstado, setConversacionesConEstado] = useState(mockConversaciones);
    const [conversacionesPaginadas, setConversacionesPaginadas] = useState(mockConversaciones.slice(0, CONVERSACIONES_PER_PAGE));
    const [modalNotificacionesAbierto, setModalNotificacionesAbierto] = useState(false);
    const [notificacionesConEstado, setNotificacionesConEstado] = useState(mockNotificaciones);


    const tieneNotificacionesSinLeer = useMemo(
        () => notificacionesConEstado.some((notif) => notif.mensajesNoLeidos > 0),
        [notificacionesConEstado]
    );

    const conversacionActual = mockConversaciones.find((c) => c.id === conversacionSeleccionada);
    const mensajesActuales = mockMensajesConversaciones.find(
        (m) => m.conversacionId === conversacionSeleccionada
    )?.mensajes || [];

    const { isLoading, hasMore, observerTarget, resetPagination } = useInfiniteScroll({
        itemsPerPage: CONVERSACIONES_PER_PAGE,
        totalItems: conversacionesConEstado.length,
        onLoadMore: (page) => {
            const start = page * CONVERSACIONES_PER_PAGE;
            const end = start + CONVERSACIONES_PER_PAGE;
            setConversacionesPaginadas(prev => [
                ...prev,
                ...conversacionesConEstado.slice(start, end)
            ]);
        },
    });

    useEffect(() => {
        const conversacionesActualizadas = conversacionesConEstado.map(conv => {
            const paginada = conversacionesPaginadas.find(p => p.id === conv.id);
            return paginada ? { ...paginada, ...conv } : conv;
        });
        setConversacionesPaginadas(conversacionesActualizadas.slice(0, conversacionesPaginadas.length));
    }, [conversacionesConEstado]);

    const handleSeleccionarConversacion = (conversacionId: number) => {
        setConversacionSeleccionada(conversacionId);
        setConversacionesConEstado((prevState) =>
            prevState.map((conv) =>
                conv.id === conversacionId
                    ? { ...conv, mensajesNoLeidos: 0 }
                    : conv
            )
        );
        setNotificacionesConEstado((prevState) =>
            prevState.map((notif) =>
                notif.id === conversacionId
                    ? { ...notif, mensajesNoLeidos: 0 }
                    : notif
            )
        );
    };

    const handleSeleccionarUsuarioDesdeNotificaciones = (usuarioId: number) => {
        const conversacion = conversacionesConEstado.find((c) => c.id === usuarioId);
        if (conversacion) {
            handleSeleccionarConversacion(usuarioId);
        }
    };

    const handleMarcadasComoLeidas = (usuarioId: number) => {
        setNotificacionesConEstado((prevState) =>
            prevState.map((notif) =>
                notif.id === usuarioId
                    ? { ...notif, mensajesNoLeidos: 0 }
                    : notif
            )
        );
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Mensajes" />

            <style>{`
                .scrollbar-thin::-webkit-scrollbar {
                    width: 8px;
                }
                .scrollbar-thin::-webkit-scrollbar-track {
                    background: transparent;
                }
                .scrollbar-thin::-webkit-scrollbar-thumb {
                    background: #cbd5e1;
                    border-radius: 4px;
                }
                .scrollbar-thin::-webkit-scrollbar-thumb:hover {
                    background: #94a3b8;
                }
                .scrollbar-thin {
                    scrollbar-width: thin;
                    scrollbar-color: #cbd5e1 transparent;
                }
            `}</style>

            <div className="w-full flex justify-center px-4 py-6">
                <div className="w-full max-w-6xl h-[calc(100vh-200px)] flex gap-4">
                    <div className={`${conversacionSeleccionada ? 'hidden md:flex' : 'flex'} md:w-1/3 w-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col`}>
                        <div className="p-4 md:p-6 border-b border-gray-200 flex items-center justify-between relative">
                            <h1 className="text-lg md:text-xl font-semibold text-gray-800">
                                Mensajes
                            </h1>
                            <BellNotificationButton 
                                hasNotifications={tieneNotificacionesSinLeer}
                                onClick={() => setModalNotificacionesAbierto(!modalNotificacionesAbierto)}
                            />
                    
                            <NotificacionesModalCRIS
                                isOpen={modalNotificacionesAbierto}
                                notificaciones={notificacionesConEstado}
                                onClose={() => setModalNotificacionesAbierto(false)}
                                onSeleccionarUsuario={handleSeleccionarUsuarioDesdeNotificaciones}
                                onMarcadasComoLeidas={handleMarcadasComoLeidas}
                            />
                        </div>

                        <div className="flex-1 overflow-y-auto scrollbar-thin flex flex-col">
                            <div className="flex-1">
                                {conversacionesPaginadas.map((conversacion) => (
                                    <FilaConversacion
                                        key={conversacion.id}
                                        conversacion={conversacion}
                                        seleccionada={conversacionSeleccionada === conversacion.id}
                                        onSeleccionar={handleSeleccionarConversacion}
                                    />
                                ))}
                            </div>


                            <div ref={observerTarget} className="flex justify-center py-4">
                                {isLoading && (
                                    <div className="text-center">
                                        <div className="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                                    </div>
                                )}
                                {!hasMore && conversacionesPaginadas.length > 0 && (
                                    <p className="text-gray-400 text-xs">No hay más conversaciones</p>
                                )}
                            </div>
                        </div>
                    </div>

                    <div className={`${!conversacionSeleccionada ? 'hidden md:flex' : 'flex'} md:w-2/3 w-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col`}>
                        {conversacionSeleccionada && (
                            <div className="md:hidden p-4 border-b border-gray-200 flex items-center gap-3">
                                <button
                                    onClick={() => setConversacionSeleccionada(null)}
                                    className="flex items-center gap-2 text-blue-600 hover:text-blue-800 transition font-medium"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        className="w-5 h-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M15 19l-7-7 7-7"
                                        />
                                    </svg>
                                    Atrás
                                </button>
                            </div>
                        )}
                        {conversacionActual && mensajesActuales.length > 0 ? (
                            <AreaChat
                                conversacion={conversacionActual}
                                mensajes={mensajesActuales}
                            />
                        ) : conversacionSeleccionada ? (
                            <div className="flex items-center justify-center h-full text-gray-400">
                                <p>Cargando chat...</p>
                            </div>
                        ) : (
                            <div className="hidden md:flex items-center justify-center h-full text-gray-400">
                                <p>Selecciona una conversación para empezar</p>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
