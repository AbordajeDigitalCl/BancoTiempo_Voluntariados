import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import React, { useEffect, useMemo, useRef, useState } from 'react';
import 'leaflet/dist/leaflet.css';
import * as L from 'leaflet';

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
  location_id?: number | null;
  location_lat?: number | null;
  location_lng?: number | null;
  location_address?: string | null;
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
    location_id: (item?.location_id ?? null) as number | null,
    location_lat: (item?.location_lat ?? null) as number | null,
    location_lng: (item?.location_lng ?? null) as number | null,
    location_address: (item?.location_address ?? '') as string,
  });

  const inputRef = useRef<HTMLInputElement | null>(null);
  const [imgErr, setImgErr] = useState<string>('');

  const [openMap, setOpenMap] = useState(false);
  const mapRef = useRef<L.Map | null>(null);
  const markerRef = useRef<L.Marker | null>(null);
  const mapDivRef = useRef<HTMLDivElement | null>(null);

  const [addrLoading, setAddrLoading] = useState(false);
  const [addrError, setAddrError] = useState<string>('');
  const geocodeAbortRef = useRef<AbortController | null>(null);
  const geocodeReqIdRef = useRef<number>(0);

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
      setData((prev: any) => ({ ...prev, location_address: '' }));
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
      setTimeout(() => mapRef.current?.invalidateSize(), 50);
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

        const m = L.marker([lat, lng], { icon: pinIcon, draggable: true }).addTo(map);

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
        placeAt(Number(e.latlng.lat), Number(e.latlng.lng));
      });

      mapRef.current = map;

      if (!hasLocation && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          (pos) => {
            map.setView([Number(pos.coords.latitude), Number(pos.coords.longitude)], 12);
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

    return () => window.clearTimeout(t);
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
  }, [data.location_lat, data.location_lng, openMap, pinIcon]);

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
      onSuccess: () => {},
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
              <div className="rounded-lg border border-gray-200 p-3">
                <div className="flex items-center justify-between gap-2">
                  <div>
                    <p className="text-sm font-medium text-gray-900">Ubicación (opcional)</p>
                    <p className="text-xs text-gray-600">Click para poner el pin, o arrástralo para ajustar.</p>
                  </div>

                  <button
                    type="button"
                    onClick={() => setOpenMap(true)}
                    className="rounded-lg bg-black text-white px-3 py-2 text-sm hover:bg-gray-800"
                  >
                    Editar ubicación
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

                {(errors as any)?.location_lat && <p className="text-xs text-red-500 mt-2">{String((errors as any).location_lat)}</p>}
                {(errors as any)?.location_lng && <p className="text-xs text-red-500 mt-2">{String((errors as any).location_lng)}</p>}
              </div>
              <div className="pt-2">
                <div className="flex items-center justify-between">
                  <label className="block text-xs text-gray-600">Imágenes (máx. 5)</label>
                  <span className="text-xs text-gray-500">{data.imageUrls.length}/5</span>
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
      {openMap && (
        <div className="fixed inset-0 z-50 flex items-center justify-center px-4">
          <div className="absolute inset-0 bg-black/40" onClick={closeMapModal} />

          <div className="relative w-full max-w-3xl bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div className="p-4 border-b border-gray-200 flex items-center justify-between gap-2">
              <div>
                <h2 className="text-sm font-semibold">Seleccionar ubicación</h2>
                <p className="text-xs text-gray-600">Click para poner el pin. Luego puedes arrastrarlo.</p>
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
                    <span className="text-gray-500">Ubicación:</span>{' '}
                    {addrLoading ? 'Buscando...' : data.location_address ? data.location_address : '(No disponible)'}
                    {addrError ? <div className="text-red-500 mt-1">{addrError}</div> : null}
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