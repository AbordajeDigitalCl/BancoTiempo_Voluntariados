import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function AdminHome({ auth }: any) {
  return (
    <AuthenticatedLayout user={auth.user}>
      <Head title="Administración" />

      <div className="w-full flex justify-center px-4 py-6">
        <div className="w-full max-w-5xl space-y-4">
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <h1 className="text-lg font-semibold">Administración</h1>
            <p className="mt-1 text-sm text-gray-600">Accesos a vistas de administrador.</p>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <Link
              href="/Administrar-Usuarios"
              className="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:bg-gray-50 transition"
            >
              <div className="text-sm text-gray-500">Módulo</div>
              <div className="mt-1 text-base font-semibold text-gray-900">Administrar usuarios</div>
              <div className="mt-2 text-sm text-gray-600">
                Ver lista, buscar, filtrar y entrar a perfil/editar.
              </div>
            </Link>

            <Link
              href="/verperfil-Admin/1"
              className="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:bg-gray-50 transition"
            >
              <div className="text-sm text-gray-500">Vista</div>
              <div className="mt-1 text-base font-semibold text-gray-900">Ver perfil (Admin)</div>
              <div className="mt-2 text-sm text-gray-600">
                Vista de perfil con publicaciones/historial (mock por ahora).
              </div>
            </Link>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
