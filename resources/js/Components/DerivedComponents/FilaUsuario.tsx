import { Link } from '@inertiajs/react';
import { useState } from 'react';
import { crearOBuscarConversacion } from '@/services/crearOBuscarConversacion';
import { eliminarUsuario } from '@/services/eliminarUsuario';

interface Usuario {
    id: number;
    nombre: string;
    apellidoPaterno: string;
    apellidoMaterno: string;
    ciudad: string;
    direccion: string;
    email: string;
    foto: string;
}

interface FilaUsuarioProps {
    usuario: Usuario;
    usuarioActualId?: number;
}

export default function FilaUsuario({ usuario, usuarioActualId }: FilaUsuarioProps) {
    const [mostrarConfirmacion, setMostrarConfirmacion] = useState(false);
    const [cargando, setCargando] = useState(false);

    const nombreCompleto = `${usuario.nombre} ${usuario.apellidoPaterno} ${usuario.apellidoMaterno}`.trim();

    const handleComentarios = async () => {
        setCargando(true);
        try {
            await crearOBuscarConversacion({ usuario_id: usuario.id });
        } catch (error) {
            setCargando(false);
            console.error('Error:', error);
        }
    };

    const handleEliminar = async () => {
        setCargando(true);
        try {
            await eliminarUsuario({ usuario_id: usuario.id });
            setMostrarConfirmacion(false);
        } catch (error) {
            setCargando(false);
            setMostrarConfirmacion(false);
            console.error('Error:', error);
        }
    };

    return (
        <>
            <div className="flex items-center justify-between p-4 md:p-6 border-b border-gray-200 hover:bg-gray-50 transition">
                
                <div className="flex items-center gap-4 flex-1 min-w-0">
                    
                    <img
                        src={usuario.foto}
                        alt={nombreCompleto}
                        className="w-12 h-12 md:w-16 md:h-16 rounded-lg flex-shrink-0 object-cover"
                    />

                    
                    <div className="flex-1 min-w-0">
                        <h3 className="font-semibold text-gray-800 text-sm md:text-base">
                            {nombreCompleto}
                        </h3>
                        
                        <p className="text-xs md:text-sm text-gray-500 mt-1">
                            {usuario.ciudad}
                            {usuario.direccion && ` • ${usuario.direccion}`}
                        </p>
                    </div>
                </div>

                
                <div className="flex gap-2 flex-shrink-0 ml-4">
                    
                    <Link
                        href={`/EditarPerfil/${usuario.id}?return_to=administrar`}
                        className="flex items-center justify-center w-10 h-10 md:w-12 md:h-12 rounded-lg hover:bg-blue-50 transition border border-blue-200"
                        title="Editar usuario"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            className="w-5 h-5 md:w-6 md:h-6 text-blue-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                            />
                        </svg>
                    </Link>

                    {usuarioActualId !== usuario.id && (
                        <button
                            onClick={handleComentarios}
                            disabled={cargando}
                            className="flex items-center justify-center w-10 h-10 md:w-12 md:h-12 rounded-lg hover:bg-green-50 transition border border-green-200 disabled:opacity-50"
                            title="Crear conversación"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                className="w-5 h-5 md:w-6 md:h-6 text-green-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                                />
                            </svg>
                        </button>
                    )}

                    
                    <button
                        onClick={() => setMostrarConfirmacion(true)}
                        disabled={cargando}
                        className="flex items-center justify-center w-10 h-10 md:w-12 md:h-12 rounded-lg hover:bg-red-50 transition border border-red-200 disabled:opacity-50"
                        title="Eliminar usuario"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            className="w-5 h-5 md:w-6 md:h-6 text-red-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                            />
                        </svg>
                    </button>
                </div>
            </div>

            
            {mostrarConfirmacion && (
                <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div className="bg-white rounded-lg shadow-2xl p-6 max-w-sm mx-4">
                        <h2 className="text-xl font-bold text-gray-800 mb-4">¿Desea confirmar?</h2>
                        
                        <p className="text-sm text-gray-600 mb-6">
                            ¿Estás seguro de que deseas eliminar a <strong>{nombreCompleto}</strong>? Esta acción no se puede deshacer.
                        </p>

                        
                        <div className="flex gap-3 justify-end">
                            <button
                                onClick={() => setMostrarConfirmacion(false)}
                                disabled={cargando}
                                className="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-lg transition disabled:opacity-50"
                            >
                                No
                            </button>
                            <button
                                onClick={handleEliminar}
                                disabled={cargando}
                                className="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition disabled:opacity-50"
                            >
                                {cargando ? 'Eliminando...' : 'Sí'}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </>
    );
}
