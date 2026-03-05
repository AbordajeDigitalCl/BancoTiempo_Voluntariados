import { Link, Head } from '@inertiajs/react';
import { PageProps } from '@/types';

function NavLink({
  href,
  children,
}: {
  href: string;
  children: React.ReactNode;
}) {
  return (
    <a
      href={href}
      className="text-sm font-medium text-gray-700 hover:text-gray-900 transition"
    >
      {children}
    </a>
  );
}

function FeatureItem({ children }: { children: React.ReactNode }) {
  return (
    <li className="flex gap-2 text-sm text-gray-700">
      <span className="mt-2 h-1.5 w-1.5 rounded-full bg-gray-300" />
      <span className="leading-relaxed">{children}</span>
    </li>
  );
}

function Section({
  id,
  title,
  children,
}: {
  id: string;
  title: string;
  children: React.ReactNode;
}) {
  return (
    <section id={id} className="scroll-mt-24">
      <div className="bg-white rounded-2xl shadow-sm border border-gray-200 p-7">
        <h2 className="text-xl font-semibold text-gray-900">{title}</h2>
        <div className="mt-3">{children}</div>
      </div>
    </section>
  );
}

export default function Welcome({
  auth,
  laravelVersion,
  phpVersion,
}: PageProps<{ laravelVersion: string; phpVersion: string }>) {
  const primaryCta = auth.user ? route('dashboard') : route('login');
  const secondaryCta = auth.user ? route('advertisement') : route('register');

  const primaryLabel = auth.user ? 'Ir al Dashboard' : 'Iniciar sesión';
  const secondaryLabel = auth.user ? 'Ver publicaciones' : 'Crear cuenta';

  return (
    <>
      <Head title="Banco de tiempo" />

      <div className="min-h-screen bg-gray-100 flex flex-col">
        <header className="sticky top-0 z-30 bg-white/90 backdrop-blur border-b border-gray-200">
          <div className="mx-auto max-w-6xl px-4">
            <div className="h-16 flex items-center justify-between">
              <div className="flex items-center gap-8">
                <a href="#top" className="text-sm font-semibold text-gray-900">
                  Banco de tiempo
                </a>

                <nav className="hidden sm:flex items-center gap-6">
                  <NavLink href="#nosotros">Nosotros</NavLink>
                  <NavLink href="#publicaciones">Publicaciones</NavLink>
                </nav>
              </div>

              <div className="flex items-center gap-2">
                {auth.user ? (
                  <Link
                    href={route('dashboard')}
                    className="inline-flex items-center justify-center rounded-lg bg-black px-4 py-2 text-sm font-medium text-white hover:bg-gray-900 transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                  >
                    Ir al Dashboard
                  </Link>
                ) : (
                  <>
                    <Link
                      href={route('login')}
                      className="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-50 transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                      Iniciar Sesión
                    </Link>

                    <Link
                      href={route('register')}
                      className="inline-flex items-center justify-center rounded-lg bg-black px-4 py-2 text-sm font-medium text-white hover:bg-gray-900 transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                      Crear cuenta
                    </Link>
                  </>
                )}
              </div>
            </div>
          </div>
        </header>

        <main id="top" className="flex-1">
          <div className="w-full bg-gradient-to-b from-blue-600 to-blue-700">
            <div className="mx-auto max-w-6xl px-4 py-14">
              <div className="max-w-3xl">
                <p className="text-xs font-semibold tracking-wider text-white/80 uppercase">
                  Plataforma de intercambio por horas
                </p>

                <h1 className="mt-3 text-4xl font-bold text-white">Banco de tiempo</h1>

                <p className="mt-4 text-base text-white/90 leading-relaxed">
                  Conecta ofertas y demandas de servicios/productos usando horas como moneda de intercambio.
                </p>

                <div className="mt-8 flex flex-col sm:flex-row gap-3">
                  <Link
                    href={primaryCta}
                    className="inline-flex items-center justify-center rounded-lg bg-black px-5 py-3 text-sm font-medium text-white hover:bg-gray-900 transition focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 focus:ring-offset-blue-700"
                  >
                    {primaryLabel}
                  </Link>

                  <Link
                    href={secondaryCta}
                    className="inline-flex items-center justify-center rounded-lg border border-white/25 bg-white/10 px-5 py-3 text-sm font-medium text-white hover:bg-white/15 transition focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 focus:ring-offset-blue-700"
                  >
                    {secondaryLabel}
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <div className="mx-auto max-w-6xl px-4 py-10 space-y-6">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <Section id="nosotros" title="Nosotros">
                <p className="text-sm text-gray-700 leading-relaxed">
                  Banco de tiempo es una plataforma pensada para facilitar el intercambio de servicios y productos
                  utilizando horas como unidad de valor. La idea es conectar personas de forma simple, segura y ordenada.
                </p>

                <div className="mt-5">
                  <ul className="space-y-2">
                    <FeatureItem>Intercambio por horas como unidad de valor</FeatureItem>
                    <FeatureItem>Publica ofertas y demandas de forma clara</FeatureItem>
                  </ul>
                </div>
              </Section>

              <Section id="publicaciones" title="Publicaciones">
                <p className="text-sm text-gray-700 leading-relaxed">
                  Explora ofertas y demandas disponibles. Puedes filtrar por tipo y buscar por título, descripción o autor.
                </p>

                <div className="mt-5">
                  <ul className="space-y-2">
                    <FeatureItem>Búsqueda por título, descripción o autor</FeatureItem>
                    <FeatureItem>Filtros por tipo, modo y orden</FeatureItem>
                    <FeatureItem>Visualización clara y rápida de cada publicación</FeatureItem>
                  </ul>
                </div>
              </Section>
            </div>
          </div>
        </main>
        <footer className="border-t border-gray-200 bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div className="flex flex-col items-center justify-center gap-3">
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

              <div className="text-xs text-gray-500">
                Laravel v{laravelVersion} (PHP v{phpVersion})
              </div>
            </div>
          </div>
        </footer>
      </div>
    </>
  );
}