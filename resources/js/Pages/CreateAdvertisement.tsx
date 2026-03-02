import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import React, { useEffect, useMemo, useRef, useState } from 'react';
import 'leaflet/dist/leaflet.css';
import * as L from 'leaflet';

function fileToDataUrl(file: File): Promise<string> {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onerror = () => reject(new Error('No se pudo leer el archivo'));
    reader.onload = () => resolve(String(reader.result));
    reader.readAsDataURL(file);
  });
}

export default function CreateAdvertisement({ auth, users }: any) {
  const [images, setImages] = useState<string[]>([]);
  const [imgError, setImgError] = useState<string>('');
  const isAdmin = Array.isArray(users) && users.length > 0;

  const { data, setData, post, processing, errors } = useForm({
    title: '',
    description: '',
    type: 'Servicio' as 'Servicio' | 'Producto',
    mode: 'Oferta' as 'Oferta' | 'Demanda',
    hours: '1',
    imageUrls: [] as string[],
    user_id: null as number | null,

    location_id: null as number | null,
    location_lat: null as number | null,
    location_lng: null as number | null,
    location_address: '' as string,
  });

  const [openMap, setOpenMap] = useState(false);
  const mapRef = useRef<L.Map | null>(null);
  const markerRef = useRef<L.Marker | null>(null);
  const mapDivRef = useRef<HTMLDivElement | null>(null);

  const [addrLoading, setAddrLoading] = useState(false);
  const [addrError, setAddrError] = useState<string>('');
  const geocodeAbortRef = useRef<AbortController | null>(null);
  const geocodeReqIdRef = useRef<number>(0);

  const fieldError = (key: string) => (errors as any)?.[key] as string | undefined;

  const hasLocation = useMemo(() => {
    return typeof data.location_lat === 'number' && typeof data.location_lng === 'number';
  }, [data.location_lat, data.location_lng]);

  const pinIcon = useMemo(() => {
    return L.divIcon({
      className: '',
      html: `
        <div style="
          position: relative;
          width: 30px;
          height: 30px;
          background: #000;
          border-radius: 50% 50% 50% 0;
          transform: rotate(-45deg);
          box-shadow: 0 2px 6px rgba(0,0,0,0.4);
        ">
          <div style="
            width: 14px;
            height: 14px;
            background: white;
            border-radius: 50%;
            position: absolute;
            top: 8px;
            left: 8px;
          "></div>
        </div>
      `,
      iconSize: [30, 30],
      iconAnchor: [15, 30],
    });
  }, []);

  function closeMapModal() {
    setOpenMap(false);

    if (geocodeAbortRef.current) {
      geocodeAbortRef.current.abort();
      geocodeAbortRef.current = null;
    }

    if (mapRef.current) {
      mapRef.current.off();
      mapRef.current.remove();
      mapRef.current = null;
    }

    markerRef.current = null;
  }

  async function fetchAddress(lat: number, lng: number) {
    try {
      setAddrError('');
      setAddrLoading(true);

      if (geocodeAbortRef.current) geocodeAbortRef.current.abort();
      const controller = new AbortController();
      geocodeAbortRef.current = controller;

      const reqId = ++geocodeReqIdRef.current;

      const res = await fetch(`/geo/reverse?lat=${encodeURIComponent(lat)}&lng=${encodeURIComponent(lng)}`, {
        method: 'GET',
        signal: controller.signal,
        headers: { Accept: 'application/json' },
      });

      if (!res.ok) throw new Error('bad response');
      const json: any = await res.json();

      if (reqId !== geocodeReqIdRef.current) return;

      setData((prev: any) => ({
        ...prev,
        location_address: String(json?.display_name ?? ''),
      }));
    } catch (e: any) {
      if (e?.name === 'AbortError') return;
      setAddrError('No se pudo obtener la dirección (intenta de nuevo).');
      setData((prev: any) => ({
        ...prev,
        location_address: '',
      }));
    } finally {
      setAddrLoading(false);
    }
  }

  function applyLocation(lat: number, lng: number) {
    const nextId = data.location_id ?? Date.now();

    setData((prev: any) => ({
      ...prev,
      location_id: nextId,
      location_lat: lat,
      location_lng: lng,
    }));

    fetchAddress(lat, lng);
  }

  useEffect(() => {
    if (!openMap) return;

    const el = mapDivRef.current;
    if (!el) return;

    if (mapRef.current) {
      setTimeout(() => {
        mapRef.current?.invalidateSize();
      }, 50);
      return;
    }

    const initialLat = typeof data.location_lat === 'number' ? data.location_lat : 0;
    const initialLng = typeof data.location_lng === 'number' ? data.location_lng : 0;
    const initialZoom = hasLocation ? 13 : 2;

    const t = window.setTimeout(() => {
      const map = L.map(el, ({ zoomControl: true } as any)).setView([initialLat, initialLng], initialZoom);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
      }).addTo(map);

      const ensureMarker = (lat: number, lng: number) => {
        if (markerRef.current) {
          markerRef.current.setLatLng([lat, lng]);
          return markerRef.current;
        }

        const m = L.marker([lat, lng], {
          icon: pinIcon,
          draggable: true,
        }).addTo(map);

        m.on('dragend', () => {
          const p = m.getLatLng();
          const nlat = Number(p.lat);
          const nlng = Number(p.lng);
          ensureMarker(nlat, nlng);
          applyLocation(nlat, nlng);
        });

        markerRef.current = m;
        return m;
      };

      const placeAt = (lat: number, lng: number) => {
        ensureMarker(lat, lng);
        applyLocation(lat, lng);
        map.setView([lat, lng], Math.max(map.getZoom(), 13));
      };

      map.on('click', (e: any) => {
        if (!e?.latlng) return;
        const lat = Number(e.latlng.lat);
        const lng = Number(e.latlng.lng);
        placeAt(lat, lng);
      });

      mapRef.current = map;

      if (!hasLocation && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          (pos) => {
            const lat = Number(pos.coords.latitude);
            const lng = Number(pos.coords.longitude);
            map.setView([lat, lng], 12);
          },
          () => {},
          { enableHighAccuracy: true, timeout: 8000 }
        );
      }

      if (hasLocation) {
        ensureMarker(initialLat, initialLng);
        if (!data.location_address) fetchAddress(initialLat, initialLng);
      }

      map.invalidateSize();
    }, 50);

    return () => {
      window.clearTimeout(t);
    };
  }, [openMap]);

  useEffect(() => {
    if (!openMap) return;
    if (!mapRef.current) return;

    const latIsNum = typeof data.location_lat === 'number';
    const lngIsNum = typeof data.location_lng === 'number';

    if (latIsNum && lngIsNum) {
      const lat = Number(data.location_lat);
      const lng = Number(data.location_lng);

      if (markerRef.current) {
        markerRef.current.setLatLng([lat, lng]);
      } else {
        const m = L.marker([lat, lng], { icon: pinIcon, draggable: true }).addTo(mapRef.current);
        m.on('dragend', () => {
          const p = m.getLatLng();
          applyLocation(Number(p.lat), Number(p.lng));
        });
        markerRef.current = m;
      }
      return;
    }

    if (data.location_lat === null && data.location_lng === null) {
      if (markerRef.current) {
        markerRef.current.remove();
        markerRef.current = null;
      }
    }
  }, [data.location_lat, data.location_lng, openMap]);

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
      setData((prev: any) => ({ ...prev, imageUrls: next }));
    } catch {
      setImgError('No se pudieron cargar una o más imágenes.');
    } finally {
      e.target.value = '';
    }
  }

  function removeImage(idx: number) {
    const next = images.filter((_, i) => i !== idx);
    setImages(next);
    setData((prev: any) => ({ ...prev, imageUrls: next }));
  }

  function submit(e: React.FormEvent) {
    e.preventDefault();
    post('/Advertisement', { preserveScroll: true });
  }

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
              {isAdmin && (
                <div>
                  <select
                    className={
                      'w-full rounded-lg border px-3 py-2 text-sm bg-white ' +
                      (fieldError('user_id') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300')
                    }
                    value={data.user_id ?? ''}
                    onChange={(e) => {
                      const v = e.target.value;
                      setData((prev: any) => ({ ...prev, user_id: v ? Number(v) : null }));
                    }}
                  >
                    <option value="">(Yo / por defecto)</option>
                    {(users ?? []).map((u: any) => (
                      <option key={u.id} value={u.id}>
                        {u.name}
                      </option>
                    ))}
                  </select>

                  {fieldError('user_id') && <p className="text-xs text-red-500 mt-1">{fieldError('user_id')}</p>}

                  <p className="mt-1 text-xs text-gray-500">Asignar publicacion a otro usuario.</p>
                </div>
              )}

              <div>
                <label className="block text-xs text-gray-600 mb-1">Título</label>
                <input
                  className={
                    'w-full rounded-lg border px-3 py-2 text-sm ' +
                    (fieldError('title') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300')
                  }
                  value={data.title}
                  onChange={(e) => setData((prev: any) => ({ ...prev, title: e.target.value }))}
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
                  onChange={(e) => setData((prev: any) => ({ ...prev, description: e.target.value }))}
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
                    onChange={(e) => setData((prev: any) => ({ ...prev, type: e.target.value as any }))}
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
                    onChange={(e) => setData((prev: any) => ({ ...prev, mode: e.target.value as any }))}
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
                  onChange={(e) => setData((prev: any) => ({ ...prev, hours: e.target.value }))}
                />
                {fieldError('hours') && <p className="text-xs text-red-500 mt-1">{fieldError('hours')}</p>}
              </div>
              <div className="rounded-lg border border-gray-200 p-3">
                <div className="flex items-center justify-between gap-2">
                  <div>
                    <p className="text-sm font-medium text-gray-900">Ubicación</p>
                    <p className="text-xs text-gray-600">Selecciona el lugar donde se realizará el servicio o producto.</p>
                  </div>

                  <button
                    type="button"
                    onClick={() => setOpenMap(true)}
                    className="rounded-lg bg-black text-white px-3 py-2 text-sm hover:bg-gray-800"
                  >
                    Seleccionar ubicación
                  </button>
                </div>

                {hasLocation ? (
                  <div className="mt-3 flex items-start justify-between gap-2">
                    <div className="text-xs text-gray-700">
                      <span className="text-gray-500">Ubicación:</span>{' '}
                      {addrLoading ? 'Buscando...' : data.location_address ? data.location_address : '(No disponible)'}
                      {addrError ? <div className="text-red-500 mt-1">{addrError}</div> : null}
                    </div>

                    <button
                      type="button"
                      onClick={() => {
                        setData((prev: any) => ({
                          ...prev,
                          location_id: null,
                          location_lat: null,
                          location_lng: null,
                          location_address: '',
                        }));
                        setAddrError('');
                      }}
                      className="text-sm text-gray-600 hover:underline"
                    >
                      Quitar
                    </button>
                  </div>
                ) : (
                  <p className="mt-3 text-xs text-gray-500">(No seleccionada)</p>
                )}

                {(fieldError('location_lat') || fieldError('location_lng')) && (
                  <p className="text-xs text-red-500 mt-2">{fieldError('location_lat') || fieldError('location_lng')}</p>
                )}
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

      {openMap && (
        <div className="fixed inset-0 z-50 flex items-center justify-center px-4">
          <div className="absolute inset-0 bg-black/40" onClick={closeMapModal} />

          <div className="relative w-full max-w-3xl bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div className="p-4 border-b border-gray-200 flex items-center justify-between gap-2">
              <div>
                <h2 className="text-sm font-semibold">Seleccionar ubicación</h2>
                <p className="text-xs text-gray-600">Click para establecer la ubicación, para mas precisión puedes arrastrar el marcador.</p>
              </div>

              <div className="flex items-center gap-2">
                <button
                  type="button"
                  onClick={() => {
                    setData((prev: any) => ({
                      ...prev,
                      location_id: null,
                      location_lat: null,
                      location_lng: null,
                      location_address: '',
                    }));
                    setAddrError('');
                  }}
                  className="rounded-lg border border-gray-200 px-3 py-2 text-sm hover:bg-gray-50"
                >
                  Limpiar
                </button>

                <button
                  type="button"
                  onClick={closeMapModal}
                  className="rounded-lg bg-black text-white px-3 py-2 text-sm hover:bg-gray-800"
                >
                  Listo
                </button>
              </div>
            </div>

            <div className="p-4">
              <div ref={mapDivRef} className="w-full h-[420px] rounded-lg border border-gray-200 overflow-hidden" />

              <div className="mt-3 text-xs text-gray-700">
                {hasLocation ? (
                  <>
                    <span className="text-gray-500">ID:</span> {data.location_id ?? '—'}{' '}
                    <span className="text-gray-400">|</span>{' '}
                    <span className="text-gray-500">Coordenadas:</span>{' '}
                    {Number(data.location_lat).toFixed(6)}, {Number(data.location_lng).toFixed(6)}
                    <div className="mt-1">
                      <span className="text-gray-500">Dirección:</span>{' '}
                      {addrLoading ? 'Buscando...' : data.location_address ? data.location_address : '(No disponible)'}
                      {addrError ? <div className="text-red-500 mt-1">{addrError}</div> : null}
                    </div>
                  </>
                ) : (
                  <span className="text-gray-500">Aún no seleccionas una ubicación.</span>
                )}
              </div>
            </div>
          </div>
        </div>
      )}
    </AuthenticatedLayout>
  );
}