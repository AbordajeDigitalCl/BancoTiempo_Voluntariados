import { Usuario } from '@/mockData/datosMockUsuarios';
import { Link } from '@inertiajs/react';
import { useState } from 'react';

interface FilaUsuarioProps {
    usuario: Usuario;
    onEliminar?: (usuarioId: number) => void;
}

export default function FilaUsuario({ usuario, onEliminar }: FilaUsuarioProps) {
    const [mostrarConfirmacion, setMostrarConfirmacion] = useState(false);
    return (
        <div className="flex items-center justify-between p-4 border-b border-gray-200 hover:bg-gray-50 transition">
           
            <div className="flex items-center gap-4 flex-1 min-w-0">
                
                <img
                    src={usuario.foto}
                    alt={usuario.nombre}
                    className="w-12 h-12 rounded-md flex-shrink-0 object-cover"
                />

                
                <div className="flex-1 min-w-0">
                    
                    <h3 className="font-bold text-gray-800 text-sm md:text-base">
                        {usuario.nombre}
                    </h3>

                    
                    <div className="flex items-center gap-6 mt-1 text-xs md:text-sm">
                        
                        <div className="flex flex-col items-start">
                            <div className="flex items-center gap-2">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    className="w-4 h-4 text-gray-600 flex-shrink-0"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"
                                    />
                                </svg>
                                <span className="text-gray-600 font-semibold">{usuario.ofertas}</span>
                            </div>
                            <span className="text-gray-500 text-xs">Ofertas</span>
                        </div>

                        
                        <div className="flex flex-col items-start">
                            <div className="flex items-center gap-2">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    className="w-4 h-4 text-gray-600 flex-shrink-0"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <circle cx="12" cy="12" r="10" strokeWidth={2} />
                                    <text x="12" y="15" textAnchor="middle" fontSize="8" fill="currentColor">
                                        ?
                                    </text>
                                </svg>
                                <span className="text-gray-600 font-semibold">{usuario.demandas}</span>
                            </div>
                            <span className="text-gray-500 text-xs">Demandas</span>
                        </div>

                        
                        <div className="flex-1 min-w-0">
                            <p className="text-gray-400 text-xs truncate">
                                {usuario.direccion.length > 30
                                    ? `${usuario.direccion.substring(0, 30)}...`
                                    : usuario.direccion}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            
            <div className="flex gap-2 flex-shrink-0 ml-4">
                
                <Link
                    href="/EditarPerfil"
                    className="flex flex-col items-center gap-1 p-2 rounded-lg hover:bg-blue-50 transition"
                    title="Editar usuario"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        className="w-4 h-4 md:w-5 md:h-5 text-blue-600"
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
                    <span className="text-xs text-blue-600 hidden md:block">Editar</span>
                </Link>

                
                <button
                    className="flex flex-col items-center gap-1 p-2 rounded-lg hover:bg-green-50 transition"
                    title="Ver comentarios"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        className="w-4 h-4 md:w-5 md:h-5 text-green-600"
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
                    <span className="text-xs text-green-600 hidden md:block">Comentarios</span>
                </button>

                
                <button
                    onClick={() => setMostrarConfirmacion(true)}
                    className="flex flex-col items-center gap-1 p-2 rounded-lg hover:bg-red-50 transition"
                    title="Eliminar usuario"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        className="w-4 h-4 md:w-5 md:h-5 text-red-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={2}
                            d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    <span className="text-xs text-red-600 hidden md:block">Eliminar</span>
                </button>
            </div>

            
            {mostrarConfirmacion && (
                <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div className="bg-white rounded-lg shadow-lg p-6 max-w-sm mx-4">
                        <h2 className="text-lg font-bold text-gray-800 mb-6">¿Desea confirmar?</h2>
                        
                        <div className="flex gap-3 justify-end">
                            <button
                                onClick={() => setMostrarConfirmacion(false)}
                                className="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-lg transition"
                            >
                                No
                            </button>
                            <button
                                onClick={() => {
                                    if (onEliminar) {
                                        onEliminar(usuario.id);
                                    }
                                    setMostrarConfirmacion(false);
                                }}
                                className="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition"
                            >
                                Sí
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
