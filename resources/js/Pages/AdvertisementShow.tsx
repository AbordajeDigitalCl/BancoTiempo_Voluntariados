import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';
import { useEffect, useMemo, useRef, useState } from 'react';
import { createPortal } from 'react-dom';
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
  user_id:number;
  author?: string;
  imageUrl?: string | null;
  imageUrls?: string[];
  location_id?: number | null;
  location_lat?: number | null;
  location_lng?: number | null;
  location_address?: string | null;
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
    const arr = (item?.imageUrls ?? []).filter((x): x is string => typeof x === 'string' && x.trim() !== '');
    if (arr.length) return arr.slice(0, 5);
    if (item?.imageUrl && typeof item.imageUrl === 'string' && item.imageUrl.trim() !== '') return [item.imageUrl];
    return ['https://picsum.photos/seed/demo/1200/800'];
  }, [item]);

  const [active, setActive] = useState(0);
  const [paused, setPaused] = useState(false);
  const [open, setOpen] = useState(false);
  const [mounted, setMounted] = useState(false);
  const intervalRef = useRef<number | null>(null);

  const isOwner = (item?.author ?? '') === (auth?.user?.name ?? '');

  function goTo(idx: number) {
    if (!images.length) return;
    const safe = ((idx % images.length) + images.length) % images.length;
    setActive(safe);
  }

  function next() {
    goTo(active + 1);
  }

  function prev() {
    goTo(active - 1);
  }

  useEffect(() => {
    setMounted(true);
  }, []);

  useEffect(() => {
    if (intervalRef.current) window.clearInterval(intervalRef.current);
    if (!images.length || images.length === 1 || paused || open) return;

    intervalRef.current = window.setInterval(() => {
      setActive((i) => (i + 1) % images.length);
    }, 4500);

    return () => {
      if (intervalRef.current) window.clearInterval(intervalRef.current);
    };
  }, [images.length, paused, open]);

  useEffect(() => {
    if (active >= images.length) setActive(0);
  }, [images.length, active]);

  useEffect(() => {
    function onKey(e: KeyboardEvent) {
      if (!open) return;
      if (e.key === 'Escape') setOpen(false);
      if (e.key === 'ArrowRight') goTo(active + 1);
      if (e.key === 'ArrowLeft') goTo(active - 1);
    }
    window.addEventListener('keydown', onKey);
    return () => window.removeEventListener('keydown', onKey);
  }, [open, active]);

  useEffect(() => {
    if (!open) return;

    const previousOverflow = document.body.style.overflow;
    document.body.style.overflow = 'hidden';

    return () => {
      document.body.style.overflow = previousOverflow;
    };
  }, [open]);

  const mapDivRef = useRef<HTMLDivElement | null>(null);
  const mapRef = useRef<L.Map | null>(null);
  const markerRef = useRef<L.Marker | null>(null);

  const hasLocation = typeof item?.location_lat === 'number' && typeof item?.location_lng === 'number';

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
          box-shadow: 0 2px 6px rgba(0,0,0,0.35);
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

  const crearConversacion = (usuarioId: number) => {
    router.post('/administrar-usuarios/crear-conversacion', {
        usuario_id: usuarioId,
    });
  };

  useEffect(() => {
    if (!item) return;
    if (!hasLocation) return;
    if (open) return;

    const el = mapDivRef.current;
    if (!el) return;

    if (mapRef.current) {
      mapRef.current.off();
      mapRef.current.remove();
      mapRef.current = null;
      markerRef.current = null;
    }

    const lat = Number(item.location_lat);
    const lng = Number(item.location_lng);

    const map = L.map(el, {
      zoomControl: true,
      dragging: false,
      scrollWheelZoom: false,
      doubleClickZoom: false,
      boxZoom: false,
      keyboard: false,
      touchZoom: false,
    }).setView([lat, lng], 14);
    (map as any).tap?.disable?.();

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    const m = L.marker([lat, lng], { icon: pinIcon, draggable: false }).addTo(map);

    mapRef.current = map;
    markerRef.current = m;

    setTimeout(() => map.invalidateSize(), 60);

    return () => {
      map.off();
      map.remove();
      mapRef.current = null;
      markerRef.current = null;
    };
  }, [item?.id, hasLocation, pinIcon, open]);

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

  const modal =
    mounted && open
      ? createPortal(
          <div
            className="fixed inset-0 z-[99999] bg-black/95 flex items-center justify-center p-4"
            onClick={() => setOpen(false)}
          >
            <div
              className="relative max-w-[95vw] max-h-[95vh] flex items-center justify-center"
              onClick={(e) => e.stopPropagation()}
            >
              <button
                type="button"
                onClick={() => setOpen(false)}
                className="absolute -top-12 right-0 z-20 rounded-full bg-white/10 hover:bg-white/20 text-white w-10 h-10 flex items-center justify-center"
                aria-label="Cerrar"
                title="Cerrar"
              >
                ✕
              </button>

              <img
                key={`modal-${images[active]}`}
                src={images[active]}
                alt={`Imagen-${active}`}
                className="block max-w-[95vw] max-h-[85vh] w-auto h-auto object-contain rounded-xl select-none"
                draggable={false}
              />

              {images.length > 1 && (
                <>
                  <button
                    type="button"
                    onClick={prev}
                    className="absolute left-3 top-1/2 -translate-y-1/2 rounded-full bg-white/15 hover:bg-white/25 text-white w-10 h-10 flex items-center justify-center z-20"
                    aria-label="Anterior"
                    title="Anterior"
                  >
                    ‹
                  </button>

                  <button
                    type="button"
                    onClick={next}
                    className="absolute right-3 top-1/2 -translate-y-1/2 rounded-full bg-white/15 hover:bg-white/25 text-white w-10 h-10 flex items-center justify-center z-20"
                    aria-label="Siguiente"
                    title="Siguiente"
                  >
                    ›
                  </button>

                  <div className="absolute -bottom-8 left-0 right-0 flex items-center justify-center gap-2 z-20">
                    {images.map((_, i) => (
                      <button
                        key={`modal-dot-${i}`}
                        type="button"
                        onClick={() => goTo(i)}
                        className={`h-2.5 rounded-full transition-all ${
                          i === active ? 'w-6 bg-white/80' : 'w-2.5 bg-white/30 hover:bg-white/45'
                        }`}
                        aria-label={`Ir a imagen ${i + 1}`}
                        title={`Imagen ${i + 1}`}
                      />
                    ))}
                  </div>
                </>
              )}
            </div>
          </div>,
          document.body
        )
      : null;

  return (
    <AuthenticatedLayout user={auth.user}>
      <Head title={item.title} />

      {modal}

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
            <div
              className="relative group"
              onMouseEnter={() => setPaused(true)}
              onMouseLeave={() => setPaused(false)}
            >
              <button
                type="button"
                onClick={() => setOpen(true)}
                className="block w-full text-left"
                aria-label="Abrir imagen en grande"
                title="Ver imagen"
              >
                <img
                  key={images[active]}
                  src={images[active]}
                  alt={`Imagen-${active}`}
                  className="w-full h-[420px] sm:h-[420px] max-sm:h-[360px] object-cover select-none"
                  draggable={false}
                />
              </button>

              <div className="absolute top-3 left-3 flex gap-2 flex-wrap">
                <span className="bg-white/90 rounded-full px-3 py-1 text-xs">{item.type}</span>
                <span className="bg-white/90 rounded-full px-3 py-1 text-xs">{item.mode}</span>
                <span className="bg-white/90 rounded-full px-3 py-1 text-xs">
                  <StatusPill status={(item.status ?? 'active') as AdStatus} />
                </span>
                <span className="bg-white/90 rounded-full px-3 py-1 text-xs">{item.hours} horas</span>
              </div>

              {images.length > 1 && (
                <>
                  <button
                    type="button"
                    onClick={prev}
                    className="absolute left-3 top-1/2 -translate-y-1/2 rounded-full bg-white/80 hover:bg-white border border-gray-200 shadow-sm w-9 h-9 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                    aria-label="Anterior"
                    title="Anterior"
                  >
                    <span className="text-lg leading-none">‹</span>
                  </button>

                  <button
                    type="button"
                    onClick={next}
                    className="absolute right-3 top-1/2 -translate-y-1/2 rounded-full bg-white/80 hover:bg-white border border-gray-200 shadow-sm w-9 h-9 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                    aria-label="Siguiente"
                    title="Siguiente"
                  >
                    <span className="text-lg leading-none">›</span>
                  </button>

                  <div className="absolute bottom-3 left-0 right-0 flex items-center justify-center gap-2">
                    {images.map((_, i) => (
                      <button
                        key={i}
                        type="button"
                        onClick={() => goTo(i)}
                        className={`h-2.5 rounded-full transition-all ${
                          i === active ? 'w-6 bg-black/70' : 'w-2.5 bg-black/25 hover:bg-black/40'
                        }`}
                        aria-label={`Ir a imagen ${i + 1}`}
                        title={`Imagen ${i + 1}`}
                      />
                    ))}
                  </div>
                </>
              )}
            </div>

            {images.length > 1 && (
              <div className="p-3 border-t border-gray-100">
                <div className="flex gap-2 overflow-x-auto">
                  {images.map((src, idx) => (
                    <button
                      key={idx}
                      type="button"
                      onClick={() => goTo(idx)}
                      className={`rounded-lg overflow-hidden border ${
                        idx === active ? 'border-black' : 'border-gray-200'
                      }`}
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

              {!open && (
                <div className="mt-5">
                  <div className="text-sm font-medium text-gray-900">Ubicación</div>

                  {hasLocation ? (
                    <>
                      <div className="mt-1 text-sm text-gray-700">
                        {item.location_address ? item.location_address : 'Ubicación seleccionada'}
                      </div>
                      <div
                        ref={mapDivRef}
                        className="mt-3 w-full h-[220px] rounded-lg border border-gray-200 overflow-hidden"
                      />
                    </>
                  ) : (
                    <div className="mt-1 text-sm text-gray-500">(Sin ubicación)</div>
                  )}
                </div>
              )}

              <div className="mt-5 flex flex-wrap gap-2 justify-end">
                {/*
                <Link href="/chat" className="rounded-lg border border-gray-200 px-4 py-2 text-sm hover:bg-gray-50">
                  Chat
                </Link>
                */}
                <button
                  type="button"
                  onClick={() => crearConversacion(item.user_id)}
                  className="rounded-lg border border-gray-200 px-4 py-2 text-sm hover:bg-gray-50"
                >
                  Chat
                </button>
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