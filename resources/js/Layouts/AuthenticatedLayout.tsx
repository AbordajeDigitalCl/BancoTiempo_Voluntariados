import { useState, PropsWithChildren, ReactNode } from 'react';
import ApplicationLogo from '@/Components/ApplicationLogo';
import Dropdown from '@/Components/Dropdown';
import NavLink from '@/Components/NavLink';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink';
import { Link, usePage } from '@inertiajs/react';
import { User } from '@/types';

function IconUser(props: React.SVGProps<SVGSVGElement>) {
    return (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" {...props}>
            <path strokeWidth="1.75" strokeLinecap="round" strokeLinejoin="round" d="M20 21a8 8 0 0 0-16 0" />
            <path strokeWidth="1.75" strokeLinecap="round" strokeLinejoin="round" d="M12 13a4 4 0 1 0-4-4 4 4 0 0 0 4 4Z" />
        </svg>
    );
}

function IconBell(props: React.SVGProps<SVGSVGElement>) {
    return (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" {...props}>
            <path strokeWidth="1.75" strokeLinecap="round" strokeLinejoin="round" d="M15 17H9" />
            <path strokeWidth="1.75" strokeLinecap="round" strokeLinejoin="round" d="M18 17V11a6 6 0 1 0-12 0v6l-2 2h16l-2-2Z" />
            <path strokeWidth="1.75" strokeLinecap="round" strokeLinejoin="round" d="M10 21a2 2 0 0 0 4 0" />
        </svg>
    );
}

function IconChat(props: React.SVGProps<SVGSVGElement>) {
    return (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" {...props}>
            <path strokeWidth="1.75" strokeLinecap="round" strokeLinejoin="round" d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4Z" />
        </svg>
    );
}

function IconLogout(props: React.SVGProps<SVGSVGElement>) {
    return (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" {...props}>
            <path strokeWidth="1.75" strokeLinecap="round" strokeLinejoin="round" d="M10 17l-1 0a4 4 0 0 1-4-4V7a4 4 0 0 1 4-4h1" />
            <path strokeWidth="1.75" strokeLinecap="round" strokeLinejoin="round" d="M17 7l4 5-4 5" />
            <path strokeWidth="1.75" strokeLinecap="round" strokeLinejoin="round" d="M21 12H10" />
        </svg>
    );
}

function Badge({ value }: { value: number }) {
    if (!value || value <= 0) return null;
    const text = value > 99 ? '99+' : String(value);

    return (
        <span className="ml-auto inline-flex min-w-[22px] items-center justify-center rounded-full bg-red-600 px-1.5 py-0.5 text-[11px] font-semibold leading-none text-white">
            {text}
        </span>
    );
}

function MenuRow({ icon, label, badge }: { icon: ReactNode; label: string; badge?: number }) {
    return (
        <div className="flex items-center gap-3 w-full">
            <span className="text-gray-500">{icon}</span>
            <span className="text-sm">{label}</span>
            <Badge value={badge ?? 0} />
        </div>
    );
}

type SharedProps = {
    nav?: {
        badges?: {
            notifications?: number;
            messages?: number;
        };
    };
};

// ✅ Evita que Ziggy rompa toda la app si una ruta no existe.
function safeRoute(name: string, fallback: string) {
    try {
        // @ts-ignore Ziggy global
        if (typeof route === 'function' && typeof route().has === 'function' && route().has(name)) {
            // @ts-ignore
            return route(name);
        }
    } catch (e) {
        // ignore
    }
    return fallback;
}

