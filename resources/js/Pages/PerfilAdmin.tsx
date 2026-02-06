import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageProps } from '@/types';
import PerfilInfo from '@/Components/DerivedComponents/PerfilInfo';
import { mockProfileData } from '@/mockData/datosMockPerfil';

export default function PerfilAdmin({ auth, usuarioId }: PageProps & { usuarioId?: number }) {
    const perfilUsuario = mockProfileData;

    return (
        <AuthenticatedLayout user={auth.user}>
            <style>{`
                header nav button:first-of-type { display: none !important; }
                header button[type="button"]:not([title="Menú"]) { display: none !important; }
            `}</style>

            <div className="w-full flex flex-col items-center space-y-8 mt-1">
                <div className='shadow-md rounded-xl bg-white'>
                   <PerfilInfo perfil={perfilUsuario} />
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
