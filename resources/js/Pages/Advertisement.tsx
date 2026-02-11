import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';
import { useEffect, useMemo, useRef, useState } from 'react';

type AdStatus = 'active' | 'paused' | 'finished';

type Ad = {
  id: number;
  title: string;
  description: string;
  hours: number;
  type: 'Servicio' | 'Producto';
  mode: 'Oferta' | 'Demanda';
  status?: AdStatus;

  user_id?: number | null;
  author?: string;

  imageUrl?: string | null;
  imageUrls?: string[];
  image_urls?: string[];
  created_at?: string | null;
};

function OwnerStatusPill({ status }: { status: AdStatus }) {
  const label = status === 'active' ? 'Disponible' : status === 'finished' ? 'Agotado' : 'Desactivado';
  const cls =
    status === 'active'
      ? 'bg-green-50 text-green-700 border-green-200'
      : status === 'finished'
      ? 'bg-red-50 text-red-700 border-red-200'
      : 'bg-gray-50 text-gray-700 border-gray-200';

  return (
    <span className={`inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-medium ${cls}`}>
      {label}
    </span>
  );
}

function safeParseFromUrl() {
  try {
    const sp = new URLSearchParams(window.location.search);
    return {
      q: sp.get('q') ?? '',
      type: (sp.get('type') as any) ?? 'all',
      sort: (sp.get('sort') as any) ?? 'newest',
      distance: (sp.get('distance') as any) ?? 'Cualquiera',
    };
  } catch {
    return { q: '', type: 'all', sort: 'newest', distance: 'Cualquiera' };
  }
}

function buildParams(q: string, type: string, sort: string, distance: string) {
  const params: Record<string, string> = {};
  const qq = q.trim();
  if (qq) params.q = qq;
  if (type && type !== 'all') params.type = type;
  if (sort && sort !== 'newest') params.sort = sort;
  if (distance && distance !== 'Cualquiera') params.distance = distance;
  return params;
}

