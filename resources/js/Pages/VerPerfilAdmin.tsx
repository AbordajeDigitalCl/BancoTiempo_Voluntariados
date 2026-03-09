import { useState, useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageProps } from '@/types';
import { Link } from '@inertiajs/react';
import PerfilInfo from '@/Components/DerivedComponents/PerfilInfo';
import ServiceCardListview from '@/Components/DerivedComponents/ServiceCardListview';
import ServicioDropDown from '@/Components/DerivedComponents/ServiciosDropDown';
import OrdenarDropDown from '@/Components/DerivedComponents/OrdenarDropDown';
import OfertaDropDown from '@/Components/DerivedComponents/OfertaDropDown';
import { mockProfileData, mockServiciosData } from '@/mockData/datosMockPerfil';
import { useInfiniteScroll } from '@/hooks/useInfiniteScroll';

const ITEMS_PER_PAGE = 5;

export default function VerPerfilAdmin({ auth, usuarioId }: PageProps & { usuarioId?: number }) {
  const [filtroTipo, setFiltroTipo] = useState<'Servicio' | 'Producto' | null>(null);
  const [filtroOfertaDemanda, setFiltroOfertaDemanda] = useState<'Oferta' | 'Demanda' | null>(null);
  const [filtroOrden, setFiltroOrden] = useState<'precioMayor' | 'precioMenor' | 'popular' | 'reciente' | null>(null);
  const [serviciosPaginados, setServiciosPaginados] = useState(mockServiciosData.slice(0, ITEMS_PER_PAGE));

  const perfilUsuario = mockProfileData;

  const serviciosFiltrados = mockServiciosData.filter((s) => {
    const cumpleTipo = !filtroTipo || s.tipo === filtroTipo;
    const cumpleOferta = !filtroOfertaDemanda || s.ofertaDemanda === filtroOfertaDemanda;
    return cumpleTipo && cumpleOferta;
  });

  const serviciosOrdenados = [...serviciosFiltrados].sort((a, b) => {
    if (filtroOrden === 'precioMenor') {
      const costA = parseInt(a.costo.split(' ')[0]);
      const costB = parseInt(b.costo.split(' ')[0]);
      return costA - costB;
    } else if (filtroOrden === 'precioMayor') {
      const costA = parseInt(a.costo.split(' ')[0]);
      const costB = parseInt(b.costo.split(' ')[0]);
      return costB - costA;
    } else if (filtroOrden === 'popular') {
      // TODO: Implementar cuando popularidad esté disponible
      return 0;
    } else if (filtroOrden === 'reciente') {
      return new Date(b.fecha).getTime() - new Date(a.fecha).getTime();
    }
    return 0;
  });

  const { isLoading, hasMore, observerTarget, resetPagination } = useInfiniteScroll({
    itemsPerPage: ITEMS_PER_PAGE,
    totalItems: serviciosOrdenados.length,
    onLoadMore: (page) => {
      const start = page * ITEMS_PER_PAGE;
      const end = start + ITEMS_PER_PAGE;
      setServiciosPaginados((prev) => [...prev, ...serviciosOrdenados.slice(start, end)]);
    },
  });

  useEffect(() => {
    resetPagination();
    setServiciosPaginados(serviciosOrdenados.slice(0, ITEMS_PER_PAGE));
  }, [filtroTipo, filtroOfertaDemanda, filtroOrden, serviciosOrdenados.length]);

  const editHref = usuarioId ? `/perfil-Admin/${usuarioId}` : '/Administrar-Usuarios';

  return (
    <AuthenticatedLayout user={auth.user}>
      <style>{`
        header nav button:first-of-type { display: none !important; }
        header button[type="button"]:not([title="Menú"]) { display: none !important; }

        @media (max-width: 768px) {
          .barra-controles { flex-direction: column; align-items: flex-start; gap: 0.75rem; }
          .filtros { width: 100%; gap: 0.5rem; flex-wrap: wrap; }
        }
      `}</style>

      <div className="w-full flex justify-center px-4 py-6">
        <div className="w-full max-w-6xl space-y-4">
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div className="p-4 border-b border-gray-200 flex items-center justify-between gap-2">
              <div className="min-w-0">
                <div className="text-lg font-semibold text-gray-900 truncate">Perfil de usuario</div>
                <div className="text-sm text-gray-600">Vista de administración</div>
              </div>

              <div className="flex items-center gap-2">
                <Link
                  href="/Administrar-Usuarios"
                  className="rounded-lg border border-gray-200 px-3 py-2 text-sm hover:bg-gray-50"
                >
                  Volver
                </Link>

                <Link
                  href={editHref}
                  className="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 hover:bg-gray-50"
                  title="Editar perfil"
                >
                  ✎
                </Link>
              </div>
            </div>

            <div className="p-4">
              <PerfilInfo perfil={perfilUsuario} />
            </div>

            <div className="barra-controles flex items-center justify-between px-4 py-3 border-t border-gray-200">
              <div className="flex items-center gap-2 text-sm">
                <span className="font-semibold text-gray-900">Publicaciones</span>
                <span className="text-gray-400">/</span>
                <span className="font-semibold text-gray-900">Historial</span>
              </div>

              <div className="filtros flex items-center gap-2">
                <OrdenarDropDown onOrdenChange={setFiltroOrden} ordenActual={filtroOrden} />
                <ServicioDropDown onTipoChange={setFiltroTipo} tipoActual={filtroTipo} />
                <OfertaDropDown onOfertaDemandaChange={setFiltroOfertaDemanda} ofertaDemandaActual={filtroOfertaDemanda} />
              </div>
            </div>

            <div className="p-4">
              <ServiceCardListview
                servicios={serviciosPaginados}
                filtroTipo={filtroTipo}
                filtroOfertaDemanda={filtroOfertaDemanda}
                filtroOrden={filtroOrden}
                returnTo={usuarioId ? `/verperfil/${usuarioId}` : '/verperfil'}
              />

              <div ref={observerTarget} className="flex justify-center py-8">
                {isLoading && (
                  <div className="text-center">
                    <div className="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                    <p className="text-gray-500 mt-2">Cargando más...</p>
                  </div>
                )}
                {!hasMore && serviciosPaginados.length > 0 && <p className="text-gray-400 text-sm">No hay más publicaciones</p>}
              </div>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
