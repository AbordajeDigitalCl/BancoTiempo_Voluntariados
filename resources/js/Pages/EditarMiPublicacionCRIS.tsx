import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { PageProps } from '@/types';
import FormEditarMiPublicacionCRIS from '@/Components/DerivedComponents/FormEditarMiPublicacionCRIS';

interface EditarPublicacionProps extends PageProps {
    datosPublicacion: {
        id: number;
        titulo: string;
        descripcion: string;
        costo: number;
        tipo: string;
        ofertaDemanda: string;
        imagen: string;
        estado: string;
        tipoPublicacion: number;
        imagenId?: number; // Imagen ID del servidor
    };
    returnTo?: string;
}

export default function EditarMiPublicacionCRIS({ auth, datosPublicacion, returnTo = '/verperfil' }: EditarPublicacionProps) {
    const publicacion = {
        id: datosPublicacion.id,
        titulo: datosPublicacion.titulo,
        descripcion: datosPublicacion.descripcion,
        costo: datosPublicacion.costo.toString(),
        tipo: datosPublicacion.tipo as 'Servicio' | 'Producto',
        ofertaDemanda: datosPublicacion.ofertaDemanda as 'Oferta' | 'Demanda',
        imagen: datosPublicacion.imagen,
        estado: datosPublicacion.estado as 'Activo' | 'Desactivado' | 'Agotado',
        fecha: new Date().toISOString().split('T')[0],
        imagenId: datosPublicacion.imagenId, // Entra id de imagen al componente
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Editar publicación" />
            <div className="w-full flex justify-center px-4 py-6">
                <FormEditarMiPublicacionCRIS publicacion={publicacion} returnTo={returnTo} />
            </div>
        </AuthenticatedLayout>
    );
}
