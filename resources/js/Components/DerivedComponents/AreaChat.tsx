import { Conversacion } from '@/mockData/datosMockMensajes';
import { Mensaje } from '@/mockData/datosMockChatMensajes';
import GloboMensaje from '@/Components/DerivedComponents/GloboMensaje';
import { enviarMensaje } from '@/services/enviarMensaje';
import { enviarMensajeConImagen } from '@/services/enviarMensajeConImagen';
import {
    guardarMensajeConImagenTemporal,
    obtenerMensajesTemporales,
    eliminarMensajemporal,
    limpiarMensajesTemporales,
} from '@/services/chatImagenesService';
import { useState, useRef, useEffect } from 'react';
import React from 'react';

/**
 * Convierte fecha en formato dd/mm/yyyy HH:mm:ss (o HH:mm) a timestamp para comparación
 * Maneja ambos formatos: con y sin segundos
 */
const convertirFechaATimestamp = (fecha: string, hora: string): number => {
    const [dia, mes, año] = fecha.split('/').map(Number);
    const partes = hora.split(':');
    const horas = Number(partes[0]);
    const minutos = Number(partes[1]);
    const segundos = partes.length > 2 ? Number(partes[2]) : 0; // Si no hay segundos, usar 0
    return new Date(año, mes - 1, dia, horas, minutos, segundos).getTime();
};

/**
 * Ordena mensajes por fecha (más antiguos primero, más recientes últimos)
 */
const ordenarMensajesPorFecha = (mensajes: Mensaje[]): Mensaje[] => {
    return [...mensajes].sort((a, b) => {
        const timestampA = convertirFechaATimestamp(a.fecha, a.hora);
        const timestampB = convertirFechaATimestamp(b.fecha, b.hora);
        return timestampA - timestampB;
    });
};

const mergeMensajesById = (a: Mensaje[], b: Mensaje[]) : Mensaje[] => {

    const map = new Map<number, Mensaje>();
    [...a, ...b].forEach((m) => {
        map.set(Number(m.id), m);
    });

    return(ordenarMensajesPorFecha(Array.from(map.values())));
}

const upsertMnesajeEnConversacion = (
    prev: ConversacionDetalle[],
    convId: number,
    nuevoMensaje: Mensaje
): ConversacionDetalle[] => {
    const idx = prev.findIndex((c) => c.conversacionId === convId);

    if(idx === -1) {
        return [...prev, {conversacionId: convId, mensajes: [nuevoMensaje]}];
    }

    const actual = prev[idx];
    const mensajesMerged = mergeMensajesById(actual.mensajes, [nuevoMensaje]);

    const copia = [...prev];
    copia[idx] = {...actual, mensajes: mensajesMerged};
    return copia;
}

interface AreaChatProps {
    conversacion: Conversacion;
    mensajes: Mensaje[];
    userId?: number;
    onMensajeEnviado?: (contenido: string, fecha: string, hora: string) => void;
}

