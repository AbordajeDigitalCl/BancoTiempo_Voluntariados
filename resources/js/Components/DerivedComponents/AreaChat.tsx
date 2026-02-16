import { Conversacion } from '@/mockData/datosMockMensajes';
import { Mensaje } from '@/mockData/datosMockChatMensajes';
import GloboMensaje from '@/Components/DerivedComponents/GloboMensaje';
import { enviarMensaje } from '@/services/enviarMensaje';
import { useState, useRef, useEffect } from 'react';
import React from 'react';

interface AreaChatProps {
    conversacion: Conversacion;
    mensajes: Mensaje[];
}

export default function AreaChat({ conversacion, mensajes: mensajesIniciales }: AreaChatProps) {
    const [nuevoMensaje, setNuevoMensaje] = useState('');
    const [descripcionEditando, setDescripcionEditando] = useState(false);
    const [descripcion, setDescripcion] = useState(
        'Usuario verificado con excelentes referencias'
    );
    const [mensajes, setMensajes] = useState<Mensaje[]>(mensajesIniciales);
    const [imagenURL, setImagenURL] = useState('');
    const [enviando, setEnviando] = useState(false);
    const mensajesContainerRef = useRef<HTMLDivElement>(null);

    React.useEffect(() => {
        setMensajes(mensajesIniciales);
    }, [conversacion.id, mensajesIniciales]);

    useEffect(() => {
        if (mensajesContainerRef.current) {
            const scrollContainer = mensajesContainerRef.current;
            scrollContainer.scrollTop = scrollContainer.scrollHeight;
        }
    }, [mensajes]);

    const handleEnviarMensaje = () => {
        if (nuevoMensaje.trim() === '') return;

        const fechaHoy = new Date();
        const fecha = `${String(fechaHoy.getDate()).padStart(2, '0')}/${String(
            fechaHoy.getMonth() + 1
        ).padStart(2, '0')}/${fechaHoy.getFullYear()}`;
        const hora = `${String(fechaHoy.getHours()).padStart(2, '0')}:${String(
            fechaHoy.getMinutes()
        ).padStart(2, '0')}`;

        const nuevoMensajeObj: Mensaje = {
            id: mensajes.length + 1,
            contenido: nuevoMensaje,
            esDelUsuarioPrincipal: true,
            fecha,
            hora,
        };

        setMensajes([...mensajes, nuevoMensajeObj]);
        setNuevoMensaje('');

        enviarMensaje({
            conversacion_id: conversacion.id,
            contenido: nuevoMensajeObj.contenido,
            es_imagen: false,
        }).catch((error) => {
            console.error('Error al enviar mensaje:', error);
        });
    };

    const handleSeleccionarImagen = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = async (event) => {
            const imagenData = event.target?.result as string;
            const fechaHoy = new Date();
            const fecha = `${String(fechaHoy.getDate()).padStart(2, '0')}/${String(
                fechaHoy.getMonth() + 1
            ).padStart(2, '0')}/${fechaHoy.getFullYear()}`;
            const hora = `${String(fechaHoy.getHours()).padStart(2, '0')}:${String(
                fechaHoy.getMinutes()
            ).padStart(2, '0')}`;

            const nuevoMensajeConImagen: Mensaje = {
                id: mensajes.length + 1,
                contenido: '[Imagen compartida]',
                esDelUsuarioPrincipal: true,
                fecha,
                hora,
                esImagen: true,
                imagenUrl: imagenData,
            };

            setMensajes([...mensajes, nuevoMensajeConImagen]);
            setEnviando(true);

            try {
                await enviarMensaje({
                    conversacion_id: conversacion.id,
                    contenido: file.name,
                    es_imagen: true,
                    nombre_archivo: file.name,
                });
            } catch (error) {
                console.error('Error al enviar imagen:', error);
            } finally {
                setEnviando(false);
            }
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
            >
                {mensajes.map((mensaje) => (
                    <GloboMensaje key={mensaje.id} mensaje={mensaje} />
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
                        type="file"
                        id="inputImagen"
                        accept="image/*"
                        onChange={handleSeleccionarImagen}
                        className="hidden"
                    />

                  
                    <button
                        onClick={() => {}}
                        disabled={true}
                        className="flex flex-col items-center gap-1 text-gray-400 cursor-not-allowed opacity-50"
                        title="Próximamente"
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
                        <span className="text-xs">Próximamente</span>
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
       
