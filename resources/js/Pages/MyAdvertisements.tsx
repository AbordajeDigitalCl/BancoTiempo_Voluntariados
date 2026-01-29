import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function MisPublicaciones({ auth }: any) {
  return (
    <AuthenticatedLayout user={auth.user}>
      <Head title="Mis Publicaciones" />

      <div className="py-6 px-4 max-w-5xl mx-auto">
        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <div className="flex items-start justify-between gap-4">
            <div>
              <h1 className="text-xl font-semibold">Mis publicaciones</h1>
              <p className="mt-1 text-sm text-gray-600">
                Gestiona tus publicaciones (estado, edición y eliminación).
              </p>
            </div>

            <div className="flex gap-2">
              <button className="px-4 py-2 rounded-lg border border-gray-200 text-sm hover:bg-gray-50">
                Ver todas
              </button>
              <button className="px-4 py-2 rounded-lg bg-black text-white text-sm hover:bg-gray-900">
                + Crear
              </button>
            </div>
          </div>

          <div className="mt-6 text-sm text-gray-500">
            (Vista base lista para conectar con backend después)
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
