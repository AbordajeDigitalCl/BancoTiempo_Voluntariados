import { Head, Link } from '@inertiajs/react';

export default function AdminHome({ auth }: any) {
  const user = auth?.user ?? null;

  if (!user) {
    return (
      <>
        <Head title="Admin" />

        <div className="min-h-screen bg-gray-100 flex items-center justify-center px-4 py-8">
          <div className="w-full max-w-2xl bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h1 className="text-2xl font-semibold text-gray-900">Administrador</h1>
            <p className="mt-2 text-sm text-gray-600">Acceso a visuales del administrador.</p>

            <div className="mt-6 flex flex-col sm:flex-row gap-3">
              <Link
                href="/Admin/Login"
                className="inline-flex justify-center rounded-lg bg-black text-white px-4 py-2 text-sm hover:bg-gray-800"
              >
                Ver Login Admin
              </Link>

              <Link
                href="/Admin/Register"
                className="inline-flex justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm hover:bg-gray-50"
              >
                Ver Registro Admin
              </Link>

              <Link
                href="/"
                className="inline-flex justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm hover:bg-gray-50"
              >
                Volver
              </Link>
            </div>
          </div>
        </div>
      </>
    );
  }

  return (
    <>
      <Head title="Admin" />

      <div className="min-h-screen bg-gray-100 flex items-center justify-center px-4 py-8">
        <div className="w-full max-w-2xl bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h1 className="text-2xl font-semibold text-gray-900">Administrador</h1>
          <p className="mt-2 text-sm text-gray-600">Acceso a visuales del administrador.</p>

          <p className="mt-4 text-xs text-gray-500">
            Sesión iniciada como <span className="font-medium text-gray-700">{user?.name}</span>
          </p>

          <div className="mt-6 flex flex-col sm:flex-row gap-3">
            <Link
              href="/Advertisement"
              className="inline-flex justify-center rounded-lg bg-black text-white px-4 py-2 text-sm hover:bg-gray-800"
            >
              Ir a Publicaciones
            </Link>

            <Link
              href="/Administrar-Usuarios"
              className="inline-flex justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm hover:bg-gray-50"
            >
              Ir a Administrar Usuarios
            </Link>
          </div>
        </div>
      </div>
    </>
  );
}
