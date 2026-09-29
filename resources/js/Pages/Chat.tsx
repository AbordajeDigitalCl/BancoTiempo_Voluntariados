import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import FilaConversacion from '@/Components/DerivedComponents/FilaConversacion';
import AreaChat from '@/Components/DerivedComponents/AreaChat';
import { useState, useEffect, useRef } from 'react';
import { useInfiniteScroll } from '@/hooks/useInfiniteScroll';
import type { Conversacion } from '@/mockData/datosMockMensajes';
import type { ConversacionDetalle, Mensaje} from '@/mockData/datosMockChatMensajes';
import {router} from '@inertiajs/react';
import axios from 'axios';

const CONVERSACIONES_PER_PAGE = 10;


/**
 * Hacer merge de mensajes
 */

const mergeMensajesById = (a: Mensaje[], b: Mensaje[]) : Mensaje[] => {

    const map = new Map<number, Mensaje>();
    [...a, ...b].forEach((m) => {
        map.set(Number(m.id), m);
    });

    return Array.from(map.values()).sort((x, y) => {
        const ax = new Date(`${x.fecha.split('/').reverse().join('-')}T${x.hora}`).getTime();
        const by = new Date(`${y.fecha.split('/').reverse().join('-')}T${y.hora}`).getTime();
        return ax - by;
    });
}

const upsertMensajeEnConversacion = (
    prev: ConversacionDetalle[],
    convId: number,
     nuevoMensaje: Mensaje
) : ConversacionDetalle[] => {
        
    const idx = prev.findIndex((c) => c.conversacionId === convId);

    if(idx === -1) {
        return [...prev, {conversacionId: convId, mensajes: [nuevoMensaje]}];
    }

    const actual = prev[idx];
    const mensajesMerged = mergeMensajesById(actual.mensajes, [nuevoMensaje]);

    const copia = [...prev];
    copia[idx] = {...actual, mensajes: mensajesMerged};

    console.log("ESTOS SON LOS MENSAJES QUE ENVIA EL UPSERT");
    console.log(copia);
    return copia;
};


type ChatMessageEvent = {
    message_id: number;
    conversation_id: number;
    sender_id: number;
    receiver_id: number;
    preview: string;
    is_image:boolean;
    created_at: string;
};

const formatearFecha = (iso: string) => {
    const d = new Date(iso);
    const dd = String(d.getDate()).padStart(2, '0');
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const yyyy = d.getFullYear();

    const hh = String(d.getHours()).padStart(2, '0');
    const min = String(d.getMinutes()).padStart(2, '0');

    return {
        fecha: `${dd}/${mm}/${yyyy}`,
        hora: `${hh}:${min}`
    };
        
};

interface ChatProps {
    auth: any;
    conversaciones: Conversacion[];
    mensajesPorConversacion: ConversacionDetalle[];
}

