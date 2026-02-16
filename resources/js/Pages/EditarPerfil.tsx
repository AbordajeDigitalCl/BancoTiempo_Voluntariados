import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageProps } from '@/types';
import FormEditarPerfil from '@/Components/DerivedComponents/FormEditarPerfil';
import { mockPerfilEditableData } from '@/mockData/datosMockEditarPerfil';

export default function EditarPerfil({ auth, datosUsuario }: PageProps & { datosUsuario?: any }) {
    const datos = datosUsuario || mockPerfilEditableData;

    return (
        <AuthenticatedLayout
            user={auth.user}
        >
            <div className="w-full flex flex-col items-center space-y-8 mt-8 mb-8">
                <div className="w-full max-w-2xl">
                    <FormEditarPerfil datosIniciales={datos} />
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