export default function Advertisement({ auth, createdAds, filters }: any) {
  const [openMenuId, setOpenMenuId] = useState<number | null>(null);

  const initial = useMemo(() => {
    const fromUrl = safeParseFromUrl();
    return {
      q: (filters?.q ?? fromUrl.q ?? '') as string,
      type: (filters?.type ?? fromUrl.type ?? 'all') as 'Servicio' | 'Producto' | 'all',
      sort: (filters?.sort ?? fromUrl.sort ?? 'newest') as 'newest' | 'oldest',
      distance: (filters?.distance ?? fromUrl.distance ?? 'Cualquiera') as
        | 'Cualquiera'
        | '1'
        | '5'
        | '10'
        | '20'
        | '30'
        | '50',
    };
  }, [filters]);

  const [q, setQ] = useState<string>(typeof initial.q === 'string' ? initial.q : '');
  const [typeFilter, setTypeFilter] = useState(initial.type);
  const [sort, setSort] = useState(initial.sort);
  const [distance, setDistance] = useState(initial.distance);

  const debounceRef = useRef<number | null>(null);
  const firstRunRef = useRef(true);

  const ads: Ad[] = useMemo(() => {
    return (createdAds ?? []).map((x: any) => {
      const normalizedImageUrls: string[] = (x.imageUrls ?? x.image_urls ?? x.images ?? []) as string[];

      return {
        ...x,
        status: (x.status ?? 'active') as AdStatus,
        imageUrls: normalizedImageUrls,
        created_at: x.created_at ?? null,
        author: x.author ?? x?.user?.name ?? x?.user_name ?? x?.username ?? undefined,
        user_id: x.user_id ?? x?.user?.id ?? null,
      } as Ad;
    });
  }, [createdAds]);

  const filteredAds: Ad[] = useMemo(() => {
    const query = (q ?? '').trim().toLowerCase();
    let list = [...ads];

    if (typeFilter !== 'all') list = list.filter((a) => a.type === typeFilter);

    if (query.length) {
      list = list.filter((a) => {
        const hay = `${a.title ?? ''} ${a.description ?? ''} ${a.author ?? ''}`.toLowerCase();
        return hay.includes(query);
      });
    }

    const getTime = (x: Ad) => {
      const t = x.created_at ? Date.parse(x.created_at) : NaN;
      return Number.isFinite(t) ? t : x.id;
    };

    list.sort((a, b) => (sort === 'newest' ? getTime(b) - getTime(a) : getTime(a) - getTime(b)));
    return list;
  }, [ads, q, typeFilter, sort]);

  function isOwner(ad: Ad) {
    const myId = auth?.user?.id ?? null;
    if (myId != null && ad.user_id != null) return Number(ad.user_id) === Number(myId);
    const myName = String(auth?.user?.name ?? '');
    const author = String(ad.author ?? '');
    return !!myName && !!author && myName === author;
  }

  function getCover(ad: Ad) {
    const first = ad.imageUrls?.[0];
    return first || ad.imageUrl || 'https://picsum.photos/seed/demo/900/600';
  }

  function closeMenu() {
    setOpenMenuId(null);
  }

  function onDelete(id: number) {
    if (!confirm('¿Eliminar publicación?')) return;
    router.delete(`/Advertisement/${id}`);
  }

  function onReport(id: number) {
    router.post(`/Advertisement/${id}/report`);
  }

  function updateStatus(id: number, status: AdStatus) {
    router.patch(`/Advertisement/${id}/status`, { status }, { preserveScroll: true });
    closeMenu();
  }

  function syncToUrlAndServer(nextQ: string, nextType: string, nextSort: string, nextDistance: string) {
    const params = buildParams(nextQ, nextType, nextSort, nextDistance);

    router.get('/Advertisement', params, {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    });
  }

  useEffect(() => {
    if (firstRunRef.current) {
      firstRunRef.current = false;
      return;
    }

    if (debounceRef.current) window.clearTimeout(debounceRef.current);
    debounceRef.current = window.setTimeout(() => {
      syncToUrlAndServer(q, typeFilter, sort, distance);
    }, 350);

    return () => {
      if (debounceRef.current) window.clearTimeout(debounceRef.current);
    };
  }, [q, typeFilter, sort, distance]);

  return (
    <AuthenticatedLayout user={auth.user}>
      <Head title="Publicaciones" />

      <div className="w-full flex justify-center px-4 py-6" onClick={() => closeMenu()}>
        <div className="w-full max-w-4xl space-y-4" onClick={(e) => e.stopPropagation()}>
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
                value={q ?? ''}
                onChange={(e) => setQ(e.target.value)}
              />

              <div className="grid grid-cols-2 gap-3">
                <button
                  type="button"
                  onClick={() => setTypeFilter('Servicio')}
                  className={`rounded-lg border px-3 py-2 text-sm font-medium ${
                    typeFilter === 'Servicio'
                      ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                      : 'border-gray-200 hover:bg-gray-50'
                  }`}
                >
                  Servicio
                </button>

                <button
                  type="button"
                  onClick={() => setTypeFilter('Producto')}
                  className={`rounded-lg border px-3 py-2 text-sm font-medium ${
                    typeFilter === 'Producto'
                      ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                      : 'border-gray-200 hover:bg-gray-50'
                  }`}
                >
                  Producto
                </button>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <select
                  className="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white"
                  value={distance}
                  onChange={(e) => setDistance(e.target.value as any)}
                >
                  <option value="Cualquiera">Distancia</option>
                  <option value="1">1 km</option>
                  <option value="5">5 km</option>
                  <option value="10">10 km</option>
                  <option value="20">20 km</option>
                  <option value="30">30 km</option>
                  <option value="50">50 km</option>
                </select>

                <select
                  className="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white"
                  value={sort}
                  onChange={(e) => setSort(e.target.value as any)}
                >
                  <option value="newest">Más recientes</option>
                  <option value="oldest">Más antiguos</option>
                </select>
              </div>

              <div className="flex items-center justify-between gap-2">
                <Link href="/MyAdvertisements" className="text-sm text-gray-700 hover:underline">
                  Ir a Mis publicaciones
                </Link>

                {typeFilter !== 'all' || (q ?? '').trim() || sort !== 'newest' || distance !== 'Cualquiera' ? (
                  <button
                    type="button"
                    onClick={() => {
                      setQ('');
                      setTypeFilter('all');
                      setSort('newest');
                      setDistance('Cualquiera');
                    }}
                    className="text-sm text-gray-600 hover:underline"
                  >
                    Limpiar filtros
                  </button>
                ) : null}
              </div>

              <div className="text-xs text-gray-500">
                Mostrando {filteredAds.length} de {ads.length}
              </div>
            </div>
          </div>

          <div className="space-y-4">
            {filteredAds.length === 0 ? (
              <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-sm text-gray-600">
                No hay publicaciones con esos filtros.
              </div>
            ) : (
              filteredAds.map((ad) => {
                const cover = getCover(ad);
                const ownerStatus = (ad.status ?? 'active') as AdStatus;

                return (
                  <div key={ad.id} className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div className="flex flex-col md:flex-row">
                      <Link href={`/Advertisement/${ad.id}`} className="md:w-[320px] w-full shrink-0">
                        <img src={cover} alt={ad.title} className="w-full h-56 md:h-full object-cover" loading="lazy" />
                      </Link>

                      <div className="flex-1 p-4 relative">
                        <div className="flex items-center justify-between gap-3">
                          <div className="flex flex-wrap items-center gap-2">
                            <span className="inline-flex items-center rounded-full border border-gray-200 bg-white px-2 py-0.5 text-xs">
                              {ad.type}
                            </span>
                            <span className="inline-flex items-center rounded-full border border-gray-200 bg-white px-2 py-0.5 text-xs">
                              {ad.mode}
                            </span>
                            <OwnerStatusPill status={ownerStatus} />
                          </div>

                          <div className="relative">
                            <button
                              type="button"
                              onClick={() => setOpenMenuId(openMenuId === ad.id ? null : ad.id)}
                              className="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 hover:bg-gray-50"
                              title="Opciones"
                            >
                              <span className="text-xl leading-none">⋮</span>
                            </button>

                            {openMenuId === ad.id && (
                              <div className="absolute right-0 mt-2 w-60 rounded-xl border border-gray-200 bg-white shadow-lg overflow-hidden z-20">
                                {isOwner(ad) ? (
                                  <>
                                    <Link
                                      href={`/Advertisement/${ad.id}/edit`}
                                      className="block px-4 py-2 text-sm hover:bg-gray-50"
                                    >
                                      Editar
                                    </Link>

                                    <button
                                      type="button"
                                      onClick={() => updateStatus(ad.id, 'active')}
                                      className="w-full text-left px-4 py-2 text-sm hover:bg-gray-50"
                                    >
                                      Marcar como Disponible
                                    </button>

                                    <button
                                      type="button"
                                      onClick={() => updateStatus(ad.id, 'finished')}
                                      className="w-full text-left px-4 py-2 text-sm hover:bg-gray-50"
                                    >
                                      Marcar como Agotado
                                    </button>

                                    <button
                                      type="button"
                                      onClick={() => updateStatus(ad.id, 'paused')}
                                      className="w-full text-left px-4 py-2 text-sm hover:bg-gray-50"
                                    >
                                      Desactivar publicación
                                    </button>

                                    <div className="h-px bg-gray-100" />

                                    <button
                                      type="button"
                                      onClick={() => onDelete(ad.id)}
                                      className="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 text-red-600"
                                    >
                                      Eliminar
                                    </button>
                                  </>
                                ) : (
                                  <button
                                    type="button"
                                    onClick={() => onReport(ad.id)}
                                    className="w-full text-left px-4 py-2 text-sm hover:bg-gray-50"
                                  >
                                    Reportar publicación
                                  </button>
                                )}
                              </div>
                            )}
                          </div>
                        </div>

                        <div className="mt-3">
                          <div className="text-xs text-gray-500 flex items-center gap-2">
                            <span className="inline-flex items-center gap-1">
                              <span className="inline-block w-4 text-center">⏱</span>
                              {ad.hours} horas
                            </span>
                          </div>

                          <h2 className="mt-2 text-lg font-semibold leading-snug">{ad.title}</h2>
                          <p className="mt-1 text-sm text-gray-700 line-clamp-3">{ad.description}</p>

                          <div className="mt-2 text-sm text-gray-500">
                            Ofrecido por <span className="text-gray-700">{ad.author ?? '—'}</span>
                          </div>
                        </div>

                        <div className="mt-4 flex items-center justify-end gap-2">
                          <Link
                            href={`/Advertisement/${ad.id}`}
                            className="rounded-lg border border-gray-200 px-3 py-2 text-sm hover:bg-gray-50"
                          >
                            Ver detalle
                          </Link>

                          <button type="button" className="rounded-lg bg-black text-white px-4 py-2 text-sm hover:bg-gray-800">
                            Acción
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                );
              })
            )}
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
