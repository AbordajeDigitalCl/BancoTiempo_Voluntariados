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

export default function Advertisement({ auth, createdAds }: any) {
  const [openMenuId, setOpenMenuId] = useState<number | null>(null);


  const [q, setQ] = useState('');
  const [typeFilter, setTypeFilter] = useState<'Servicio' | 'Producto' | 'all'>('all');
  const [sort, setSort] = useState<'newest' | 'oldest'>('newest');

  const ads: Ad[] = useMemo(() => {
    return (createdAds ?? []).map((x: any) => ({
      ...x,
      status: (x.status ?? 'active') as AdStatus,
      imageUrls: (x.imageUrls ?? []) as string[],
    }));
  }, [createdAds]);

  const filteredAds: Ad[] = useMemo(() => {
    const query = q.trim().toLowerCase();

    let list = [...ads];

    // type filter
    if (typeFilter !== 'all') {
      list = list.filter((a) => a.type === typeFilter);
    }

    // search filter
    if (query.length) {
      list = list.filter((a) => {
        const hay = `${a.title ?? ''} ${a.description ?? ''} ${a.author ?? ''}`.toLowerCase();
        return hay.includes(query);
      });
    }

    // sort
    list.sort((a, b) => (sort === 'newest' ? b.id - a.id : a.id - b.id));

    return list;
  }, [ads, q, typeFilter, sort]);

  function isOwner(ad: Ad) {
    return (ad.author ?? '') === (auth?.user?.name ?? '');
  }

  function getCover(ad: Ad) {
    return ad.imageUrls?.[0] || ad.imageUrl || 'https://picsum.photos/seed/demo/900/600';
  }

  function closeMenu() {
    setOpenMenuId(null);
  }

  return (
    <AuthenticatedLayout user={auth.user}>
      <Head title="Publicaciones" />

      <div className="w-full flex justify-center px-4 py-6" onClick={() => closeMenu()}>
        <div className="w-full max-w-3xl space-y-4" onClick={(e) => e.stopPropagation()}>
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div className="flex items-start justify-between gap-3">
              <div>
                <h1 className="text-lg font-semibold">Publicaciones</h1>
                <p className="mt-1 text-sm text-gray-600">Explora ofertas y demandas cerca de ti.</p>
              </div>

              <Link
                href="/Advertisement/create"
                className="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 hover:bg-gray-50"
                title="Crear publicación"
              >
                <span className="text-xl leading-none">+</span>
              </Link>
            </div>

            <div className="mt-4 grid gap-3">
              <input
                className="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                placeholder="Buscar por título, descripción o autor"
                value={q}
                onChange={(e) => setQ(e.target.value)}
              />

              <div className="grid grid-cols-2 gap-3">
                <button
                  type="button"
                  onClick={() => setTypeFilter((prev) => (prev === 'Servicio' ? 'all' : 'Servicio'))}
                  className={`rounded-lg border py-2 text-sm font-medium hover:bg-gray-50 ${
                    typeFilter === 'Servicio' ? 'border-black' : 'border-gray-200'
                  }`}
                >
                  Servicio
                </button>

                <button
                  type="button"
                  onClick={() => setTypeFilter((prev) => (prev === 'Producto' ? 'all' : 'Producto'))}
                  className={`rounded-lg border py-2 text-sm font-medium hover:bg-gray-50 ${
                    typeFilter === 'Producto' ? 'border-black' : 'border-gray-200'
                  }`}
                >
                  Producto
                </button>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <select className="rounded-lg border border-gray-200 px-3 py-2 text-sm" defaultValue="Distancia">
                  <option>Distancia</option>
                  <option>1 km</option>
                  <option>5 km</option>
                  <option>10 km</option>
                </select>

                <select
                  className="rounded-lg border border-gray-200 px-3 py-2 text-sm"
                  value={sort === 'newest' ? 'Más recientes' : 'Más antiguas'}
                  onChange={(e) => setSort(e.target.value === 'Más antiguas' ? 'oldest' : 'newest')}
                >
                  <option>Más recientes</option>
                  <option>Más antiguas</option>
                </select>
              </div>

              <div className="text-sm text-gray-600">
                <Link href="/MyAdvertisements" className="underline hover:text-gray-900">
                  Ir a Mis publicaciones
                </Link>
              </div>

              <div className="text-xs text-gray-500">
                Mostrando <b>{filteredAds.length}</b> de <b>{ads.length}</b>
              </div>
            </div>
          </div>

          {/* Listado */}
          {filteredAds.length === 0 ? (
            <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
              <div className="text-sm text-gray-600">No hay publicaciones con esos filtros.</div>
            </div>
          ) : (
            <div className="space-y-4">
              {filteredAds.map((ad) => (
                <div key={ad.id} className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                  <div className="relative">
                    <button
                      type="button"
                      onClick={() => router.visit(`/Advertisement/${ad.id}`)}
                      className="block w-full text-left"
                      title="Ver detalle"
                    >
                      <img src={getCover(ad)} alt={ad.title} className="h-56 w-full object-cover" />
                    </button>

                    <div className="absolute top-3 left-3 flex flex-wrap gap-2">
                      <span className="bg-white/90 rounded-full px-3 py-1 text-xs">{ad.type}</span>
                      <span className="bg-white/90 rounded-full px-3 py-1 text-xs">{ad.mode}</span>
                      <span className="bg-white/90 rounded-full px-3 py-1 text-xs">
                        <StatusPill status={(ad.status ?? 'active') as AdStatus} />
                      </span>
                    </div>

                    <div className="absolute top-3 right-3">
                      <button
                        type="button"
                        onClick={(e) => {
                          e.stopPropagation();
                          setOpenMenuId(openMenuId === ad.id ? null : ad.id);
                        }}
                        className="w-10 h-10 rounded-full bg-white/90 border border-gray-200 flex items-center justify-center hover:bg-white"
                        title="Acciones"
                      >
                        ⋮
                      </button>

                      {openMenuId === ad.id && (
                        <div
                          className="absolute right-0 mt-2 w-56 rounded-xl border border-gray-200 bg-white shadow-lg overflow-hidden z-10"
                          onClick={(e) => e.stopPropagation()}
                        >
                          {isOwner(ad) ? (
                            <>
                              <button
                                type="button"
                                onClick={() => {
                                  closeMenu();
                                  router.visit(`/Advertisement/${ad.id}/edit`);
                                }}
                                className="w-full text-left px-4 py-2 text-sm hover:bg-gray-50"
                              >
                                Editar
                              </button>

                              <button
                                type="button"
                                onClick={() => {
                                  closeMenu();
                                  router.patch(`/Advertisement/${ad.id}/status`, { status: 'paused' });
                                }}
                                className="w-full text-left px-4 py-2 text-sm hover:bg-gray-50"
                              >
                                Pausar
                              </button>

                              <button
                                type="button"
                                onClick={() => {
                                  closeMenu();
                                  router.patch(`/Advertisement/${ad.id}/status`, { status: 'active' });
                                }}
                                className="w-full text-left px-4 py-2 text-sm hover:bg-gray-50"
                              >
                                Activar
                              </button>

                              <button
                                type="button"
                                onClick={() => {
                                  closeMenu();
                                  router.patch(`/Advertisement/${ad.id}/status`, { status: 'finished' });
                                }}
                                className="w-full text-left px-4 py-2 text-sm hover:bg-gray-50"
                              >
                                Finalizar
                              </button>

                              <button
                                type="button"
                                onClick={() => {
                                  closeMenu();
                                  if (confirm('¿Eliminar publicación?')) router.delete(`/Advertisement/${ad.id}`);
                                }}
                                className="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                              >
                                Eliminar
                              </button>
                            </>
                          ) : (
                            <button
                              type="button"
                              onClick={() => {
                                closeMenu();
                                if (confirm('¿Reportar esta publicación?')) router.post(`/Advertisement/${ad.id}/report`);
                              }}
                              className="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                            >
                              Reportar publicación
                            </button>
                          )}
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
                        href="/chat"
                        className="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm hover:bg-gray-50"
                      >
                        Chat
                      </Link>

                      <button
                        type="button"
                        onClick={() => router.visit(`/Advertisement/${ad.id}`)}
                        className="inline-flex items-center justify-center rounded-lg bg-black px-4 py-2 text-sm text-white hover:bg-gray-900"
                      >
                        Ver detalle
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
