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

// Interface para las publicaciones con calificaciones
interface Publicacion {
    id: number;
    titulo: string;
    descripcion: string;
    costo: string;
    tipo: 'Servicio' | 'Producto';
    ofertaDemanda: 'Oferta' | 'Demanda';
    imagen: string;
    estado: 'Activo' | 'Desactivado' | 'Agotado';
    fecha: string;
    // Placeholder para popularidad.
    popularidad?: number | null;
}

interface VerPerfilProps extends PageProps {
    datosUsuario: {
        id: number;
        nombre: string;
        apellidoPaterno: string;
        apellidoMaterno: string;
        ciudad: string;
        telefono: string;
        direccion: string;
        email: string;
        fechaNacimiento: string;
        saldoHoras: number;
        foto: string;
    };
    publicaciones?: any[];
    isAdmin?: boolean;
    esAdminUsuarioAVer?: boolean;
    esPerfilPropio?: boolean;
}

export default function VerPerfil({ auth, datosUsuario, publicaciones = [], isAdmin = false, esAdminUsuarioAVer = false, esPerfilPropio = true }: VerPerfilProps) {
    const [filtroTipo, setFiltroTipo] = useState<'Servicio' | 'Producto' | null>(null);
    const [filtroOfertaDemanda, setFiltroOfertaDemanda] = useState<'Oferta' | 'Demanda' | null>(null);
    const [filtroOrden, setFiltroOrden] = useState<'precioMayor' | 'precioMenor' | 'popular' | 'reciente' | null>(null);
    
    // Las imágenes vienen correctas desde el backend con URLs en formato /imagen/{filename}
    const [serviciosPaginados, setServiciosPaginados] = useState<Publicacion[]>(publicaciones.slice(0, ITEMS_PER_PAGE));

    const serviciosFiltrados = publicaciones.filter(s => {
        const cumpleTipo = !filtroTipo || s.tipo === filtroTipo;
        const cumpleOferta = !filtroOfertaDemanda || s.ofertaDemanda === filtroOfertaDemanda;
        return cumpleTipo && cumpleOferta;
    });

    const serviciosOrdenados = [...serviciosFiltrados].sort((a, b) => {
        if (filtroOrden === 'precioMayor') {
            // Mayor a Menor: restar b - a para orden descendente
            const costA = typeof a.costo === 'string' ? parseInt(a.costo.split(' ')[0]) : parseInt(a.costo) || 0;
            const costB = typeof b.costo === 'string' ? parseInt(b.costo.split(' ')[0]) : parseInt(b.costo) || 0;
            return costB - costA;
        } else if (filtroOrden === 'precioMenor') {
            // Menor a Mayor: restar a - b para orden ascendente
            const costA = typeof a.costo === 'string' ? parseInt(a.costo.split(' ')[0]) : parseInt(a.costo) || 0;
            const costB = typeof b.costo === 'string' ? parseInt(b.costo.split(' ')[0]) : parseInt(b.costo) || 0;
            return costA - costB;
        } else if (filtroOrden === 'popular') {
            // TODO: POPULARIDAD (Cuando BD esté lista)
            // Este dropdown está LISTO para funcionar cuando verPerfil() en TestControllerCris
            // devuelva datos en el campo 'popularidad' basado en calificacions
            // Por ahora devuelve null, pero la lógica de ordenamiento está implementada
            // Cuando se agreguen datos de calificaciones, este dropdown funcionará automáticamente
            const popularidadA = Number(a.popularidad) || 0;
            const popularidadB = Number(b.popularidad) || 0;
            return popularidadB - popularidadA; // Mayor popularidad primero
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

    const perfilData = {
        id: datosUsuario.id,
        nombre: datosUsuario.nombre,
        apellidoPaterno: datosUsuario.apellidoPaterno,
        apellidoMaterno: datosUsuario.apellidoMaterno,
        titulo: '',
        foto: datosUsuario.foto,
        ciudad: datosUsuario.ciudad,
        telefono: datosUsuario.telefono,
        direccion: datosUsuario.direccion,
        email: datosUsuario.email,
        fechaNacimiento: datosUsuario.fechaNacimiento,
        saldoHoras: datosUsuario.saldoHoras,
        fechaExpiracion: '',
    };

    // Generar el returnTo correcto basado en si es perfil propio o no
    // Si es perfil propio: /verperfil (sin ID)
    // Si es perfil de otro usuario (admin): /verperfil/{id}
    const returnTo = esPerfilPropio ? '/verperfil' : `/verperfil/${datosUsuario.id}`;

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
                   
                    <PerfilInfo perfil={perfilData} esPerfilPropio={esPerfilPropio} isAdmin={isAdmin} usuarioId={datosUsuario.id} />

                    
                    {esPerfilPropio && (
                    <div className="barra-controles flex md:flex-row flex-row md:justify-between md:items-center px-4 py-2 gap-2 md:gap-4">
                       
                        <div className="flex items-center space-x-2 text-sm md:text-base">
                            <Link
                                href="/MyAdvertisements"
                                className="w-8 h-8 flex items-center justify-center border border-black bg-gray-200 rounded-full font-bold text-lg hover:bg-gray-300 transition"
                                title="Mis publicaciones"
                            >
                                ✎
                            </Link>
                            <span className="font-bold">Mis Publicaciones</span>
                            <span className="font-bold">/</span>
                            <span className="font-bold">Historial</span>
                        </div>

                        {publicaciones.length > 0 && (
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
                        )}
                    </div>
                    )}

                    {publicaciones.length > 0 && (
                        <>
                            <ServiceCardListview 
                                servicios={serviciosPaginados}
                                filtroTipo={filtroTipo}
                                filtroOfertaDemanda={filtroOfertaDemanda}
                                filtroOrden={filtroOrden}
                                returnTo={returnTo}
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
                        </>
                    )}
                    {publicaciones.length === 0 && !isAdmin && (
                        <div className="py-8 text-center">
                            <p className="text-gray-500">No hay publicaciones recientes</p>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
