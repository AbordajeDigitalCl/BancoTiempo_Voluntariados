import { useState, useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageProps } from '@/types';
import { Link } from '@inertiajs/react';
import PerfilInfo from '@/Components/DerivedComponents/PerfilInfo';
import ServiceCardListview from '@/Components/DerivedComponents/ServiceCardListview';
import ServicioDropDown from '@/Components/DerivedComponents/ServiciosDropDown';
import OrdenarDropDown from '@/Components/DerivedComponents/OrdenarDropDown';
import OfertaDropDown from '@/Components/DerivedComponents/OfertaDropDown';
import MenuHamburguesa from '@/Components/DerivedComponents/MenuHamburguesa';
import { mockProfileData, mockServiciosData } from '@/mockData/datosMockPerfil';
import { mockUsuarioHeader, mockOpcionesMenu } from '@/mockData/datosMockEncabezadoMenu';
import { useInfiniteScroll } from '@/hooks/useInfiniteScroll';

const ITEMS_PER_PAGE = 5;


export default function VerPerfil({ auth }: PageProps) {
    const [filtroTipo, setFiltroTipo] = useState<'Servicio' | 'Producto' | null>(null);
    const [filtroOfertaDemanda, setFiltroOfertaDemanda] = useState<'Oferta' | 'Demanda' | null>(null);
    const [filtroOrden, setFiltroOrden] = useState<'precio' | 'popular' | 'reciente' | null>(null);
    const [serviciosPaginados, setServiciosPaginados] = useState(mockServiciosData.slice(0, ITEMS_PER_PAGE));

    const serviciosFiltrados = mockServiciosData.filter(s => {
        const cumpleTipo = !filtroTipo || s.tipo === filtroTipo;
        const cumpleOferta = !filtroOfertaDemanda || s.ofertaDemanda === filtroOfertaDemanda;
        return cumpleTipo && cumpleOferta;
    });

    const serviciosOrdenados = [...serviciosFiltrados].sort((a, b) => {
        if (filtroOrden === 'precio') {
            const costA = parseInt(a.costo.split(' ')[0]);
            const costB = parseInt(b.costo.split(' ')[0]);
            return costA - costB;
        } else if (filtroOrden === 'reciente') {
            return new Date(b.fecha).getTime() - new Date(a.fecha).getTime();
        }
        return 0;
    });

    const { displayedItems, isLoading, hasMore, observerTarget, resetPagination } = useInfiniteScroll({
        itemsPerPage: ITEMS_PER_PAGE,
        totalItems: serviciosOrdenados.length,
        onLoadMore: (page) => {
            const start = page * ITEMS_PER_PAGE;
            const end = start + ITEMS_PER_PAGE;
            setServiciosPaginados(prev => [
                ...prev,
                ...serviciosOrdenados.slice(start, end)
            ]);
        },
    });

    useEffect(() => {
        resetPagination();
        setServiciosPaginados(serviciosOrdenados.slice(0, ITEMS_PER_PAGE));
    }, [filtroTipo, filtroOfertaDemanda, filtroOrden, serviciosOrdenados.length]);

    return (
        <AuthenticatedLayout
            user={auth.user}
        >
            <style>{`
                header nav button:first-of-type { display: none !important; }
                header button[type="button"]:not([title="Menú"]) { display: none !important; }
                
                @media (max-width: 768px) {
                    .barra-controles { flex-direction: row; gap: 0.5rem; flex-wrap: wrap; }
                    .filtros { gap: 0.5rem; }
                }
            `}</style>
            
            

            
            <div className="w-full flex flex-col items-center space-y-8 mt-1">
                <div className='shadow-md rounded-xl bg-white'>
                   
                    <PerfilInfo perfil={mockProfileData} />

                    
                    <div className="barra-controles flex md:flex-row flex-row md:justify-between md:items-center px-4 py-2 gap-2 md:gap-4">
                       
                        <div className="flex items-center space-x-2 text-sm md:text-base">
                            <Link
                                href="/MyAdvertisements"
                                className="w-8 h-8 flex items-center justify-center border border-black bg-gray-200 rounded-full font-bold text-lg hover:bg-gray-300 transition"
                                title="Editar perfil"
                            >
                                ✎
                            </Link>
                            <span className="font-bold">Mis Publicaciones</span>
                            <span className="font-bold">/</span>
                            <span className="font-bold">Historial</span>
                        </div>

                        <div className="filtros flex flex-row space-y-0 space-x-2 md:space-x-4 flex-wrap">
                            <OrdenarDropDown 
                                onOrdenChange={setFiltroOrden} 
                                ordenActual={filtroOrden} 
                            />
                            <ServicioDropDown 
                                onTipoChange={setFiltroTipo} 
                                tipoActual={filtroTipo} 
                            />
                            <OfertaDropDown 
                                onOfertaDemandaChange={setFiltroOfertaDemanda} 
                                ofertaDemandaActual={filtroOfertaDemanda} 
                            />
                        </div>
                    </div>

                    <ServiceCardListview 
                        servicios={serviciosPaginados}
                        filtroTipo={filtroTipo}
                        filtroOfertaDemanda={filtroOfertaDemanda}
                        filtroOrden={filtroOrden}
                    />

                    <div ref={observerTarget} className="flex justify-center py-8">
                        {isLoading && (
                            <div className="text-center">
                                <div className="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                <p className="text-gray-500 mt-2">Cargando más...</p>
                            </div>
                        )}
                        {!hasMore && serviciosPaginados.length > 0 && (
                            <p className="text-gray-400 text-sm">No hay más publicaciones</p>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
