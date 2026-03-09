import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageProps } from '@/types';
import FormEditarPerfil from '@/Components/DerivedComponents/FormEditarPerfil';
import { mockPerfilEditableData } from '@/mockData/datosMockEditarPerfil';

export default function EditarPerfil({ auth, datosUsuario, fotoInicial, returnTo = 'verperfil' }: PageProps & { datosUsuario?: any; fotoInicial?: string; returnTo?: string }) {
    const datos = datosUsuario || mockPerfilEditableData;
    const foto = fotoInicial || "https://picsum.photos/150?random=1";
    
    // Usar ID del usuario editado o fallback al usuario autenticado actual
    // Asegura que usuarioId sempre tenga un valor válido
    const usuarioId = datos?.id || auth.user.id;

    return (
        <AuthenticatedLayout
            user={auth.user}
        >
            <div className="w-full flex flex-col items-center space-y-8 mt-8 mb-8">
                <div className="w-full max-w-2xl">
                    <FormEditarPerfil 
                        datosIniciales={datos} 
                        fotoInicial={foto} 
                        usuarioId={usuarioId} 
                        returnTo={returnTo}
                        usuarioActualId={auth.user.id}
                    />
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

