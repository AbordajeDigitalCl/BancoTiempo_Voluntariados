import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

function Card({
  title,
  subtitle,
  icon,
  children,
}: {
  title: string;
  subtitle: string;
  icon: React.ReactNode;
  children: React.ReactNode;
}) {
  return (
    <div className="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col">
      <div className="flex items-start gap-3">
        <div className="h-10 w-10 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-center">
          {icon}
        </div>
        <div className="min-w-0">
          <h2 className="text-base font-semibold text-gray-900">{title}</h2>
          <p className="mt-1 text-sm text-gray-600">{subtitle}</p>
        </div>
      </div>

      <div className="mt-5 flex-1">{children}</div>
    </div>
  );
}

function IconGrid() {
  return (
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" className="text-gray-700">
      <path
        d="M4 4h7v7H4V4Zm9 0h7v7h-7V4ZM4 13h7v7H4v-7Zm9 0h7v7h-7v-7Z"
        stroke="currentColor"
        strokeWidth="1.8"
      />
    </svg>
  );
}

function IconUser() {
  return (
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" className="text-gray-700">
      <path
        d="M20 21a8 8 0 1 0-16 0"
        stroke="currentColor"
        strokeWidth="1.8"
        strokeLinecap="round"
      />
      <path
        d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Z"
        stroke="currentColor"
        strokeWidth="1.8"
      />
    </svg>
  );
}

function IconInfo() {
  return (
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" className="text-gray-700">
      <path
        d="M12 22A10 10 0 1 0 2 12a10 10 0 0 0 10 10Z"
        stroke="currentColor"
        strokeWidth="1.8"
      />
      <path d="M12 10v7" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" />
      <path d="M12 7h.01" stroke="currentColor" strokeWidth="2.6" strokeLinecap="round" />
    </svg>
  );
}

export default function Dashboard({ auth }: any) {
  return (
    <AuthenticatedLayout user={auth.user}>
      <Head title="Banco de tiempo" />

      <div className="w-full">
        <div className="w-full bg-gradient-to-b from-blue-600 to-blue-700">
          <div className="mx-auto max-w-6xl px-4 py-12">
            <div className="max-w-3xl">
              <p className="text-xs font-semibold tracking-wider text-white/80 uppercase">
                Plataforma de intercambio por horas
              </p>

              <h1 className="mt-3 text-4xl font-bold text-white">Banco de tiempo</h1>

              <p className="mt-4 text-base text-white/90 leading-relaxed">
                Conecta ofertas y demandas de servicios/productos usando horas como moneda de intercambio.
              </p>
            </div>
          </div>
        </div>

        {/* Cards */}
        <div className="mx-auto max-w-6xl px-4 py-10">
          <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <Card
              title="Publicaciones"
              subtitle="Explora ofertas y demandas disponibles."
              icon={<IconGrid />}
            >
              <ul className="space-y-2 text-sm text-gray-600">
                <li className="flex gap-2">
                  <span className="mt-2 h-1.5 w-1.5 rounded-full bg-gray-300" />
                  Búsqueda por título, descripción o autor
                </li>
                <li className="flex gap-2">
                  <span className="mt-2 h-1.5 w-1.5 rounded-full bg-gray-300" />
                  Filtros por tipo, modo y orden
                </li>
              </ul>

              <div className="mt-6 flex flex-col gap-2">
                <Link
                  href={route('advertisement')}
                  className="inline-flex items-center justify-center rounded-lg bg-black px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-900"
                >
                  Ver publicaciones
                </Link>

                <Link
                  href="/Advertisement/create"
                  className="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-800 hover:bg-gray-50"
                >
                  Crear publicación
                </Link>
              </div>
            </Card>

            <Card
              title="Mis publicaciones"
              subtitle="Gestiona lo que tú has creado."
              icon={<IconUser />}
            >
              <ul className="space-y-2 text-sm text-gray-600">
                <li className="flex gap-2">
                  <span className="mt-2 h-1.5 w-1.5 rounded-full bg-gray-300" />
                  Editar, eliminar y ver estado
                </li>
                <li className="flex gap-2">
                  <span className="mt-2 h-1.5 w-1.5 rounded-full bg-gray-300" />
                  Acceso directo a tus publicaciones
                </li>
              </ul>

              <div className="mt-6 flex flex-col gap-2">
                <Link
                  href={route('myadvertisements')}
                  className="inline-flex items-center justify-center rounded-lg bg-black px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-900"
                >
                  Ver mis publicaciones
                </Link>

                <Link
                  href="/Advertisement/create"
                  className="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-800 hover:bg-gray-50"
                >
                  Crear nueva
                </Link>
              </div>
            </Card>

            <Card
              title="Nosotros"
              subtitle="Información institucional (pendiente)."
              icon={<IconInfo />}
            >
              <div className="rounded-lg border border-dashed border-gray-300 p-5 text-sm text-gray-500">
                (Sección “Nosotros” pendiente de contenido)
              </div>

              <div className="mt-6 grid grid-cols-2 gap-2">
                <Link
                  href={route('verperfil')}
                  className="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium hover:bg-gray-50"
                >
                  Ver perfil
                </Link>

                <Link
                  href={route('chat')}
                  className="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium hover:bg-gray-50"
                >
                  Mensajes
                </Link>
              </div>

              <p className="mt-4 text-xs text-gray-500">
                Sesión iniciada como <span className="font-medium text-gray-700">{auth.user?.name}</span>.
              </p>
            </Card>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
