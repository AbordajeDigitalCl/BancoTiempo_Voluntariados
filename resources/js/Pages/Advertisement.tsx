import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';
import {useCallback, useEffect, useMemo, useRef, useState } from 'react';
import 'leaflet/dist/leaflet.css';
import * as L from 'leaflet';

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

  location_lat?: number | null;
  location_lng?: number | null;
};

type Coordinates = {
  latitude: number,
  longitude: number
};

type LaravelPaginator<T> = {
  data: T[];
  current_page: number;
  last_page: number;
  prev_page_url:string | null;
  next_page_url: string | null;
  total: number;
};

type AdvertisementProps = {
  auth: any,
  createdAds: LaravelPaginator<Ad>,
  filters: any,
  isAdmin: any
};

type LocationStatus =
    | 'checking'
    | 'granted'
    | 'denied'
    | 'error'
    | 'unavailable';

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

function MiniMap({ lat, lng }: { lat: number; lng: number }) {
  const divRef = useRef<HTMLDivElement | null>(null);
  const mapRef = useRef<L.Map | null>(null);
  const markerRef = useRef<L.CircleMarker | null>(null);

  useEffect(() => {
    const el = divRef.current;
    if (!el) return;

    if (!mapRef.current) {
      const map = L.map(el, {
        zoomControl: false,
        attributionControl: false,
        dragging: false,
        scrollWheelZoom: false,
        doubleClickZoom: false,
        boxZoom: false,
        keyboard: false,
        touchZoom: false,
      }).setView([lat, lng], 13);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
      }).addTo(map);

      markerRef.current = L.circleMarker([lat, lng], {
        radius: 6,
        weight: 2,
        fillOpacity: 0.85,
      }).addTo(map);

      mapRef.current = map;
      setTimeout(() => map.invalidateSize(), 0);
    } else {
      mapRef.current.setView([lat, lng], 13);
      if (markerRef.current) markerRef.current.setLatLng([lat, lng]);
      setTimeout(() => mapRef.current?.invalidateSize(), 0);
    }
  }, [lat, lng]);

  return <div ref={divRef} className="w-full h-28 rounded-lg border border-gray-200 overflow-hidden" />;
}

