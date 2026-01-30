import { Link } from '@inertiajs/react';
import { UsuarioHeader, OpcionMenu } from '@/mockData/datosMockEncabezadoMenu';
import { useMenuState } from '@/hooks/usarEstadoMenu';

interface MenuHamburguesaProps {
    usuario: UsuarioHeader;
    opciones: OpcionMenu[];
}

export default function MenuHamburguesa({ usuario, opciones }: MenuHamburguesaProps) {
    const { menuAbierto, toggleMenu, closeMenu } = useMenuState();

    const handleLogout = () => {
        // Aquí irá la lógica de logout (por ahora solo console.log)
        console.log('Logout ejecutado');
        closeMenu(); // Cerrar menú después de logout
        // En el futuro: window.location.href = '/logout' o usar Inertia
    };

    const renderIcon = (tipo: string) => {
        switch (tipo) {
            case 'mensajes':
                return (
                    <svg xmlns="http://www.w3.org/2000/svg" className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                );
            case 'notificaciones':
                return (
                    <svg xmlns="http://www.w3.org/2000/svg" className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                );
            case 'usuario':
                return (
                    <svg xmlns="http://www.w3.org/2000/svg" className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                );
            case 'logout':
                return (
                    <svg xmlns="http://www.w3.org/2000/svg" className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                );
            default:
                return null;
        }
    };

    return (
        <div className="relative">
            {/* Botón Hamburguesa */}
            <button
                onClick={toggleMenu}
                className="p-2 rounded-md hover:bg-gray-100 transition focus:outline-none focus:ring-2 focus:ring-blue-500"
                title="Menú"
            >
                <svg xmlns="http://www.w3.org/2000/svg" className="w-6 h-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            {/* Dropdown Menu */}
            {menuAbierto && (
                <div className="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 z-50 overflow-hidden">
                    {/* Header con info del usuario */}
                    <div className="bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-4 text-white">
                        <div className="flex items-center space-x-3">
                            <img
                                src={usuario.foto}
                                alt={usuario.nombre}
                                className="w-10 h-10 rounded-full border-2 border-white"
                            />
                            <div>
                                <p className="font-bold text-sm">{usuario.nombre} {usuario.apellido}</p>
                                <p className="text-xs text-blue-100">{usuario.email}</p>
                            </div>
                        </div>
                    </div>

                    {/* Opciones del menú */}
                    <div className="py-2">
                        {opciones.map((opcion, index) => (
                            <div key={opcion.id}>
                                {opcion.action === 'logout' ? (
                                    <button
                                        onClick={handleLogout}
                                        className="w-full px-4 py-2 text-left text-red-600 hover:bg-red-50 flex items-center space-x-2 text-sm font-medium transition"
                                    >
                                        {renderIcon(opcion.icono)}
                                        <span>{opcion.label}</span>
                                    </button>
                                ) : (
                                    <Link
                                        href={opcion.href || '#'}
                                        className="block px-4 py-2 text-gray-700 hover:bg-blue-50 flex items-center space-x-2 text-sm font-medium transition"
                                        onClick={closeMenu}
                                    >
                                        {renderIcon(opcion.icono)}
                                        <span>{opcion.label}</span>
                                    </Link>
                                )}
                                {/* Separador antes de logout */}
                                {index === opciones.length - 2 && (
                                    <div className="my-2 border-t border-gray-200"></div>
                                )}
                            </div>
                        ))}
                    </div>
                </div>
            )}

            {/* Overlay para cerrar el menú */}
            {menuAbierto && (
                <div
                    className="fixed inset-0 z-40"
                    onClick={closeMenu}
                ></div>
            )}
        </div>
    );
}
