import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageProps } from '@/types';
import MenuHamburguesa from '@/Components/DerivedComponents/MenuHamburguesa';
import { mockUsuarioHeader, mockOpcionesMenu } from '@/mockData/datosMockEncabezadoMenu';

export default function MenuHeader({ auth }: PageProps) {
    return (
        <AuthenticatedLayout user={auth.user}>
            <div className="w-full h-screen flex flex-col">
                <div className="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                    <h1 className="text-2xl font-bold text-gray-800">Menú Hamburguesa</h1>
                    <MenuHamburguesa usuario={mockUsuarioHeader} opciones={mockOpcionesMenu} />
                </div>

                
                <div className="flex-1 p-6">
                    <div className="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
                        <h2 className="text-xl font-bold text-gray-800 mb-4">Vista de Ejemplo</h2>
                        <p className="text-gray-600 mb-4">
                            Haz click en el botón de menú (≡) en la esquina superior derecha para ver las opciones:
                        </p>
                        <ul className="list-disc list-inside space-y-2 text-gray-700">
                            <li>Mensajes - Acceso rápido al chat</li>
                            <li>Notificaciones - Ver notificaciones</li>
                            <li>Nombre de Usuario - Ir al perfil</li>
                            <li>Cerrar Sesión - Salir de la aplicación</li>
                        </ul>
                        <div className="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
                            <p className="text-sm text-blue-800">
                                💡 <strong>Nota:</strong> Este es un menú con datos ficticios. 
                                Para usar en otras páginas, importa el componente MenuHamburguesa 
                                y pásale los datos del usuario y opciones.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
