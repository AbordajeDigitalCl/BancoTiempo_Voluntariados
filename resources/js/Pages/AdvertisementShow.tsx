import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';
import { useMemo, useState } from 'react';

type AdStatus = 'active' | 'paused' | 'finished';

type Ad = {
  id: number;
  title: string;
  description: string;
  hours: number;
  type: 'Servicio' | 'Producto';
  mode: 'Oferta' | 'Demanda';
  status?: AdStatus;
  author?: string;
  imageUrl?: string | null;
  imageUrls?: string[];
};

function StatusPill({ status }: { status: AdStatus }) {
  const label = status === 'active' ? 'Activa' : status === 'paused' ? 'Pausada' : 'Finalizada';
  const cls =
    status === 'active'
      ? 'bg-green-50 text-green-700 border-green-200'
      : status === 'paused'
      ? 'bg-yellow-50 text-yellow-800 border-yellow-200'
      : 'bg-gray-50 text-gray-700 border-gray-200';

  return (
    <span className={`inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-medium ${cls}`}>
      {label}
    </span>
  );
}

export default function AdvertisementShow({ auth, item }: { auth: any; item: Ad | null }) {
  const images = useMemo<string[]>(() => {
    const arr = (item?.imageUrls ?? []).filter(Boolean);
    if (arr.length) return arr;
    if (item?.imageUrl) return [item.imageUrl];
    return ['https://picsum.photos/seed/demo/1200/800'];
  }, [item]);

  const [active, setActive] = useState(0);

  if (!item) {
    return (
      <AuthenticatedLayout user={auth.user}>
        <Head title="Publicación no encontrada" />
        <div className="w-full flex justify-center px-4 py-10">
          <div className="w-full max-w-2xl bg-white rounded-xl border border-gray-200 p-6">
            <h1 className="text-lg font-semibold">Publicación no encontrada</h1>
            <p className="mt-2 text-sm text-gray-600">No existe esa publicación.</p>
            <Link href="/Advertisement" className="inline-block mt-4 text-sm underline">
              Volver
            </Link>
          </div>
        </div>
      </AuthenticatedLayout>
    );
  }

  const isOwner = (item.author ?? '') === (auth?.user?.name ?? '');

  return (
    <AuthenticatedLayout user={auth.user}>
      <Head title={item.title} />

      <div className="w-full flex justify-center px-4 py-6">
        <div className="w-full max-w-3xl space-y-4">
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center justify-between">
            <div>
              <h1 className="text-lg font-semibold">Detalle de publicación</h1>
              <p className="text-sm text-gray-600">Revisa la información completa.</p>
            </div>

            <Link href="/Advertisement" className="text-sm text-gray-600 hover:underline">
              Volver
            </Link>
          </div>

          <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div className="relative">
              <img src={images[active]} alt="Imagen" className="w-full h-[420px] object-cover" />
              <div className="absolute top-3 left-3 flex gap-2">
                <span className="bg-white/90 rounded-full px-3 py-1 text-xs">{item.type}</span>
                <span className="bg-white/90 rounded-full px-3 py-1 text-xs">{item.mode}</span>
                <span className="bg-white/90 rounded-full px-3 py-1 text-xs">
                  <StatusPill status={(item.status ?? 'active') as AdStatus} />
                </span>
                <span className="bg-white/90 rounded-full px-3 py-1 text-xs">{item.hours} horas</span>
              </div>
            </div>

            {images.length > 1 && (
              <div className="p-3 border-t border-gray-100">
                <div className="flex gap-2 overflow-x-auto">
                  {images.map((src, idx) => (
                    <button
                      key={idx}
                      type="button"
                      onClick={() => setActive(idx)}
                      className={`rounded-lg overflow-hidden border ${idx === active ? 'border-black' : 'border-gray-200'}`}
                      title={`Foto ${idx + 1}`}
                    >
                      <img src={src} alt={`thumb-${idx}`} className="h-16 w-24 object-cover" />
                    </button>
                  ))}
                </div>
              </div>
            )}

            <div className="p-5">
              <h2 className="text-xl font-semibold text-gray-900">{item.title}</h2>
              <p className="mt-2 text-sm text-gray-700 whitespace-pre-wrap">{item.description}</p>

              <div className="mt-4 text-sm text-gray-500">
                Ofrecido por <b>{item.author || 'Usuario'}</b>
              </div>

              <div className="mt-5 flex flex-wrap gap-2 justify-end">
                <Link
                  href="/chat"
                  className="rounded-lg border border-gray-200 px-4 py-2 text-sm hover:bg-gray-50"
                >
                  Chat
                </Link>

                {isOwner ? (
                  <>
                    <button
                      type="button"
                      onClick={() => router.visit(`/Advertisement/${item.id}/edit`)}
                      className="rounded-lg bg-black px-4 py-2 text-sm text-white hover:bg-gray-900"
                    >
                      Editar
                    </button>
                    <button
                      type="button"
                      onClick={() => {
                        if (confirm('¿Eliminar publicación?')) router.delete(`/Advertisement/${item.id}`);
                      }}
                      className="rounded-lg border border-red-200 px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                    >
                      Eliminar
                    </button>
                  </>
                ) : (
                  <button
                    type="button"
                    onClick={() => {
                      if (confirm('¿Reportar esta publicación?')) router.post(`/Advertisement/${item.id}/report`);
                    }}
                    className="rounded-lg border border-red-200 px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                  >
                    Reportar
                  </button>
                )}
              </div>
            </div>
          </div>

        </div>
      </div>
    </AuthenticatedLayout>
  );
}