export default function Chat({ auth, conversaciones: conversacionesServer, mensajesPorConversacion }: ChatProps) {
    const [conversacionSeleccionada, setConversacionSeleccionada] = useState<number | null>(null);
    const [conversacionesConEstado, setConversacionesConEstado] = useState(conversacionesServer || []);
    const [conversacionesPaginadas, setConversacionesPaginadas] = useState((conversacionesServer || []).slice(0, CONVERSACIONES_PER_PAGE));

    //estado local para mensajes en tiempo real
    const [mensajesPorConversacionState, setMensajesPorConversacionState] = useState<ConversacionDetalle[]>(mensajesPorConversacion || []);

    const conversacionActual = conversacionesConEstado.find((c) => c.id === conversacionSeleccionada);

    //Se modificó para que lea el estado mensajesPorConversacionState en lugar de mensajesPorConversacion
    const mensajesActuales = mensajesPorConversacionState.find(
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

    const marcarConversacionComoLeida = async (conversacionId: number) => {
        try {
            const response = await axios.post('/chat/marcar-leidos', {
                conversacion_id: conversacionId,
            });

            const unreadTotal = Number(response?.data?.unread_total ?? 0);

            window.dispatchEvent(
                new CustomEvent('chat:unread-sync', {
                    detail: {total: unreadTotal},
                })
            );
            /*
            router.reload({
                only: ['unreadMessageCount', 'nav'],
                preserveScroll:true,
                preserveState: true,
            });
            */
        } catch (error) {
            console.error('Error al marcar mensajes como leidos', error);
        }
    };

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

        //Persistencia en BD (read_at = now)
        void marcarConversacionComoLeida(conversacionId);
    };




    /**
     * Actualizar conversación cuando se envía un mensaje o imagen
     * Actualiza el último mensaje y fecha en la lista de conversaciones
     */
    const handleMensajeEnviado = (contenido: string, fecha: string, hora: string) => {
        if (!conversacionSeleccionada) return;

        setConversacionesConEstado((prevState) =>
            prevState.map((conv) =>
                conv.id === conversacionSeleccionada
                    ? {
                        ...conv,
                        ultimoMensaje: contenido.length > 35 ? `${contenido.substring(0, 35)}...` : contenido,
                        fechaUltimoMensaje: fecha,
                        esMensajeDelUsuarioPrincipal: true,
                    }
                    : conv
            )
        );
        
        setMensajesPorConversacionState((prev) => {

            const msg: Mensaje = {
                id: Date.now(),
                contenido,
                esDelUsuarioPrincipal: true,
                fecha,
                hora,
            };

            console.log("ESTE ES EL ESTADO DEL PREV EN EL PADRE ANTES DE HACER UPSERT");
            console.log(prev);

            return upsertMensajeEnConversacion(prev, conversacionSeleccionada, msg);
        });
    };

    const mostrarListaVacia = conversacionesPaginadas.length === 0;


    const hydratedRef = useRef(false);
    //Para actualizar estado local de las conversaciones
    useEffect(()=> {
        if(hydratedRef.current) return;
        setMensajesPorConversacionState(mensajesPorConversacion || []);
        hydratedRef.current = true;
    }, [mensajesPorConversacion]);




    useEffect(() => {
        if(!auth?.user?.id || !window.Echo) return;

        const channelName = `App.Models.User.${auth.user.id}`;
        console.log(`CHANNEL NAME: App.Models.User.${auth.user.id}`);
        window.Echo.private(channelName).listen('.chat.message.created', (e: ChatMessageEvent) => {

            console.log(`ESTE ES EL CANAL DE ${e.conversation_id}`);
            console.log(`EL QUE ENVÍA ES: ${e.sender_id} y ${e.sender_id === auth.user.id ? "si" : "no"} es el usuario autenticado`);

            const convId = Number(e.conversation_id);
            const isOwnMessage = Number(e.sender_id) === Number(auth.user.id);
            const isActive = convId === conversacionSeleccionada;

            setConversacionesConEstado((prev) =>
                prev.map((conv) => {
                    if(conv.id !== convId) return conv;

                    const fechaHoraUltimoMensaje = `${formatearFecha(e.created_at).fecha} ${formatearFecha(e.created_at).hora}`; 

                    return {
                        ...conv,
                        ultimoMensaje: String(e.preview ?? ''),
                        fechaUltimoMensaje: fechaHoraUltimoMensaje,
                        esMensajeDelUsuarioPrincipal:false,
                        mensajesNoLeidos: isActive ? 0 : (conv.mensajesNoLeidos ?? 0) + 1,
                    };
                })
            );

            setMensajesPorConversacionState((prev) => {

                const {fecha, hora} = formatearFecha(e.created_at);
                const nuevoMensaje: Mensaje = {
                    id: Number(e.message_id),
                    contenido: e.is_image ? '[Imagen compartida]' : String(e.preview ?? ''),
                    esDelUsuarioPrincipal: isOwnMessage,
                    fecha,
                    hora,
                    esImagen: Boolean(e.is_image),
                };


                return upsertMensajeEnConversacion(prev, convId, nuevoMensaje);
                
            });

            if(convId === conversacionSeleccionada) {
                void marcarConversacionComoLeida(convId);
            }
        });

        return () => {
            window.Echo.leave(channelName);
        };
    }, [auth?.user?.id, conversacionSeleccionada]);

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

            <div className="w-full flex justify-center lg:px-0 md:px-4 px-4 py-6">
                <div className="w-full lg:max-w-full md:max-w-4xl max-w-2xl h-[calc(100vh-200px)] flex gap-4">
                    <div className={`${conversacionSeleccionada ? 'hidden md:flex' : 'flex'} md:w-1/3 lg:w-1/4 w-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col`}>
                        <div className="p-4 md:p-6 border-b border-gray-200 flex items-center justify-between relative">
                            <h1 className="text-lg md:text-xl font-semibold text-gray-800">
                                Mensajes
                            </h1>
                        </div>

                        <div className="flex-1 overflow-y-auto scrollbar-thin flex flex-col">
                            {mostrarListaVacia ? (
                                <div className="flex items-center justify-center h-full">
                                    <p className="text-gray-400 text-center px-4">No hay conversaciones</p>
                                </div>
                            ) : (
                                <>
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
                                </>
                            )}
                        </div>
                    </div>

                    <div className={`${!conversacionSeleccionada ? 'hidden md:flex' : 'flex'} md:w-2/3 lg:w-3/4 w-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col`}>
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
                        {conversacionActual ? (
                            <AreaChat
                                conversacion={conversacionActual}
                                mensajes={mensajesActuales}
                                userId={auth.user?.id}
                                onMensajeEnviado={handleMensajeEnviado}
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
