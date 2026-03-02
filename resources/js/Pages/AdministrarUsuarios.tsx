import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageProps } from '@/types';
import { Link } from '@inertiajs/react';
import FilaUsuario from '@/Components/DerivedComponents/FilaUsuario';
import { mockUsuariosAdmin } from '@/mockData/datosMockUsuarios';
import { useState, useEffect, useMemo } from 'react';
import { useInfiniteScroll } from '@/hooks/useInfiniteScroll';

const ITEMS_PER_PAGE = 8;

export default function AdministrarUsuarios({ auth }: PageProps) {
  const [busquedaNombre, setBusquedaNombre] = useState('');
  const [busquedaCiudad, setBusquedaCiudad] = useState('');
  const [filtroTipo, setFiltroTipo] = useState<'todos' | 'usuarios' | 'admin'>('todos');
  const [usuariosEliminados, setUsuariosEliminados] = useState<number[]>([]);
  const [usuariosPaginados, setUsuariosPaginados] = useState<typeof mockUsuariosAdmin>([]);

  const handleEliminarUsuario = (usuarioId: number) => {
    setUsuariosEliminados((prev) => [...prev, usuarioId]);
  };

  const usuariosFiltrados = useMemo(() => {
    const n = busquedaNombre.trim().toLowerCase();
    const c = busquedaCiudad.trim().toLowerCase();

    return mockUsuariosAdmin
      .filter((usuario) => !usuariosEliminados.includes(usuario.id))
      .filter((usuario) => {
        if (filtroTipo === 'todos') return true;
        const rol = (usuario as any).rol ? String((usuario as any).rol).toLowerCase() : '';
        if (filtroTipo === 'admin') return rol.includes('admin');
        return !rol.includes('admin');
      })
      .filter((usuario) => {
        const nombre = String(usuario.nombre ?? '').toLowerCase();
        const apellido = String((usuario as any).apellido ?? '').toLowerCase();
        const full = `${nombre} ${apellido}`.trim();

        const ciudad = String((usuario as any).ciudad ?? '').toLowerCase();
        const direccion = String((usuario as any).direccion ?? '').toLowerCase();
        const lugar = ciudad || direccion;

        const cumpleNombre = n ? full.includes(n) : true;
        const cumpleCiudad = c ? lugar.includes(c) : true;

        return cumpleNombre && cumpleCiudad;
      });
  }, [busquedaNombre, busquedaCiudad, filtroTipo, usuariosEliminados]);

  const { isLoading, hasMore, observerTarget, resetPagination } = useInfiniteScroll({
    itemsPerPage: ITEMS_PER_PAGE,
    totalItems: usuariosFiltrados.length,
    onLoadMore: (page) => {
      const start = page * ITEMS_PER_PAGE;
      const end = start + ITEMS_PER_PAGE;
      setUsuariosPaginados((prev) => [...prev, ...usuariosFiltrados.slice(start, end)]);
    },
  });

  useEffect(() => {
    resetPagination();
    setUsuariosPaginados(usuariosFiltrados.slice(0, ITEMS_PER_PAGE));
  }, [busquedaNombre, busquedaCiudad, filtroTipo, usuariosFiltrados.length]);

  return (
    <AuthenticatedLayout user={auth.user}>
      <div className="w-full flex flex-col items-center space-y-8 p-4 md:p-6">
        <div className="w-full max-w-6xl bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <div className="p-4 md:p-6 border-b border-gray-200">
            <h1 className="text-2xl md:text-3xl font-bold text-gray-900">Administración de usuarios</h1>
            <p className="mt-1 text-sm text-gray-600">Gestiona usuarios y administradores.</p>
          </div>

          <div className="p-4 md:p-6 border-b border-gray-200 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <Link
              href="/register"
              className="w-full md:w-auto inline-flex items-center justify-center rounded-lg bg-black text-white px-4 py-2 text-sm font-medium hover:bg-gray-800"
              title="Crear nuevo usuario"
            >
              Crear usuario
            </Link>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-3 w-full md:w-auto md:flex-1">
              <div className="relative">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                  type="text"
                  placeholder="Buscar por nombre"
                  value={busquedaNombre}
                  onChange={(e) => setBusquedaNombre(e.target.value)}
                  className="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                />
              </div>

              <div className="relative">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                  type="text"
                  placeholder="Buscar por ciudad"
                  value={busquedaCiudad}
                  onChange={(e) => setBusquedaCiudad(e.target.value)}
                  className="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                />
              </div>

              <select
                value={filtroTipo}
                onChange={(e) => setFiltroTipo(e.target.value as any)}
                className="w-full px-4 py-2 border border-gray-200 rounded-lg text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer"
              >
                <option value="todos">Todos</option>
                <option value="usuarios">Usuarios</option>
                <option value="admin">Admin</option>
              </select>
            </div>
          </div>

          <div className="p-4 md:p-6 border-b border-gray-200">
            <div className="flex items-end justify-between gap-2">
              <div>
                <h2 className="text-xl md:text-2xl font-semibold text-gray-900">Lista de usuarios</h2>
                <p className="text-gray-600 text-sm mt-1">Total: {usuariosFiltrados.length}</p>
              </div>

              {(busquedaNombre.trim() || busquedaCiudad.trim() || filtroTipo !== 'todos' || usuariosEliminados.length) ? (
                <button
                  type="button"
                  onClick={() => {
                    setBusquedaNombre('');
                    setBusquedaCiudad('');
                    setFiltroTipo('todos');
                    setUsuariosEliminados([]);
                  }}
                  className="text-sm text-gray-600 hover:underline"
                >
                  Limpiar
                </button>
              ) : null}
            </div>
          </div>

          <div className="p-4 md:p-6 space-y-3">
            {usuariosPaginados.length > 0 ? (
              usuariosPaginados.map((usuario: any) => (
                <FilaUsuario key={usuario.id} usuario={usuario} onEliminar={handleEliminarUsuario} />
              ))
            ) : (
              <div className="rounded-xl border border-gray-200 bg-gray-50 p-6 text-center">
                <p className="text-gray-700 font-medium">Ningún usuario coincide con los criterios de búsqueda</p>
              </div>
            )}
          </div>

          <div ref={observerTarget} className="flex justify-center py-8 border-t border-gray-200">
            {isLoading && (
              <div className="text-center">
                <div className="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                <p className="text-gray-500 mt-2">Cargando más usuarios...</p>
              </div>
            )}
            {!hasMore && usuariosPaginados.length > 0 && <p className="text-gray-400 text-sm">No hay más usuarios</p>}
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
