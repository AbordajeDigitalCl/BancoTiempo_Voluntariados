import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

type Item = {
  id: number;
  title: string;
  description: string;
  hours: number;
  type: 'Servicio' | 'Producto';
  mode: 'Oferta' | 'Demanda';
  author: string;
  imageUrl: string;
};

export default function ServiceView({ auth, item }: { auth: any; item: Item }) {
  return (
    <AuthenticatedLayout user={auth.user}>
      <Head title="Detalle" />

      <div className="w-full flex justify-center px-4 py-6">
        <div className="w-full max-w-md space-y-4">
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div className="p-4 flex items-center justify-between">
              <h1 className="text-lg font-semibold">Detalle</h1>
              <Link
                href="/Advertisement"
                className="text-sm text-gray-600 hover:underline"
              >
                Volver
              </Link>
            </div>

            <img
              src={item.imageUrl}
              alt={item.title}
              className="w-full h-56 object-cover"
            />

            <div className="p-4">
              <div className="flex gap-2 mb-3">
                <span className="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-700">
                  {item.hours} horas
                </span>
                <span className="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-700">
                  {item.type}
                </span>
                <span className="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-700">
                  {item.mode}
                </span>
              </div>

              <h2 className="text-base font-semibold text-gray-900">
                {item.title}
              </h2>

              <p className="mt-2 text-sm text-gray-700 whitespace-pre-wrap">
                {item.description}
              </p>

              <p className="mt-3 text-sm text-gray-600">
                Publicado por <span className="font-medium">{item.author}</span>
              </p>

              <div className="mt-5 flex gap-2">
                <button
                  type="button"
                  className="flex-1 rounded-lg border border-gray-300 bg-white py-2 text-sm hover:bg-gray-50"
                >
                  Chat
                </button>
                <button
                  type="button"
                  className="flex-1 rounded-lg bg-black text-white py-2 text-sm hover:bg-gray-800"
                >
                  Acción
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
