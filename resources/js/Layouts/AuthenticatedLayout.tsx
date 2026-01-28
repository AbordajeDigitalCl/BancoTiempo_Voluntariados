import { useMemo, useState, PropsWithChildren, ReactNode } from 'react';
import ApplicationLogo from '@/Components/ApplicationLogo';
import Dropdown from '@/Components/Dropdown';
import NavLink from '@/Components/NavLink';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink';
import { Link, usePage } from '@inertiajs/react';
import { User } from '@/types';

type Noti = {
  id: number;
  title: string;
  body: string;
  time: string;
  unread?: boolean;
};

export default function Authenticated({
  user,
  header,
  children,
}: PropsWithChildren<{ user: User; header?: ReactNode }>) {
  const [showingNavigationDropdown, setShowingNavigationDropdown] = useState(false);

  const page: any = usePage();
  const notifications: Noti[] =
    page?.props?.notifications ??
    [
      { id: 1, title: 'Nueva publicación', body: 'Marcelo publicó “Gasfiter”.', time: 'Hace 2 min', unread: true },
      { id: 2, title: 'Mensaje', body: 'Pedro Juarez te envió un mensaje.', time: 'Hace 1 hora', unread: true },
      { id: 3, title: 'Actualización', body: 'Tu publicación fue guardada.', time: 'Ayer', unread: false },
    ];

  const unreadCount = useMemo(
    () => notifications.filter((n) => n.unread).length,
    [notifications]
  );

  return (
    <div className="min-h-screen bg-gray-100">
      <nav className="bg-white border-b border-gray-100">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between h-16">
            <div className="flex">
              <div className="shrink-0 flex items-center">
                <Link href="/">
                  <ApplicationLogo className="block h-9 w-auto fill-current text-gray-800" />
                </Link>
              </div>

              <div className="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                <NavLink href={route('dashboard')} active={route().current('dashboard')}>
                  Dashboard
                </NavLink>

                <NavLink href={route('advertisement')} active={route().current('advertisement')}>
                  Publicaciones
                </NavLink>

                <NavLink href={route('chat')} active={route().current('chat')}>
                  Mensajes
                </NavLink>

                <NavLink href={route('verperfil')} active={route().current('verperfil')}>
                  Ver Perfil
                </NavLink>

                <NavLink href={route('editarservicio')} active={route().current('editarservicio')}>
                  Editar Servicio
                </NavLink>

                <NavLink
                  href={route('editarpublicacion')}
                  active={route().current('editarpublicacion')}
                >
                  Editar Publicacion
                </NavLink>
              </div>
            </div>

            <div className="hidden sm:flex sm:items-center sm:ms-6 gap-3">
              <div className="relative">
                <Dropdown>
                  <Dropdown.Trigger>
                    <span className="inline-flex rounded-md">
                      <button
                        type="button"
                        className="relative inline-flex items-center justify-center w-10 h-10 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-gray-700"
                        aria-label="Notificaciones"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                          strokeWidth="2"
                          strokeLinecap="round"
                          strokeLinejoin="round"
                          className="w-5 h-5"
                        >
                          <path d="M18 8a6 6 0 10-12 0c0 7-3 7-3 7h18s-3 0-3-7" />
                          <path d="M13.73 21a2 2 0 01-3.46 0" />
                        </svg>

                        {unreadCount > 0 && (
                          <span className="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-red-600 text-white text-[11px] leading-[18px] text-center">
                            {unreadCount}
                          </span>
                        )}
                      </button>
                    </span>
                  </Dropdown.Trigger>

                  <Dropdown.Content>
                    <div className="px-4 py-2 text-xs text-gray-500">Notificaciones</div>

                    {notifications.slice(0, 4).map((n) => (
                      <Link
                        key={n.id}
                        href={route('notifications')}
                        className="block px-4 py-2 hover:bg-gray-50"
                      >
                        <div className="flex items-start justify-between gap-3">
                          <div className="text-sm text-gray-900 font-medium">{n.title}</div>
                          <div className="text-xs text-gray-500 whitespace-nowrap">{n.time}</div>
                        </div>
                        <div className="mt-1 text-sm text-gray-600">{n.body}</div>
                      </Link>
                    ))}

                    <div className="border-t border-gray-100">
                      <Link
                        href={route('notifications')}
                        className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                      >
                        Ver todas
                      </Link>
                    </div>
                  </Dropdown.Content>
                </Dropdown>
              </div>

              <div className="ms-1 relative">
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
                    <Dropdown.Link href={route('chat')}>Mensajes</Dropdown.Link>
                    <Dropdown.Link href={route('notifications')}>Notificaciones</Dropdown.Link>
                    <Dropdown.Link href={route('profile.edit')}>Profile</Dropdown.Link>
                    <Dropdown.Link href={route('logout')} method="post" as="button">
                      Log Out
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
            <ResponsiveNavLink href={route('dashboard')} active={route().current('dashboard')}>
              Dashboard
            </ResponsiveNavLink>

            <ResponsiveNavLink
              href={route('advertisement')}
              active={route().current('advertisement')}
            >
              Publicaciones
            </ResponsiveNavLink>

            <ResponsiveNavLink href={route('chat')} active={route().current('chat')}>
              Mensajes
            </ResponsiveNavLink>

            <ResponsiveNavLink href={route('notifications')} active={route().current('notifications')}>
              Notificaciones
            </ResponsiveNavLink>

            <ResponsiveNavLink href={route('verperfil')} active={route().current('verperfil')}>
              Ver Perfil
            </ResponsiveNavLink>

            <ResponsiveNavLink
              href={route('editarservicio')}
              active={route().current('editarservicio')}
            >
              Editar Servicio
            </ResponsiveNavLink>

            <ResponsiveNavLink
              href={route('editarpublicacion')}
              active={route().current('editarpublicacion')}
            >
              Editar Publicacion
            </ResponsiveNavLink>
          </div>

          <div className="pt-4 pb-1 border-t border-gray-200">
            <div className="px-4">
              <div className="font-medium text-base text-gray-800">{user.name}</div>
              <div className="font-medium text-sm text-gray-500">{user.email}</div>
            </div>

            <div className="mt-3 space-y-1">
              <ResponsiveNavLink href={route('profile.edit')}>Profile</ResponsiveNavLink>
              <ResponsiveNavLink method="post" href={route('logout')} as="button">
                Log Out
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

