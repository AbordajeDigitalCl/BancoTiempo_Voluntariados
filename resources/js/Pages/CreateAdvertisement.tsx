import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import React, { useState } from 'react';

function fileToDataUrl(file: File): Promise<string> {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onerror = () => reject(new Error('No se pudo leer el archivo'));
    reader.onload = () => resolve(String(reader.result));
    reader.readAsDataURL(file);
  });
}

export default function CreateAdvertisement({ auth }: any) {
  const [images, setImages] = useState<string[]>([]);
  const [imgError, setImgError] = useState<string>('');

  const { data, setData, post, processing, errors } = useForm({
    title: '',
    description: '',
    type: 'Servicio' as 'Servicio' | 'Producto',
    mode: 'Oferta' as 'Oferta' | 'Demanda',
    hours: '1',
    imageUrls: [] as string[],
  });

  async function onPickFiles(e: React.ChangeEvent<HTMLInputElement>) {
    setImgError('');
    const files = Array.from(e.target.files ?? []);
    if (!files.length) return;

    const remaining = 5 - images.length;
    const sliced = files.slice(0, remaining);

    for (const f of sliced) {
      if (!f.type.startsWith('image/')) {
        setImgError('Solo se permiten imágenes.');
        e.target.value = '';
        return;
      }
      const maxBytes = 2 * 1024 * 1024;
      if (f.size > maxBytes) {
        setImgError('Una imagen supera el tamaño máximo (2MB).');
        e.target.value = '';
        return;
      }
    }

    try {
      const urls = await Promise.all(sliced.map(fileToDataUrl));
      const next = [...images, ...urls].slice(0, 5);
      setImages(next);
      setData('imageUrls', next);
    } catch {
      setImgError('No se pudieron cargar una o más imágenes.');
    } finally {
      e.target.value = '';
    }
  }

  function removeImage(idx: number) {
    const next = images.filter((_, i) => i !== idx);
    setImages(next);
    setData('imageUrls', next);
  }

  function submit(e: React.FormEvent) {
    e.preventDefault();
    post('/Advertisement', { preserveScroll: true });
  }

  const fieldError = (key: string) => (errors as any)?.[key] as string | undefined;

  return (
    <AuthenticatedLayout user={auth.user}>
      <Head title="Crear publicación" />

      <div className="w-full flex justify-center px-4 py-6">
        <div className="w-full max-w-md space-y-4">
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div className="flex items-center justify-between">
              <h1 className="text-lg font-semibold">Crear publicación</h1>
              <Link href="/Advertisement" className="text-sm text-gray-600 hover:underline">
                Volver
              </Link>
            </div>

            <p className="mt-2 text-sm text-gray-600">Completa los datos y presiona Crear.</p>

            <form onSubmit={submit} className="mt-4 space-y-3">
              <div>
                <label className="block text-xs text-gray-600 mb-1">Título</label>
                <input
                  className={
                    'w-full rounded-lg border px-3 py-2 text-sm ' +
                    (fieldError('title') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300')
                  }
                  value={data.title}
                  onChange={(e) => setData('title', e.target.value)}
                />
                {fieldError('title') && <p className="text-xs text-red-500 mt-1">{fieldError('title')}</p>}
              </div>

              <div>
                <label className="block text-xs text-gray-600 mb-1">Descripción</label>
                <textarea
                  className={
                    'w-full rounded-lg border px-3 py-2 text-sm min-h-[110px] ' +
                    (fieldError('description') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300')
                  }
                  value={data.description}
                  onChange={(e) => setData('description', e.target.value)}
                />
                {fieldError('description') && <p className="text-xs text-red-500 mt-1">{fieldError('description')}</p>}
              </div>

              <div className="grid grid-cols-2 gap-2">
                <div>
                  <label className="block text-xs text-gray-600 mb-1">Tipo</label>
                  <select
                    className={
                      'w-full rounded-lg border px-3 py-2 text-sm bg-white ' +
                      (fieldError('type') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300')
                    }
                    value={data.type}
                    onChange={(e) => setData('type', e.target.value as any)}
                  >
                    <option value="Servicio">Servicio</option>
                    <option value="Producto">Producto</option>
                  </select>
                  {fieldError('type') && <p className="text-xs text-red-500 mt-1">{fieldError('type')}</p>}
                </div>

                <div>
                  <label className="block text-xs text-gray-600 mb-1">Modo</label>
                  <select
                    className={
                      'w-full rounded-lg border px-3 py-2 text-sm bg-white ' +
                      (fieldError('mode') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300')
                    }
                    value={data.mode}
                    onChange={(e) => setData('mode', e.target.value as any)}
                  >
                    <option value="Oferta">Oferta</option>
                    <option value="Demanda">Demanda</option>
                  </select>
                  {fieldError('mode') && <p className="text-xs text-red-500 mt-1">{fieldError('mode')}</p>}
                </div>
              </div>

              <div>
                <label className="block text-xs text-gray-600 mb-1">Horas</label>
                <input
                  type="number"
                  min={1}
                  className={
                    'w-full rounded-lg border px-3 py-2 text-sm ' +
                    (fieldError('hours') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300')
                  }
                  value={data.hours}
                  onChange={(e) => setData('hours', e.target.value)}
                />
                {fieldError('hours') && <p className="text-xs text-red-500 mt-1">{fieldError('hours')}</p>}
              </div>

              <div>
                <div className="flex items-center justify-between">
                  <label className="block text-xs text-gray-600 mb-1">Fotos (opcional, máx. 5)</label>
                  <span className="text-xs text-gray-500">{images.length}/5</span>
                </div>

                {images.length > 0 && (
                  <div className="grid grid-cols-3 gap-2 mb-2">
                    {images.map((src, idx) => (
                      <div key={idx} className="relative rounded-lg overflow-hidden border border-gray-200">
                        <img src={src} alt={`Foto ${idx + 1}`} className="h-24 w-full object-cover" />
                        <button
                          type="button"
                          onClick={() => removeImage(idx)}
                          className="absolute top-1 right-1 bg-white/90 border border-gray-200 rounded-md px-2 py-1 text-xs hover:bg-white"
                          title="Quitar"
                        >
                          ✕
                        </button>
                      </div>
                    ))}
                  </div>
                )}

                <input
                  type="file"
                  multiple
                  accept="image/*"
                  onChange={onPickFiles}
                  disabled={images.length >= 5}
                  className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white"
                />

                {imgError && <p className="text-xs text-red-500 mt-1">{imgError}</p>}
                {(errors as any)?.imageUrls && <p className="text-xs text-red-500 mt-1">{(errors as any).imageUrls}</p>}
              </div>

              <button
                type="submit"
                disabled={processing}
                className="w-full mt-2 rounded-lg bg-black text-white py-2 text-sm font-medium hover:bg-gray-800 disabled:opacity-50"
              >
                {processing ? 'Creando...' : 'Crear'}
              </button>
            </form>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
