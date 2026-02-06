import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { useMemo, useRef, useState } from 'react';

type Item = {
  id: number;
  title: string;
  description: string;
  hours: number;
  type: 'Servicio' | 'Producto';
  mode: 'Oferta' | 'Demanda';
  author?: string;
  imageUrl?: string | null;
  imageUrls?: string[];
};

function fileToDataURL(file: File) {
  return new Promise<string>((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = () => resolve(String(reader.result));
    reader.onerror = reject;
    reader.readAsDataURL(file);
  });
}

export default function EditarPublicacion({ auth, item }: { auth: any; item: Item | null }) {
  const initialImages = useMemo<string[]>(() => {
    if (!item) return [];
    const arr = item.imageUrls && Array.isArray(item.imageUrls) ? item.imageUrls : [];
    if (arr.length) return arr.slice(0, 5);
    if (item.imageUrl) return [item.imageUrl];
    return [];
  }, [item]);

  const { data, setData, patch, processing, errors } = useForm({
    title: item?.title ?? '',
    description: item?.description ?? '',
    type: (item?.type ?? 'Servicio') as 'Servicio' | 'Producto',
    mode: (item?.mode ?? 'Oferta') as 'Oferta' | 'Demanda',
    hours: String(item?.hours ?? 1),
    imageUrls: initialImages as string[],
  });

  const inputRef = useRef<HTMLInputElement | null>(null);
  const [imgErr, setImgErr] = useState<string>('');

  function removeImage(idx: number) {
    const next = data.imageUrls.filter((_, i) => i !== idx);
    setData('imageUrls', next);
  }

  async function onPickFiles(e: React.ChangeEvent<HTMLInputElement>) {
    setImgErr('');
    const files = Array.from(e.target.files ?? []);
    if (!files.length) return;

    const remaining = 5 - data.imageUrls.length;
    if (remaining <= 0) {
      setImgErr('Máximo 5 imágenes.');
      if (inputRef.current) inputRef.current.value = '';
      return;
    }

    const take = files.slice(0, remaining);

    try {
      const urls = await Promise.all(take.map(fileToDataURL));
      setData('imageUrls', [...data.imageUrls, ...urls].slice(0, 5));
    } catch {
      setImgErr('No se pudo leer una de las imágenes.');
    } finally {
      if (inputRef.current) inputRef.current.value = '';
    }
  }

  function submit(e: React.FormEvent) {
    e.preventDefault();
    if (!item?.id) return;

    patch(`/Advertisement/${item.id}`, {
      preserveScroll: true,
      onSuccess: () => {
      },
    });
  }

  if (!item) {
    return (
      <AuthenticatedLayout user={auth.user}>
        <Head title="Editar publicación" />
        <div className="w-full flex justify-center px-4 py-10">
          <div className="w-full max-w-md bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h1 className="text-lg font-semibold">Publicación no encontrada</h1>
            <p className="mt-2 text-sm text-gray-600">No existe esa publicación.</p>
            <Link href="/MyAdvertisements" className="inline-block mt-4 text-sm text-gray-700 hover:underline">
              Volver a Mis publicaciones
            </Link>
          </div>
        </div>
      </AuthenticatedLayout>
    );
  }

  return (
    <AuthenticatedLayout user={auth.user}>
      <Head title="Editar publicación" />

      <div className="w-full flex justify-center px-4 py-6">
        <div className="w-full max-w-md space-y-4">
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div className="flex items-center justify-between">
              <h1 className="text-lg font-semibold">Editar publicación</h1>

              <Link href="/MyAdvertisements" className="text-sm text-gray-600 hover:underline">
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
                {errors.title && <p className="text-xs text-red-500">{errors.title}</p>}
              </div>

              <div>
                <label className="block text-xs text-gray-600 mb-1">Descripción</label>
                <textarea
                  className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm min-h-[110px]"
                  value={data.description}
                  onChange={(e) => setData('description', e.target.value)}
                />
                {errors.description && <p className="text-xs text-red-500">{errors.description}</p>}
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
                  {errors.type && <p className="text-xs text-red-500">{errors.type}</p>}
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
                  {errors.mode && <p className="text-xs text-red-500">{errors.mode}</p>}
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
                {errors.hours && <p className="text-xs text-red-500">{errors.hours}</p>}
              </div>

              {/* IMÁGENES */}
              <div className="pt-2">
                <div className="flex items-center justify-between">
                  <label className="block text-xs text-gray-600">Imágenes (máx. 5)</label>
                  <span className="text-xs text-gray-500">
                    {data.imageUrls.length}/5
                  </span>
                </div>

                {data.imageUrls.length > 0 && (
                  <div className="mt-2 grid grid-cols-3 gap-2">
                    {data.imageUrls.map((src, idx) => (
                      <div key={idx} className="relative rounded-lg overflow-hidden border border-gray-200">
                        <img src={src} alt={`img-${idx}`} className="w-full h-20 object-cover" />
                        <button
                          type="button"
                          onClick={() => removeImage(idx)}
                          className="absolute top-1 right-1 rounded-md bg-white/90 border border-gray-200 px-2 py-1 text-[11px] hover:bg-white"
                        >
                          Quitar
                        </button>
                      </div>
                    ))}
                  </div>
                )}

                <div className="mt-2">
                  <input
                    ref={inputRef}
                    type="file"
                    accept="image/*"
                    multiple
                    onChange={onPickFiles}
                    disabled={data.imageUrls.length >= 5}
                    className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white"
                  />
                  {imgErr ? <p className="text-xs text-red-500 mt-1">{imgErr}</p> : null}
                  {errors.imageUrls && <p className="text-xs text-red-500 mt-1">{String(errors.imageUrls)}</p>}
                </div>
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
