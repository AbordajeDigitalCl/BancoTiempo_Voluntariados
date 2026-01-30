import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function VerPerfil({ auth }: any) {
  return (
    <AuthenticatedLayout user={auth.user}>
      <Head title="Ver Perfil" />

      <div className="w-full flex justify-center px-4 py-6">
        <div className="w-full max-w-5xl space-y-4">
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <h1 className="text-lg font-semibold">Ver Perfil</h1>
            <p className="mt-1 text-sm text-gray-600">
              Vista base (lista para conectar al backend después).
            </p>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