export default function Advertisement({ auth, createdAds, filters, isAdmin }: AdvertisementProps) {
  const [openMenuId, setOpenMenuId] = useState<number | null>(null);
  const [userLocation, setUserLocation] = useState<Coordinates | null>(null);
  const [locationError, setLocationError] = useState<string | null>(null);  


  const [locationStatus, setLocationStatus] =
      useState<LocationStatus>('checking');


  const getUserLocation = useCallback(() => {

    if (!navigator.geolocation) {
        setLocationStatus('unavailable');
        setLocationError('Tu navegador no admite la geolocalización.');
        return;
    }

    setLocationStatus('checking');
    setLocationError(null);

    navigator.geolocation.getCurrentPosition(

        (position) => {

            const { latitude, longitude } = position.coords;

            setUserLocation({
                latitude,
                longitude
            });

            setLocationStatus('granted');
            setLocationError(null);
        },

        (error) => {

            setUserLocation(null);

            if (error.code === error.PERMISSION_DENIED) {

                setLocationStatus('denied');

                setLocationError(
                    'El acceso a la ubicación está bloqueado. ' +
                    'Actívalo desde la configuración de tu navegador.'
                );

            } else if (error.code === error.POSITION_UNAVAILABLE) {

                setLocationStatus('error');

                setLocationError(
                    'No se pudo determinar tu ubicación.'
                );

            } else if (error.code === error.TIMEOUT) {

                setLocationStatus('error');

                setLocationError(
                    'La solicitud tardó demasiado. Inténtalo de nuevo.'
                );
            }

        },

        {
            timeout: 10000,
            maximumAge: 60000
        }
    );

  }, []);

  useEffect(() => {

    let permissionStatus: PermissionStatus | null = null;
    let cancelled = false;

    if (!navigator.geolocation) {

        setLocationStatus('unavailable');

        setLocationError(
            'Tu navegador no admite la geolocalización.'
        );

        return;
    }

    const handlePermissionChange = () => {

        if (!permissionStatus) return;

        if (permissionStatus.state === 'granted') {

            getUserLocation();

        } else if (permissionStatus.state === 'denied') {

            setUserLocation(null);
            setLocationStatus('denied');

            setLocationError(
                'El acceso a la ubicación está bloqueado. ' +
                'Actívalo desde la configuración del navegador.'
            );

        }

    };

    navigator.permissions.query({
        name: 'geolocation'
    }).then((permission) => {

        if (cancelled) return;

        permissionStatus = permission;

        permissionStatus.addEventListener(
            'change',
            handlePermissionChange
        );

        if (permission.state === 'denied') {

            setLocationStatus('denied');

        } else {

            // Si está concedido, obtenemos las coordenadas.
            // Si está pendiente, el navegador puede mostrar
            // su solicitud de autorización.
            getUserLocation();

        }

    }).catch(() => {

        // Si Permissions API no está disponible o falla,
        // intentamos obtener la ubicación directamente.
        if (!cancelled) {
            getUserLocation();
        }

    });

    return () => {

        cancelled = true;

        permissionStatus?.removeEventListener(
            'change',
            handlePermissionChange
        );

    };

  }, [getUserLocation]);

  const initial = useMemo(() => {
    const fromUrl = safeParseFromUrl();
    return {
      q: (filters?.q ?? fromUrl.q ?? '') as string,
      type: (filters?.type ?? fromUrl.type ?? 'all') as 'Servicio' | 'Producto' | 'all',
      sort: (filters?.sort ?? fromUrl.sort ?? 'newest') as 'newest' | 'oldest',
      distance: (filters?.distance ?? fromUrl.distance ?? 'Cualquiera') as
        | 'Cualquiera'
        | '0'
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
    return (createdAds?.data ?? []).map((x: any) => {
      const normalizedImageUrls: string[] = (x.imageUrls ?? x.image_urls ?? x.images ?? []) as string[];

      return {
        ...x,
        status: (x.status ?? 'active') as AdStatus,
        imageUrls: normalizedImageUrls,
        created_at: x.created_at ?? null,
        author: x.author ?? x?.user?.name ?? x?.user_name ?? x?.username ?? undefined,
        user_id: x.user_id ?? x?.user?.id ?? null,

        location_lat:
          typeof x.location_lat === 'number'
            ? x.location_lat
            : typeof x.location_lat === 'string'
            ? Number(x.location_lat)
            : null,
        location_lng:
          typeof x.location_lng === 'number'
            ? x.location_lng
            : typeof x.location_lng === 'string'
            ? Number(x.location_lng)
            : null,
      } as Ad;
    });
  }, [createdAds]);
//cambiar useMemo!!
const filteredAds: Ad[] = useMemo(() => {
  // El servidor ya aplica todos los filtros (tipo, búsqueda, distancia, orden)
  // Este componente solo renderiza lo que llega de la BD
  return ads;
}, [ads]);

  function isOwner(ad: Ad) {
    if (isAdmin) return true;

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
  /*
  * Se modificó este método para que envié también las coordenadas del usuario, obtenidas del navegador.
  * Anteriormente solo enviaba los datos de los filtros como params
  */
  function syncToUrlAndServer(nextQ: string, nextType: string, nextSort: string, nextDistance: string) {
    const filterParams = buildParams(nextQ, nextType, nextSort, nextDistance);
    const params = {
      ...filterParams,
      userLocation
    };



    router.get('/Advertisement', params, {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    });
  }

  function cancelarDebounce() {
    if (debounceRef.current !== null) {
        window.clearTimeout(debounceRef.current);
        debounceRef.current = null;
    }
  }

  function cambiarPagina(url: string | null) {
      if (!url) return;

      // Cancelamos cualquier petición de filtros que todavía
      // esté esperando a que termine el debounce.
      cancelarDebounce();

      // Navegamos a la página solicitada.
      router.get(url, {}, {
          preserveState: true,
          preserveScroll: true,
      });
  }

  useEffect(() => {
    getUserLocation()
  }, []);

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
  }, [q, typeFilter, sort, distance, userLocation]);

  return (
    <AuthenticatedLayout user={auth.user}>
      <Head title="Publicaciones" />
      {locationStatus === 'denied' && (
          <div className="rounded-lg bg-yellow-50 border border-yellow-200 p-4 text-yellow-800">

              <p>
                  Para utilizar correctamente las funciones de ubicación
                  de esta aplicación, necesitas permitir el acceso a tu ubicación.
              </p>

              <p className="mt-2 text-sm">
                  Puedes activarlo desde la configuración de permisos
                  del sitio en tu navegador.
              </p>

              <button
                  type="button"
                  onClick={getUserLocation}
                  className="mt-3 rounded-lg bg-yellow-600 px-4 py-2 text-sm text-white hover:bg-yellow-700"
              >
                  Ya habilité la ubicación. Volver a intentar
              </button>

          </div>
      )}
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
    onClick={() => setTypeFilter(typeFilter === 'Servicio' ? 'all' : 'Servicio')}
    className={`w-full px-3 py-2 rounded-lg border text-sm font-medium transition-colors ${
      typeFilter === 'Servicio'
        ? 'bg-indigo-600 text-white border-indigo-600'
        : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'
    }`}
  >
    Servicio
  </button>
  <button
    type="button"
    onClick={() => setTypeFilter(typeFilter === 'Producto' ? 'all' : 'Producto')}
    className={`w-full px-3 py-2 rounded-lg border text-sm font-medium transition-colors ${
      typeFilter === 'Producto'
        ? 'bg-indigo-600 text-white border-indigo-600'
        : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'
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
                  <option value="0">Online</option>
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

             <button
              type="button"
              onClick={() => syncToUrlAndServer(q, typeFilter, sort, distance)}
              className="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
              >
                Buscar
              </button>
            </div>



              <div className="text-xs text-gray-500">
                Mostrando {filteredAds.length} de {ads.length}
              </div>
            </div>
          </div>
          <>
          <div className="space-y-4">
            {filteredAds.length === 0 ? (
              <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-sm text-gray-600">
                No hay publicaciones con esos filtros.
              </div>
            ) : (
              filteredAds.map((ad) => {
                const cover = getCover(ad);
                const ownerStatus = (ad.status ?? 'active') as AdStatus;

                const hasLoc =
                  typeof ad.location_lat === 'number' &&
                  Number.isFinite(ad.location_lat) &&
                  typeof ad.location_lng === 'number' &&
                  Number.isFinite(ad.location_lng);

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

                          {hasLoc && (
                            <div className="mt-3">
                              <div className="text-xs text-gray-600 mb-2">Ubicación</div>
                              <MiniMap lat={Number(ad.location_lat)} lng={Number(ad.location_lng)} />
                            </div>
                          )}
                        </div>

                        <div className="mt-4 flex items-center justify-end gap-2">
                          <Link
                            href={`/Advertisement/${ad.id}`}
                            className="rounded-lg border border-gray-200 px-3 py-2 text-sm hover:bg-gray-50"
                          >
                            Ver detalle
                          </Link>
                        </div>
                      </div>
                    </div>
                  </div>
                );
              })
            )}
          </div>
          <div className="flex items-center justify-between rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
              {/*
              <button
                onClick={() => {
                  if (createdAds.prev_page_url) {
                    router.get(createdAds.prev_page_url);
                  }
                }}
                className={`rounded-lg border px-3 py-2 text-sm hover:bg-gray-50 ${!createdAds.prev_page_url ? 'invisible' : ''}`}
              >
                Anterior
              </button>
              */}
              <button
              onClick={() => cambiarPagina(createdAds.prev_page_url)}
              className={`rounded-lg border px-3 py-2 text-sm hover:bg-gray-50 ${!createdAds.prev_page_url ? 'invisible' : ''}`}
              >
                Anterior
              </button>
              <span className="text-sm text-gray-600">
                Página {createdAds.current_page} de {createdAds.last_page}
              </span>
              {/*
              <button
               
                onClick={() => {
                  if (createdAds.next_page_url) {
                    router.get(createdAds.next_page_url);
                  }
                }}
                
                className={`rounded-lg border px-3 py-2 text-sm hover:bg-gray-50 ${!createdAds.next_page_url ? 'invisible' : ''}`}
              >
                Siguiente
              </button>
              */}
              <button
              onClick={() => cambiarPagina(createdAds.next_page_url)}
              className={`rounded-lg border px-3 py-2 text-sm hover:bg-gray-50 ${!createdAds.next_page_url ? 'invisible' : ''}`}
              >
                Siguiente
              </button>
          </div>
          </>
        </div>
        
      </div>
    </AuthenticatedLayout>
  );
}