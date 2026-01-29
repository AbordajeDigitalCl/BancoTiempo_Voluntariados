import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import React from 'react';

type Item = {
  id: number;
  title: string;
  description: string;
  hours: number;
  type: 'Servicio' | 'Producto';
  mode: 'Oferta' | 'Demanda';
  author: string;
  imageUrl?: string | null;
  imageUrls?: string[];
};

export default function EditarPublicacion({ auth, item }: { auth: any; item: Item | null }) {
  if (!item) {
    return (
      <AuthenticatedLayout user={auth.user}>
        <Head title="Editar publicación" />
        <div className="w-full flex justify-center px-4 py-6">
          <div className="w-full max-w-md space-y-4">
            <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
              <div className="flex items-center justify-between">
                <h1 className="text-lg font-semibold">Publicación no encontrada</h1>
                <Link href={route('myadvertisements')} className="text-sm text-gray-600 hover:underline">
                  Volver
                </Link>
              </div>
              <p className="mt-2 text-sm text-gray-600">No existe esa publicación o no está disponible.</p>
            </div>
          </div>
        </div>
      </AuthenticatedLayout>
    );
  }

  const { data, setData, patch, processing, errors } = useForm({
    title: item.title ?? '',
    description: item.description ?? '',
    type: (item.type ?? 'Servicio') as 'Servicio' | 'Producto',
    mode: (item.mode ?? 'Oferta') as 'Oferta' | 'Demanda',
    hours: String(item.hours ?? 1),
  });

  function submit(e: React.FormEvent) {
    e.preventDefault();

    patch(route('advertisement.update', item!.id));
  }

  return (
    <AuthenticatedLayout user={auth.user}>
      <Head title="Editar publicación" />

      <div className="w-full flex justify-center px-4 py-6">
        <div className="w-full max-w-md space-y-4">
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div className="flex items-center justify-between">
              <h1 className="text-lg font-semibold">Editar publicación</h1>

              <Link href={route('myadvertisements')} className="text-sm text-gray-600 hover:underline">
                Volver
              </Link>
            </div>

            <p className="mt-2 text-sm text-gray-600">Modifica los datos y guarda los cambios.</p>

            <form onSubmit={submit} className="mt-4 space-y-3">
              <div>
                <label className="block text-xs text-gray-600 mb-1">Título</label>
                <input
                  className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                  value={data.title}
                  onChange={(e) => setData('title', e.target.value)}
                />
                {errors.title && <p className="text-xs text-red-500 mt-1">{errors.title}</p>}
              </div>

              <div>
                <label className="block text-xs text-gray-600 mb-1">Descripción</label>
                <textarea
                  className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm min-h-[110px]"
                  value={data.description}
                  onChange={(e) => setData('description', e.target.value)}
                />
                {errors.description && <p className="text-xs text-red-500 mt-1">{errors.description}</p>}
              </div>

              <div className="grid grid-cols-2 gap-2">
                <div>
                  <label className="block text-xs text-gray-600 mb-1">Tipo</label>
                  <select
                    className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white"
                    value={data.type}
                    onChange={(e) => setData('type', e.target.value as any)}
                  >
                    <option value="Servicio">Servicio</option>
                    <option value="Producto">Producto</option>
                  </select>
                  {errors.type && <p className="text-xs text-red-500 mt-1">{errors.type}</p>}
                </div>

                <div>
                  <label className="block text-xs text-gray-600 mb-1">Modo</label>
                  <select
                    className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white"
                    value={data.mode}
                    onChange={(e) => setData('mode', e.target.value as any)}
                  >
                    <option value="Oferta">Oferta</option>
                    <option value="Demanda">Demanda</option>
                  </select>
                  {errors.mode && <p className="text-xs text-red-500 mt-1">{errors.mode}</p>}
                </div>
              </div>

              <div>
                <label className="block text-xs text-gray-600 mb-1">Horas</label>
                <input
                  type="number"
                  min={1}
                  className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                  value={data.hours}
                  onChange={(e) => setData('hours', e.target.value)}
                />
                {errors.hours && <p className="text-xs text-red-500 mt-1">{errors.hours}</p>}
              </div>

              <button
                type="submit"
                disabled={processing}
                className="w-full mt-2 rounded-lg bg-black text-white py-2 text-sm font-medium hover:bg-gray-800 disabled:opacity-50"
              >
                {processing ? 'Guardando...' : 'Guardar cambios'}
              </button>
            </form>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
