import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import React from 'react';

type FormData = {
  title: string;
  description: string;
  type: 'Servicio' | 'Producto';
  mode: 'Oferta' | 'Demanda';
  hours: string;
  image: File | null;
};

export default function CreateAdvertisement({ auth }: any) {
  const { data, setData, post, processing, errors, reset } = useForm<FormData>({
    title: '',
    description: '',
    type: 'Servicio',
    mode: 'Oferta',
    hours: '',
    image: null,
  });

  function submit(e: React.FormEvent) {
    e.preventDefault();
    post('/Advertisement', {
      forceFormData: true,
      preserveScroll: true,
      onSuccess: () => reset('title', 'description', 'type', 'mode', 'hours', 'image'),
    });
  }

  return (
    <AuthenticatedLayout user={auth.user}>
      <Head title="Publicar" />

      <div className="w-full flex justify-center px-4 py-6">
        <div className="w-full max-w-md space-y-4">
          <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div className="flex items-center justify-between">
              <h1 className="text-lg font-semibold">Publicar</h1>

              <Link href="/Advertisement" className="text-sm text-gray-600 hover:underline">
                Volver
              </Link>
            </div>

            <p className="mt-2 text-sm text-gray-600">
              Completa los datos para publicar una oferta o demanda.
            </p>

            <form onSubmit={submit} className="mt-4 space-y-3">
              <div>
                <label className="block text-xs text-gray-600 mb-1">Título</label>
                <input
                  className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                  placeholder="Ej: Servicio de gasfiter"
                  value={data.title}
                  onChange={(e) => setData('title', e.target.value)}
                />
                {errors.title && <p className="mt-1 text-xs text-red-500">{errors.title}</p>}
              </div>

              <div>
                <label className="block text-xs text-gray-600 mb-1">Descripción</label>
                <textarea
                  className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm min-h-[110px]"
                  placeholder="Describe el servicio o producto..."
                  value={data.description}
                  onChange={(e) => setData('description', e.target.value)}
                />
                {errors.description && (
                  <p className="mt-1 text-xs text-red-500">{errors.description}</p>
                )}
              </div>

              <div className="grid grid-cols-2 gap-2">
                <div>
                  <label className="block text-xs text-gray-600 mb-1">Tipo</label>
                  <select
                    className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white"
                    value={data.type}
                    onChange={(e) => setData('type', e.target.value as FormData['type'])}
                  >
                    <option value="Servicio">Servicio</option>
                    <option value="Producto">Producto</option>
                  </select>
                  {errors.type && <p className="mt-1 text-xs text-red-500">{errors.type}</p>}
                </div>

                <div>
                  <label className="block text-xs text-gray-600 mb-1">Modo</label>
                  <select
                    className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white"
                    value={data.mode}
                    onChange={(e) => setData('mode', e.target.value as FormData['mode'])}
                  >
                    <option value="Oferta">Oferta</option>
                    <option value="Demanda">Demanda</option>
                  </select>
                  {errors.mode && <p className="mt-1 text-xs text-red-500">{errors.mode}</p>}
                </div>
              </div>

              <div className="grid grid-cols-2 gap-2">
                <div>
                  <label className="block text-xs text-gray-600 mb-1">Horas</label>
                  <input
                    type="number"
                    min={1}
                    className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                    placeholder="Ej: 3"
                    value={data.hours}
                    onChange={(e) => setData('hours', e.target.value)}
                  />
                  {errors.hours && <p className="mt-1 text-xs text-red-500">{errors.hours}</p>}
                </div>

                <div>
                  <label className="block text-xs text-gray-600 mb-1">Imagen</label>
                  <input
                    type="file"
                    accept="image/png,image/jpeg,image/webp"
                    className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white"
                    onChange={(e) => setData('image', e.target.files?.[0] ?? null)}
                  />
                  {errors.image && <p className="mt-1 text-xs text-red-500">{errors.image}</p>}
                </div>
              </div>

              <button
                type="submit"
                disabled={processing}
                className="w-full mt-2 rounded-lg bg-black text-white py-2 text-sm font-medium hover:bg-gray-800 disabled:opacity-50"
              >
                {processing ? 'Publicando…' : 'Publicar'}
              </button>
            </form>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
