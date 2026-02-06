import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import FormEditarMiPublicacionCRIS from '@/Components/DerivedComponents/FormEditarMiPublicacionCRIS';
import { mockServiciosData } from '@/mockData/datosMockPerfil';

export default function EditarMiPublicacionCRIS({ auth, publicacionId }: any) {
    const publicacion = mockServiciosData.find((p) => p.id === parseInt(publicacionId));

    if (!publicacion) {
        return (
            <AuthenticatedLayout user={auth.user}>
                <Head title="Editar publicación" />
                <div className="w-full flex justify-center px-4 py-6">
                    <p className="text-gray-600">Publicación no encontrada</p>
                </div>
            </AuthenticatedLayout>
        );
    }

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Editar publicación" />
            <div className="w-full flex justify-center px-4 py-6">
                <FormEditarMiPublicacionCRIS publicacion={publicacion} />
            </div>
        </AuthenticatedLayout>
    );
}
