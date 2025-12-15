import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageProps } from '@/types';
import PerfilInfo from '@/Components/DerivedComponents/PerfilInfo';
import ServiceCardListview from '@/Components/DerivedComponents/ServiceCardListview';
import ServicioDropDown from '@/Components/DerivedComponents/ServiciosDropDown';
import OrdenarDropDown from '@/Components/DerivedComponents/OrdenarDropDown';
import OfertaDropDown from '@/Components/DerivedComponents/OfertaDropDown';

export default function VerPerfil({ auth }: PageProps) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            // header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">VerPerfil</h2>}
        >
            {/* <div className="w-full h-full flex items-center justify-center"> */}
            <div className="w-full flex flex-col items-center space-y-8 mt-1">
                <div className='shadow-md rounded-xl bg-white'>
                    <PerfilInfo></PerfilInfo>
                    <div className="flex justify-between items-center px-4 py-2">
                        <div className="flex items-center space-x-2">
                            <button className="w-8 h-8 flex items-center justify-center border border-black bg-gray-200 rounded-full font-bold text-lg">
                            +
                            </button>
                        
                            <span className="font-bold">Mis Publicaciones</span>
                            <span className="font-bold">/</span>
                            <span className="font-bold">Historial</span>
                        </div>

                        <div className="flex space-x-4">
                            <OrdenarDropDown></OrdenarDropDown>
                            <ServicioDropDown></ServicioDropDown>
                            <OfertaDropDown></OfertaDropDown>
                        </div>
                    </div>

                    <ServiceCardListview></ServiceCardListview>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
