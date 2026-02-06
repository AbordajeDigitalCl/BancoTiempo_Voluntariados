import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageProps } from '@/types';
import { Link } from '@inertiajs/react';
import FilaUsuario from '@/Components/DerivedComponents/FilaUsuario';
import { mockUsuariosAdmin } from '@/mockData/datosMockUsuarios';
import { useState, useEffect } from 'react';
import { useInfiniteScroll } from '@/hooks/useInfiniteScroll';

const ITEMS_PER_PAGE = 8;

export default function AdministrarUsuarios({ auth }: PageProps) {
    const [busquedaNombre, setBusquedaNombre] = useState('');
    const [busquedaCiudad, setBusquedaCiudad] = useState('');
    const [filtroTipo, setFiltroTipo] = useState('todos');
    const [usuariosEliminados, setUsuariosEliminados] = useState<number[]>([]);
    const [usuariosPaginados, setUsuariosPaginados] = useState<typeof mockUsuariosAdmin>([]);

   
    const handleEliminarUsuario = (usuarioId: number) => {
        setUsuariosEliminados((prev) => [...prev, usuarioId]);
    };

    
    const usuariosFiltrados = mockUsuariosAdmin
        .filter((usuario) => !usuariosEliminados.includes(usuario.id))
        .filter((usuario) => {
            
            const cumpleNombre = usuario.nombre
                .toLowerCase()
                .includes(busquedaNombre.toLowerCase());

            
            const cumpleCiudad = usuario.direccion
                .toLowerCase()
                .includes(busquedaCiudad.toLowerCase());

           
            return cumpleNombre && cumpleCiudad;
        });

    const { displayedItems, isLoading, hasMore, observerTarget, resetPagination } = useInfiniteScroll({
        itemsPerPage: ITEMS_PER_PAGE,
        totalItems: usuariosFiltrados.length,
        onLoadMore: (page) => {
            const start = page * ITEMS_PER_PAGE;
            const end = start + ITEMS_PER_PAGE;
            setUsuariosPaginados(prev => [
                ...prev,
                ...usuariosFiltrados.slice(start, end)
            ]);
        },
    });

    
    useEffect(() => {
        resetPagination();
        setUsuariosPaginados(usuariosFiltrados.slice(0, ITEMS_PER_PAGE));
    }, [busquedaNombre, busquedaCiudad, filtroTipo, usuariosFiltrados.length]);

    return (
        <AuthenticatedLayout user={auth.user}>
            <div className="w-full flex flex-col items-center space-y-8 p-4 md:p-6">           
                <div className="w-full max-w-6xl bg-white rounded-lg shadow-md overflow-hidden">
                    <div className="p-4 md:p-6 border-b border-gray-200">
                        <h1 className="text-2xl md:text-3xl font-bold text-gray-800">Administración Usuarios</h1>
                    </div>

                    
                    <div className="p-4 md:p-6 border-b border-gray-200 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        
                        <Link
                            href="/register"
                            className="w-12 h-12 rounded-full bg-blue-700 hover:bg-blue-800 transition flex items-center justify-center text-white text-2xl font-bold flex-shrink-0 shadow-md hover:shadow-lg"
                            title="Crear nuevo usuario"
                        >
                            +
                        </Link>

                     
                        <div className="flex-1 flex items-center gap-3 ml-0 md:ml-4">
                            <div className="flex-1 relative">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                    />
                                </svg>
                                <input
                                    type="text"
                                    placeholder="Buscar"
                                    value={busquedaNombre}
                                    onChange={(e) => setBusquedaNombre(e.target.value)}
                                    className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-gray-600 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>
                        </div>

                        
                        <div className="flex-1 flex items-center gap-3">
                            <div className="flex-1 relative">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                    />
                                </svg>
                                <input
                                    type="text"
                                    placeholder="Ciudad"
                                    value={busquedaCiudad}
                                    onChange={(e) => setBusquedaCiudad(e.target.value)}
                                    className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-gray-600 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>
                        </div>

                        
                        <div className="flex-shrink-0">
                            <select
                                value={filtroTipo}
                                onChange={(e) => setFiltroTipo(e.target.value)}
                                className="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
                            >
                                <option value="todos">Todos</option>
                                <option value="usuarios">Usuarios</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>

                   
                    <div className="p-4 md:p-6 border-b border-gray-200">
                        <h2 className="text-xl md:text-2xl font-bold text-gray-800">Lista de Usuarios</h2>
                        <p className="text-gray-600 text-sm mt-1">Total: {usuariosFiltrados.length} usuarios</p>
                    </div>

                    
                    <div className="divide-y divide-gray-200">
                        {usuariosPaginados.length > 0 ? (
                            usuariosPaginados.map((usuario) => (
                                <FilaUsuario
                                    key={usuario.id}
                                    usuario={usuario}
                                    onEliminar={handleEliminarUsuario}
                                />
                            ))
                        ) : (
                            <div className="p-4 md:p-6 text-center">
                                <p className="text-gray-600 font-medium">
                                    Ningún usuario coincide con los criterios de búsqueda
                                </p>
                            </div>
                        )}
                    </div>

                    
                    <div ref={observerTarget} className="flex justify-center py-8 border-t border-gray-200">
                        {isLoading && (
                            <div className="text-center">
                                <div className="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                <p className="text-gray-500 mt-2">Cargando más usuarios...</p>
                            </div>
                        )}
                        {!hasMore && usuariosPaginados.length > 0 && (
                            <p className="text-gray-400 text-sm">No hay más usuarios</p>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
