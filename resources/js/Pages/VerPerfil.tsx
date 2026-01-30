import { useState } from 'react';
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

export default function VerPerfil({ auth }: PageProps) {
    const [filtroTipo, setFiltroTipo] = useState<'Servicio' | 'Producto' | null>(null);
    const [filtroOfertaDemanda, setFiltroOfertaDemanda] = useState<'Oferta' | 'Demanda' | null>(null);
    const [filtroOrden, setFiltroOrden] = useState<'precio' | 'popular' | 'reciente' | null>(null);

    return (
        <AuthenticatedLayout
            user={auth.user}
        >
            {/* Menú Hamburguesa en posición fija */}
            <div className="w-full flex justify-end px-4 pt-4 fixed top-0 right-0 z-50">
                <MenuHamburguesa usuario={mockUsuarioHeader} opciones={mockOpcionesMenu} />
            </div>

            {/* Contenedor principal */}
            <div className="w-full flex flex-col items-center space-y-8 mt-1">
                <div className='shadow-md rounded-xl bg-white'>
                    {/* Sección de información del perfil */}
                    <PerfilInfo perfil={mockProfileData} />

                    {/* Barra de controles: botón editar y filtros */}
                    <div className="flex justify-between items-center px-4 py-2">
                        {/* Botón editar y título de sección */}
                        <div className="flex items-center space-x-2">
                            <Link
                                href="/EditarPerfil"
                                className="w-8 h-8 flex items-center justify-center border border-black bg-gray-200 rounded-full font-bold text-lg hover:bg-gray-300 transition"
                                title="Editar perfil"
                            >
                                ✎
                            </Link>
                            <span className="font-bold">Mis Publicaciones</span>
                            <span className="font-bold">/</span>
                            <span className="font-bold">Historial</span>
                        </div>

                        {/* Controles de filtrado */}
                        <div className="flex space-x-4">
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

                    {/* Lista de servicios/productos */}
                    <ServiceCardListview 
                        servicios={mockServiciosData}
                        filtroTipo={filtroTipo}
                        filtroOfertaDemanda={filtroOfertaDemanda}
                        filtroOrden={filtroOrden}
                    />
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
