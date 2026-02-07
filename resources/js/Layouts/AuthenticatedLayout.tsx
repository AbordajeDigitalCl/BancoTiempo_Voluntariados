import { PropsWithChildren, ReactNode, useState } from 'react';
import Dropdown from '@/Components/Dropdown';
import NavLink from '@/Components/NavLink';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink';
import { Link, usePage } from '@inertiajs/react';

export default function Authenticated({
  user,
  header,
  children,
}: PropsWithChildren<{ user: any; header?: ReactNode }>) {
  const [showingNavigationDropdown, setShowingNavigationDropdown] = useState(false);


  const page: any = usePage();
  const unreadMessageCount: number = Number(page?.props?.unreadMessageCount ?? 0);
  const unreadNotificationCount: number = Number(page?.props?.unreadNotificationCount ?? 0);


  const totalBadge = unreadMessageCount + unreadNotificationCount;

  const hasRoute = (name: string) => {
    try {

      return typeof route === 'function' && !!route().has?.(name);
    } catch {
      return false;
    }
  };

  const safeRoute = (name: string, fallback: string) => {
    try {

      if (typeof route === 'function' && route().has?.(name)) return route(name);
    } catch {}
    return fallback;
  };

  const hrefDashboard = safeRoute('dashboard', '/dashboard');
  const hrefAds = safeRoute('advertisement', '/Advertisement');
  const hrefMyAds = safeRoute('myadvertisements', '/MyAdvertisements');
  const hrefChat = safeRoute('chat', '/chat');
  const hrefProfile = safeRoute('profile.edit', '/profile');
  const hrefNotifications = safeRoute('notifications', '/Notificaciones');


  const CountBadge = ({ value }: { value: number }) => {
    if (!value || value <= 0) return null;

    return (
      <span className="ml-2 inline-flex min-w-6 justify-center rounded-full bg-black px-2 py-0.5 text-xs text-white tabular-nums">
        {value}
      </span>

    );
  };

  return (
    <div className="min-h-screen bg-gray-100">
      <nav className="bg-white border-b border-gray-100">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between h-16">
            <div className="flex">
              <div className="shrink-0 flex items-center">
                <Link href={hrefDashboard}>
                  <div className="h-10 w-10 rounded-lg border border-gray-200 flex items-center justify-center" />
                </Link>
              </div>

              <div className="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                <NavLink href={hrefDashboard} active={hasRoute('dashboard') ? route().current('dashboard') : false}>
                  Dashboard
                </NavLink>

                <NavLink href={hrefAds} active={hasRoute('advertisement') ? route().current('advertisement') : false}>
                  Publicaciones
                </NavLink>

                <NavLink href={hrefChat} active={hasRoute('chat') ? route().current('chat') : false}>
                  Mensajes
                  <CountBadge value={unreadMessageCount} />
                </NavLink>

                <NavLink
                  href={hrefMyAds}
                  active={hasRoute('myadvertisements') ? route().current('myadvertisements') : false}
                >
                  Mis publicaciones
                </NavLink>

                <NavLink href={hrefProfile} active={hasRoute('profile.edit') ? route().current('profile.edit') : false}>
                  Ver Perfil
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
                        <span className="relative inline-flex items-center">
                          {user.name}

                          {totalBadge > 0 && (
                            <span className="ml-2 inline-flex min-w-6 justify-center rounded-full bg-black px-2 py-0.5 text-xs text-white tabular-nums">
                              {totalBadge}
                            </span>
                          )}
                        </span>

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
                    <Dropdown.Link href={hrefChat}>
                      <span className="flex items-center justify-between">
                        <span>Mensajes</span>
                        {unreadMessageCount > 0 && (
                          <span className="inline-flex min-w-6 justify-center rounded-full bg-black px-2 py-0.5 text-xs text-white tabular-nums">
                            {unreadMessageCount}
                          </span>
                        )}
                      </span>
                    </Dropdown.Link>

                    <Dropdown.Link href={hrefNotifications}>
                      <span className="flex items-center justify-between">
                        <span>Notificaciones</span>
                        {unreadNotificationCount > 0 && (
                          <span className="inline-flex min-w-6 justify-center rounded-full bg-black px-2 py-0.5 text-xs text-white tabular-nums">
                            {unreadNotificationCount}
                          </span>
                        )}
                      </span>
                    </Dropdown.Link>

                    <Dropdown.Link href={hrefProfile}>Ver perfil</Dropdown.Link>

                    <div className="h-px bg-gray-100 my-1" />

                    <Dropdown.Link href={safeRoute('logout', '/logout')} method="post" as="button">
                      Cerrar sesión
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
            <ResponsiveNavLink href={hrefDashboard} active={hasRoute('dashboard') ? route().current('dashboard') : false}>
              Dashboard
            </ResponsiveNavLink>

            <ResponsiveNavLink href={hrefAds} active={hasRoute('advertisement') ? route().current('advertisement') : false}>
              Publicaciones
            </ResponsiveNavLink>

            <ResponsiveNavLink href={hrefChat} active={hasRoute('chat') ? route().current('chat') : false}>
              <span className="flex items-center justify-between w-full">
                <span>Mensajes</span>
                {unreadMessageCount > 0 && (
                  <span className="inline-flex min-w-6 justify-center rounded-full bg-black px-2 py-0.5 text-xs text-white tabular-nums">
                    {unreadMessageCount}
                  </span>
                )}
              </span>
            </ResponsiveNavLink>

            <ResponsiveNavLink
              href={hrefMyAds}
              active={hasRoute('myadvertisements') ? route().current('myadvertisements') : false}
            >
              Mis publicaciones
            </ResponsiveNavLink>

            <ResponsiveNavLink href={hrefProfile} active={hasRoute('profile.edit') ? route().current('profile.edit') : false}>
              Ver Perfil
            </ResponsiveNavLink>

            <ResponsiveNavLink
              href={hrefNotifications}
              active={hasRoute('notifications') ? route().current('notifications') : false}
            >
              <span className="flex items-center justify-between w-full">
                <span>Notificaciones</span>
                {unreadNotificationCount > 0 && (
                  <span className="inline-flex min-w-6 justify-center rounded-full bg-black px-2 py-0.5 text-xs text-white tabular-nums">
                    {unreadNotificationCount}
                  </span>
                )}
              </span>
            </ResponsiveNavLink>
          </div>

          <div className="pt-4 pb-1 border-t border-gray-200">
            <div className="px-4">
              <div className="font-medium text-base text-gray-800">{user.name}</div>
              <div className="font-medium text-sm text-gray-500">{user.email}</div>
            </div>

            <div className="mt-3 space-y-1">
              <ResponsiveNavLink href={hrefProfile}>Ver perfil</ResponsiveNavLink>
              <ResponsiveNavLink method="post" href={safeRoute('logout', '/logout')} as="button">
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
