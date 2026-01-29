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
  imageUrl?: string;
  createdAt?: string;
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

const url = {
  createAd: '/editarpublicacion',
  myAds: '/MisPublicaciones',
  chat: '/Mensajes',
  detail: (id: number) => `/serviceview/${id}`,
  edit: (id: number) => `/editarpublicacion/${id}`,
  status: (id: number) => `/advertisement/${id}/status`,
  destroy: (id: number) => `/advertisement/${id}`,
};


export default function Advertisement({ auth, createdAds }: any) {
  const [openMenuId, setOpenMenuId] = useState<number | null>(null);

  const ads: Ad[] = useMemo(
    () =>
      (createdAds ?? []).map((x: any) => ({
        ...x,
        status: (x.status ?? 'active') as AdStatus,
      })),
    [createdAds]
  );

  return (
    <AuthenticatedLayout user={auth.user}>
      <Head title="Publicaciones" />

      <div className="w-full flex justify-center px-4 py-6">
        <div className="w-full max-w-3xl space-y-4">
          {/* Header / filtros */}
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div className="flex items-start justify-between gap-3">
              <div>
                <h1 className="text-lg font-semibold">Publicaciones</h1>
                <p className="mt-1 text-sm text-gray-600">Explora ofertas y demandas cerca de ti.</p>
              </div>

              <Link
                href={url.createAd}
                className="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 hover:bg-gray-50"
                title="Crear publicación"
              >
                <span className="text-xl leading-none">+</span>
              </Link>
            </div>

            <div className="mt-4 grid gap-3">
              <input
                className="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                placeholder="Buscar"
              />

              <div className="grid grid-cols-2 gap-3">
                <button className="rounded-lg border border-gray-200 py-2 text-sm font-medium hover:bg-gray-50">
                  Servicio
                </button>
                <button className="rounded-lg border border-gray-200 py-2 text-sm font-medium hover:bg-gray-50">
                  Producto
                </button>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <select className="rounded-lg border border-gray-200 px-3 py-2 text-sm">
                  <option>Distancia</option>
                  <option>1 km</option>
                  <option>5 km</option>
                  <option>10 km</option>
                </select>

                <select className="rounded-lg border border-gray-200 px-3 py-2 text-sm">
                  <option>Más recientes</option>
                  <option>Más antiguas</option>
                </select>
              </div>

              <div className="text-sm text-gray-600">
                <Link href={url.myAds} className="underline hover:text-gray-900">
                  Ir a Mis publicaciones
                </Link>
              </div>
            </div>
          </div>

          {/* Listado */}
          {ads.length === 0 ? (
            <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
              <div className="text-sm text-gray-600">
                Aún no hay publicaciones. Crea una con el botón <b>+</b>.
              </div>
            </div>
          ) : (
            <div className="space-y-4">
              {ads.map((ad) => (
                <div key={ad.id} className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                  <div className="relative">
                    <img
                      src={ad.imageUrl || 'https://picsum.photos/seed/demo/900/600'}
                      alt={ad.title}
                      className="h-56 w-full object-cover"
                    />

                    <div className="absolute top-3 left-3 flex gap-2 flex-wrap">
                      <span className="bg-white/90 rounded-full px-3 py-1 text-xs">Hace 8 horas</span>
                      <span className="bg-white/90 rounded-full px-3 py-1 text-xs">{ad.type}</span>
                      <span className="bg-white/90 rounded-full px-3 py-1 text-xs">{ad.mode}</span>
                      <span className="bg-white/90 rounded-full px-3 py-1 text-xs">
                        <StatusPill status={(ad.status ?? 'active') as AdStatus} />
                      </span>
                    </div>

                    <div className="absolute top-3 right-3">
                      <button
                        onClick={() => setOpenMenuId(openMenuId === ad.id ? null : ad.id)}
                        className="w-10 h-10 rounded-full bg-white/90 border border-gray-200 flex items-center justify-center hover:bg-white"
                        title="Acción"
                      >
                        ⋮
                      </button>

                      {openMenuId === ad.id && (
                        <div className="absolute right-0 mt-2 w-52 rounded-xl border border-gray-200 bg-white shadow-lg overflow-hidden z-10">
                          <Link href={url.detail(ad.id)} className="block px-4 py-2 text-sm hover:bg-gray-50">
                            Ver detalle
                          </Link>

                          <Link href={url.edit(ad.id)} className="block px-4 py-2 text-sm hover:bg-gray-50">
                            Editar (solo dueño)
                          </Link>

                          <button
                            onClick={() => router.patch(url.status(ad.id), { status: 'paused' })}
                            className="w-full text-left px-4 py-2 text-sm hover:bg-gray-50"
                          >
                            Pausar (solo dueño)
                          </button>

                          <button
                            onClick={() => router.patch(url.status(ad.id), { status: 'active' })}
                            className="w-full text-left px-4 py-2 text-sm hover:bg-gray-50"
                          >
                            Activar (solo dueño)
                          </button>

                          <button
                            onClick={() => router.patch(url.status(ad.id), { status: 'finished' })}
                            className="w-full text-left px-4 py-2 text-sm hover:bg-gray-50"
                          >
                            Finalizar (solo dueño)
                          </button>

                          <button
                            onClick={() => router.delete(url.destroy(ad.id))}
                            className="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                          >
                            Eliminar (solo dueño)
                          </button>
                        </div>
                      )}
                    </div>
                  </div>

                  <div className="p-4">
                    <div className="font-semibold text-gray-900">{ad.title}</div>
                    <div className="mt-1 text-sm text-gray-600">{ad.description}</div>
                    <div className="mt-2 text-sm text-gray-500">Ofrecido por {ad.author || 'Usuario'}</div>

                    <div className="mt-4 flex items-center justify-end gap-3">
                      <Link
                        href={url.chat}
                        className="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm hover:bg-gray-50"
                      >
                        Chat
                      </Link>
                      <button className="inline-flex items-center justify-center rounded-lg bg-black px-4 py-2 text-sm text-white hover:bg-gray-900">
                        Acción
                      </button>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
