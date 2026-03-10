 import { PropsWithChildren, ReactNode, useState, useCallback } from 'react';
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
  const isAdmin: boolean = page?.props?.isAdmin ?? false;

  const totalBadge = unreadMessageCount;

  // Manejar click del botón atrás
  const handleBackClick = useCallback(() => {
    window.history.back();
  }, []);

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
  const hrefProfile = safeRoute('verperfil', '/verperfil');

  const CountBadge = ({ value }: { value: number }) => {
    if (!value || value <= 0) return null;

    return (
      <span className="ml-2 inline-flex min-w-6 justify-center rounded-full bg-black px-2 py-0.5 text-xs text-white tabular-nums">
        {value}
      </span>
    );
  };

  const hasUser = !!user && typeof user === 'object';

  return (
    <div className="min-h-screen bg-gray-100 flex flex-col">
      <nav className="bg-white border-b border-gray-100">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between h-16">
            <div className="flex">
              <div className="shrink-0 flex items-center">
                {/* Botón atrás ahora usa history.back() en lugar de ir a /dashboard */}
                <button 
                  onClick={handleBackClick}
                  className="h-10 w-10 rounded-lg border border-gray-200 flex items-center justify-center sm:hidden hover:bg-gray-100 transition"
                  title="Atrás"
                >
                  <svg className="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 19l-7-7 7-7" />
                  </svg>
                </button>
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

                <NavLink href={hrefProfile} active={hasRoute('verperfil') ? route().current('verperfil') : false}>
                  Ver Perfil
                </NavLink>
              </div>
            </div>

            <div className="hidden sm:flex sm:items-center sm:ms-6">
              {hasUser ? (
                <div className="ms-3 relative">
                  <Dropdown>
                    <Dropdown.Trigger>
                      <span className="inline-flex rounded-md">
                        <button
                          type="button"
                          className="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                        >
                          <span className="relative inline-flex items-center">
                            {user?.name}
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
                          <CountBadge value={unreadMessageCount} />
                        </span>
                      </Dropdown.Link>

                      <Dropdown.Link href={hrefProfile}>Ver perfil</Dropdown.Link>

                      {isAdmin && (
                        <Dropdown.Link href={safeRoute('administrar-usuarios', '/Administrar-Usuarios')}>
                          Usuarios
                        </Dropdown.Link>
                      )}

                      <div className="h-px bg-gray-100 my-1" />

                      <Dropdown.Link href={safeRoute('logout', '/logout')} method="post" as="button">
                        Cerrar sesión
                      </Dropdown.Link>
                    </Dropdown.Content>
                  </Dropdown>
                </div>
              ) : (
                <div className="flex items-center gap-2">
                  <Link
                    href="/login"
                    className="inline-flex items-center rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                  >
                    Iniciar sesión
                  </Link>
                  <Link
                    href="/register"
                    className="inline-flex items-center rounded-lg bg-black px-4 py-2 text-sm text-white hover:bg-gray-800"
                  >
                    Crear cuenta
                  </Link>
                </div>
              )}
            </div>

            <div className="-me-2 flex items-center sm:hidden">
              <button
                onClick={() => setShowingNavigationDropdown((previousState) => !previousState)}
                className="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 transition duration-150 ease-in-out"
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
            <ResponsiveNavLink href={hrefDashboard}>Dashboard</ResponsiveNavLink>
            <ResponsiveNavLink href={hrefAds}>Publicaciones</ResponsiveNavLink>
            <ResponsiveNavLink href={hrefChat}>
              <span className="flex items-center justify-between w-full">
                <span>Mensajes</span>
                <CountBadge value={unreadMessageCount} />
              </span>
            </ResponsiveNavLink>
            <ResponsiveNavLink href={hrefMyAds}>Mis publicaciones</ResponsiveNavLink>
            <ResponsiveNavLink href={hrefProfile}>Ver Perfil</ResponsiveNavLink>

            {isAdmin && (
              <ResponsiveNavLink href={safeRoute('administrar-usuarios', '/Administrar-Usuarios')}>
                Usuarios
              </ResponsiveNavLink>
            )}
          </div>

          {hasUser ? (
            <div className="border-t border-gray-200 pt-4 pb-1">
              <div className="px-4">
                <div className="text-base font-medium text-gray-800">{user?.name}</div>
                {user?.email && <div className="text-sm font-medium text-gray-500">{user.email}</div>}
              </div>

              <div className="mt-3 space-y-1">
                <ResponsiveNavLink href={safeRoute('logout', '/logout')} method="post" as="button">
                  Cerrar sesión
                </ResponsiveNavLink>
              </div>
            </div>
          ) : (
            <div className="border-t border-gray-200 pt-4 pb-1 space-y-1">
              <ResponsiveNavLink href="/login">Iniciar sesión</ResponsiveNavLink>
              <ResponsiveNavLink href="/register">Crear cuenta</ResponsiveNavLink>
            </div>
          )}
        </div>
      </nav>

      {header && (
        <header className="bg-white shadow">
          <div className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">{header}</div>
        </header>
      )}

      <main className="flex-1">{children}</main>

      <footer className="border-t border-gray-200 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <a
            target="_blank"
            rel="noopener noreferrer"
            className="flex items-center justify-center gap-3 group transition-all duration-300"
          >
            <img
              src="/assets/footer-logo.png"
              alt="Abordaje Digital"
              className="h-8 w-8 object-contain transition-all duration-300 group-hover:scale-110"
            />

            <span className="text-sm text-gray-600 font-medium transition-all duration-300 group-hover:text-black">
              Abordaje Digital
            </span>
          </a>
        </div>
      </footer>
    </div>
  );
}