export default function Authenticated({
    user,
    header,
    children,
}: PropsWithChildren<{ user: User; header?: ReactNode }>) {
    const [showingNavigationDropdown, setShowingNavigationDropdown] = useState(false);

    const { props } = usePage<SharedProps>();
    const unreadNotifications = Number(props?.nav?.badges?.notifications ?? 0);
    const unreadMessages = Number(props?.nav?.badges?.messages ?? 0);

    // ✅ Fallbacks por URL (ajústalos si tus URLs reales son otras)
    const hrefDashboard = safeRoute('dashboard', '/dashboard');
    const hrefAdvertisement = safeRoute('advertisement', '/Advertisement'); // o /advertisement
    const hrefChat = safeRoute('chat', '/chat');
    const hrefProfile = safeRoute('verperfil', '/verperfil');
    const hrefEditService = safeRoute('editarservicio', '/editarservicio');
    const hrefEditPost = safeRoute('editarpublicacion', '/editarpublicacion');
    const hrefNotifications = safeRoute('notifications', '/Notificaciones'); // o /notifications
    const hrefLogout = safeRoute('logout', '/logout');

    return (
        <div className="min-h-screen bg-gray-100">
            <nav className="bg-white border-b border-gray-100">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-16">
                        <div className="flex">
                            <div className="shrink-0 flex items-center">
                                <Link href={hrefDashboard}>
                                    <ApplicationLogo className="block h-9 w-auto fill-current text-gray-800" />
                                </Link>
                            </div>

                            <div className="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink href={hrefDashboard} active={route().current?.('dashboard')}>
                                    Dashboard
                                </NavLink>

                                <NavLink href={hrefAdvertisement} active={route().current?.('advertisement')}>
                                    Publicaciones
                                </NavLink>

                                <NavLink href={hrefChat} active={route().current?.('chat')}>
                                    Mensajes
                                </NavLink>

                                <NavLink href={hrefProfile} active={route().current?.('verperfil')}>
                                    Ver Perfil
                                </NavLink>

                                <NavLink href={hrefEditService} active={route().current?.('editarservicio')}>
                                    Editar Servicio
                                </NavLink>

                                <NavLink href={hrefEditPost} active={route().current?.('editarpublicacion')}>
                                    Editar Publicacion
                                </NavLink>
                            </div>
                        </div>

                        <div className="hidden sm:flex sm:items-center sm:ms-6">
                            <div className="ms-3 relative">
                                <Dropdown>
                                    <Dropdown.Trigger>
                                        <span className="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                className="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                {user.name}
                                                <svg
                                                    className="ms-2 -me-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fillRule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clipRule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </Dropdown.Trigger>

                                    <Dropdown.Content>
                                        <Dropdown.Link href={hrefProfile}>
                                            <MenuRow icon={<IconUser className="h-5 w-5" />} label="Ver Perfil" />
                                        </Dropdown.Link>

                                        <Dropdown.Link href={hrefNotifications}>
                                            <MenuRow
                                                icon={<IconBell className="h-5 w-5" />}
                                                label="Notificaciones"
                                                badge={unreadNotifications}
                                            />
                                        </Dropdown.Link>

                                        <Dropdown.Link href={hrefChat}>
                                            <MenuRow
                                                icon={<IconChat className="h-5 w-5" />}
                                                label="Mensajes"
                                                badge={unreadMessages}
                                            />
                                        </Dropdown.Link>

                                        <Dropdown.Link href={hrefLogout} method="post" as="button">
                                            <MenuRow icon={<IconLogout className="h-5 w-5" />} label="Cerrar sesión" />
                                        </Dropdown.Link>
                                    </Dropdown.Content>
                                </Dropdown>
                            </div>
                        </div>

                        <div className="-me-2 flex items-center sm:hidden">
                            <button
                                onClick={() => setShowingNavigationDropdown((previousState) => !previousState)}
                                className="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                            >
                                <svg className="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        className={!showingNavigationDropdown ? 'inline-flex' : 'hidden'}
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        className={showingNavigationDropdown ? 'inline-flex' : 'hidden'}
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div className={(showingNavigationDropdown ? 'block' : 'hidden') + ' sm:hidden'}>
                    <div className="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink href={hrefDashboard} active={route().current?.('dashboard')}>
                            Dashboard
                        </ResponsiveNavLink>

                        <ResponsiveNavLink href={hrefAdvertisement} active={route().current?.('advertisement')}>
                            Publicaciones
                        </ResponsiveNavLink>

                        <ResponsiveNavLink href={hrefChat} active={route().current?.('chat')}>
                            <div className="flex items-center w-full">
                                <span>Mensajes</span>
                                <Badge value={unreadMessages} />
                            </div>
                        </ResponsiveNavLink>

                        <ResponsiveNavLink href={hrefProfile} active={route().current?.('verperfil')}>
                            Ver Perfil
                        </ResponsiveNavLink>

                        <ResponsiveNavLink href={hrefEditService} active={route().current?.('editarservicio')}>
                            Editar Servicio
                        </ResponsiveNavLink>

                        <ResponsiveNavLink href={hrefEditPost} active={route().current?.('editarpublicacion')}>
                            Editar Publicacion
                        </ResponsiveNavLink>

                        <ResponsiveNavLink href={hrefNotifications} active={route().current?.('notifications')}>
                            <div className="flex items-center w-full">
                                <span>Notificaciones</span>
                                <Badge value={unreadNotifications} />
                            </div>
                        </ResponsiveNavLink>
                    </div>

                    <div className="pt-4 pb-1 border-t border-gray-200">
                        <div className="px-4">
                            <div className="font-medium text-base text-gray-800">{user.name}</div>
                            <div className="font-medium text-sm text-gray-500">{user.email}</div>
                        </div>

                        <div className="mt-3 space-y-1">
                            <ResponsiveNavLink href={hrefProfile}>Ver Perfil</ResponsiveNavLink>
                            <ResponsiveNavLink href={hrefNotifications}>
                                <div className="flex items-center w-full">
                                    <span>Notificaciones</span>
                                    <Badge value={unreadNotifications} />
                                </div>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink href={hrefChat}>
                                <div className="flex items-center w-full">
                                    <span>Mensajes</span>
                                    <Badge value={unreadMessages} />
                                </div>
                            </ResponsiveNavLink>
                            <ResponsiveNavLink method="post" href={hrefLogout} as="button">
                                Cerrar sesión
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            {header && (
                <header className="bg-white shadow">
                    <div className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">{header}</div>
                </header>
            )}

            <main>{children}</main>
        </div>
    );
}