export default function AreaChat({ conversacion, mensajes: mensajesIniciales, userId, onMensajeEnviado }: AreaChatProps) {
    const [nuevoMensaje, setNuevoMensaje] = useState('');
    const [descripcionEditando, setDescripcionEditando] = useState(false);
    const [descripcion, setDescripcion] = useState(
        'Usuario verificado con excelentes referencias'
    );
    //const [mensajes, setMensajes] = useState<Mensaje[]>(mensajesIniciales);
    const [imagenURL, setImagenURL] = useState('');
    const [enviando, setEnviando] = useState(false);
    const [procesandoImagen, setProcesandoImagen] = useState(false);
    const mensajesContainerRef = useRef<HTMLDivElement>(null);
    const inputImagenRef = useRef<HTMLInputElement>(null);
/*
    React.useEffect(() => {
        // Al cambiar conversación, cargar mensajes iniciales
        let todosLosMensajes = [...mensajesIniciales];
        
        // Si el servidor tiene mensajes (mensajesIniciales tiene contenido),
        // limpiar todos los mensajes temporales ya que fueron sincronizados
        if (mensajesIniciales.length > 0) {
            limpiarMensajesTemporales(conversacion.id);
        } else {
            // Si no hay mensajes del servidor, cargar mensajes temporales pendientes
            const mensajesTemporales = obtenerMensajesTemporales(conversacion.id, userId);
            if (mensajesTemporales.length > 0) {
                todosLosMensajes = [...todosLosMensajes, ...mensajesTemporales];
            }
        }
        
        // Ordenar todos los mensajes por fecha (más antiguos arriba, más recientes abajo)
        //const mensajesOrdenados = ordenarMensajesPorFecha(todosLosMensajes);
        //setMensajes(mensajesOrdenados);
        setMensajes((prev) => mergeMensajesById(prev, todosLosMensajes));
    }, [conversacion.id, mensajesIniciales, userId]);
*/

    //SE MODIFICA ESTO POR EL MOEMENTO PARA QUE USE MENSAJESINICIALES EN LUGAR DE MENSAJES
    useEffect(() => {
        if (mensajesContainerRef.current) {
            const scrollContainer = mensajesContainerRef.current;
            scrollContainer.scrollTop = scrollContainer.scrollHeight;
        }
    }, [mensajesIniciales]);

    const handleEnviarMensaje = () => {
        if (nuevoMensaje.trim() === '') return;

        const fechaHoy = new Date();
        const fecha = `${String(fechaHoy.getUTCDate()).padStart(2, '0')}/${String(
            fechaHoy.getUTCMonth() + 1
        ).padStart(2, '0')}/${fechaHoy.getUTCFullYear()}`;
        const hora = `${String(fechaHoy.getUTCHours()).padStart(2, '0')}:${String(
            fechaHoy.getUTCMinutes()
        ).padStart(2, '0')}:${String(fechaHoy.getUTCSeconds()).padStart(2, '0')}`;

        // Crear ID temporal (negativo)
        const tempId = -(Math.random() * 10000);

        const nuevoMensajeObj: Mensaje = {
            id: tempId,
            contenido: nuevoMensaje,
            esDelUsuarioPrincipal: true,
            fecha,
            hora,
        };

        // Guardar en estado local inmediatamente (optimistic update)
        //const mensajesActualizados = ordenarMensajesPorFecha([...mensajes, nuevoMensajeObj]);
        //setMensajes(mensajesActualizados);

        const contenidoOriginal = nuevoMensaje;
        setNuevoMensaje('');
        setEnviando(true);

        // Guardar en localStorage como temporal
        guardarMensajeConImagenTemporal(conversacion.id, nuevoMensajeObj, userId);

        // Notificar al padre (para actualizar lista de conversaciones)
        onMensajeEnviado?.(contenidoOriginal, fecha, hora);

        // Enviar al servidor
        enviarMensaje({
            conversacion_id: conversacion.id,
            contenido: contenidoOriginal,
            es_imagen: false,
        })
            .then((response) => {
                // Reemplazar el mensaje temporal con el mensaje real del servidor
                /*
                const mensajeReal = response.mensaje;
                setMensajes((prevMensajes) => {
                    const updated = prevMensajes.map((msg) =>
                        msg.id === tempId ? { ...mensajeReal } : msg
                    );
                    return ordenarMensajesPorFecha(updated);
                });
                // Eliminar el mensaje temporal de localStorage ahora que está sincronizado
                eliminarMensajemporal(conversacion.id, Math.abs(tempId));
                */
                setEnviando(false);
            })
            .catch((error) => {
                console.error('Error al enviar mensaje:', error);
                // Remover el mensaje temporal si hay error
                setMensajes((prevMensajes) => prevMensajes.filter((msg) => msg.id !== tempId));
                eliminarMensajemporal(conversacion.id, Math.abs(tempId));
                setEnviando(false);
            });
    };

    const handleSeleccionarImagen = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (!file || procesandoImagen) return;

        setProcesandoImagen(true);

        const reader = new FileReader();
        reader.onload = (event) => {
            const imagenData = event.target?.result as string;
            const fechaHoy = new Date();
            const fecha = `${String(fechaHoy.getDate()).padStart(2, '0')}/${String(
                fechaHoy.getMonth() + 1
            ).padStart(2, '0')}/${fechaHoy.getFullYear()}`;
            const hora = `${String(fechaHoy.getHours()).padStart(2, '0')}:${String(
                fechaHoy.getMinutes()
            ).padStart(2, '0')}:${String(fechaHoy.getSeconds()).padStart(2, '0')}`;

            // ID temporal para la imagen
            const tempId = -(Math.random() * 10000);

            const nuevoMensajeConImagen: Mensaje = {
                id: tempId, // ID temporal
                contenido: '[Imagen compartida]',
                esDelUsuarioPrincipal: true,
                fecha,
                hora,
                esImagen: true,
                imagenUrl: imagenData, // Base64 local para mostrar inmediatamente
            };

            // Guardar en localStorage como temporal
            guardarMensajeConImagenTemporal(conversacion.id, nuevoMensajeConImagen, userId);

            // Agregar mensaje al estado local y ordenar por fecha (optimistic update)
            const mensajesActualizados = ordenarMensajesPorFecha([...mensajes, nuevoMensajeConImagen]);
            setMensajes(mensajesActualizados);

            // Notificar al padre que se envió una imagen
            onMensajeEnviado?.('[Imagen compartida]', fecha, hora);

            // Enviar imagen al servidor en background
            setEnviando(true);
            enviarMensajeConImagen(conversacion.id, imagenData)
                .then((response) => {
                    // Reemplazar el mensaje temporal con el mensaje real del servidor
                    const mensajeReal = response.mensaje;
                    setMensajes((prevMensajes) => {
                        const updated = prevMensajes.map((msg) =>
                            msg.id === tempId ? { ...mensajeReal } : msg
                        );
                        return ordenarMensajesPorFecha(updated);
                    });
                    // Eliminar el mensaje temporal de localStorage ahora que está sincronizado
                    eliminarMensajemporal(conversacion.id, Math.abs(tempId));
                    setEnviando(false);
                })
                .catch((error) => {
                    console.error('Error al enviar imagen:', error);
                    // Remover el mensaje temporal si hay error
                    setMensajes((prevMensajes) => prevMensajes.filter((msg) => msg.id !== tempId));
                    eliminarMensajemporal(conversacion.id, Math.abs(tempId));
                    setEnviando(false);
                })
                .finally(() => {
                    setProcesandoImagen(false);
                    // Resetear el input file para permitir seleccionar la misma imagen nuevamente
                    if (inputImagenRef.current) {
                        inputImagenRef.current.value = '';
                    }
                });
        };
        reader.readAsDataURL(file);
    };

    return (
        <div className="flex flex-col h-full">
            
            <div className="flex items-center gap-4 px-4 py-6 border-b border-gray-200">
                
                <img
                    src={conversacion.foto}
                    alt={conversacion.nombre}
                    className="w-16 h-16 md:w-20 md:h-20 rounded-lg flex-shrink-0 object-cover"
                />

                
                <div className="flex-1 min-w-0">
                    
                    <h2 className="text-lg md:text-xl font-bold text-gray-800 mb-1">
                        {conversacion.nombre}
                    </h2>

                    
                    {descripcionEditando ? (
                        <textarea
                            value={descripcion}
                            onChange={(e) => setDescripcion(e.target.value)}
                            onBlur={() => setDescripcionEditando(false)}
                            className="w-full p-2 text-sm border border-blue-400 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                            rows={2}
                            autoFocus
                        />
                    ) : (
                        <p
                            onClick={() => setDescripcionEditando(true)}
                            className="text-sm text-gray-600 cursor-pointer hover:bg-gray-100 p-1 rounded transition"
                        >
                            {descripcion}
                        </p>
                    )}
                </div>
            </div>

            <div
                ref={mensajesContainerRef}
                className="flex-1 overflow-y-auto p-4 space-y-4 scrollbar-thin"
            >   {/*ESTO ESTOY MODIFICANDO PARA TESTEAR LOS MENSAJES EN TIEMPO REAL*/}
                {mensajesIniciales.map((mensaje, index) => (
                    <GloboMensaje key={`${conversacion.id}-${index}-${mensaje.id}`} mensaje={mensaje} />
                ))}
            </div>

            <div className="border-t border-gray-200 p-4 flex gap-2">
               
                <textarea
                    placeholder="Escribe un mensaje"
                    value={nuevoMensaje}
                    onChange={(e) => setNuevoMensaje(e.target.value)}
                    onKeyPress={(e) => {
                        if (e.key === 'Enter' && !e.shiftKey) {
                            e.preventDefault();
                            handleEnviarMensaje();
                        }
                    }}
                    rows={3}
                    className="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm resize-none overflow-y-auto scrollbar-thin"
                    style={{
                        lineHeight: '1.5rem',
                        wordWrap: 'break-word',
                        whiteSpace: 'pre-wrap',
                    }}
                />

                
                <div className="flex flex-col items-center gap-1">
                    
                    <input
                        ref={inputImagenRef}
                        type="file"
                        id="inputImagen"
                        accept="image/*"
                        onChange={handleSeleccionarImagen}
                        className="hidden"
                    />

                  
                    <button
                        onClick={() => document.getElementById('inputImagen')?.click()}
                        disabled={enviando}
                        className="flex flex-col items-center gap-1 text-blue-500 hover:text-blue-600 disabled:text-gray-400 disabled:cursor-not-allowed transition"
                        title="Compartir imagen"
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
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                            />
                        </svg>
                        <span className="text-xs">Imagen</span>
                    </button>

                  
                    <button
                        onClick={handleEnviarMensaje}
                        disabled={nuevoMensaje.trim() === ''}
                        className="mt-2 px-4 py-1 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Enviar
                    </button>
                </div>
            </div>
        </div>
    );
}
       
