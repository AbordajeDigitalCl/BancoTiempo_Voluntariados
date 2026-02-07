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
    if (arr.length) return arr.slice(0, 5);
    if (item?.imageUrl) return [item.imageUrl];
    return ['https://picsum.photos/seed/demo/1200/800'];
  }, [item]);

  const [active, setActive] = useState(0);
  const [paused, setPaused] = useState(false);
  const [open, setOpen] = useState(false);
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
  }, [open, active, images.length]);

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

  return (
    <AuthenticatedLayout user={auth.user}>
      <Head title={item.title} />

      <style>{`
        @keyframes fadeIn {
          from { opacity: 0; transform: scale(0.995); }
          to { opacity: 1; transform: scale(1); }
        }
      `}</style>

      {open && (
        <div
          className="fixed inset-0 z-[60] bg-black/80 flex items-center justify-center px-3"
          onClick={() => setOpen(false)}
        >
          <div className="relative w-full max-w-5xl" onClick={(e) => e.stopPropagation()}>
            <button
              type="button"
              onClick={() => setOpen(false)}
              className="absolute -top-12 right-0 rounded-full bg-white/10 hover:bg-white/20 text-white w-10 h-10 flex items-center justify-center"
              aria-label="Cerrar"
              title="Cerrar"
            >
              ✕
            </button>

            <div className="relative rounded-xl overflow-hidden bg-black">
              <img
                key={`modal-${images[active]}`}
                src={images[active]}
                alt={`Imagen-${active}`}
                className="w-full max-h-[80vh] object-contain select-none opacity-0 animate-[fadeIn_250ms_ease-out_forwards]"
                draggable={false}
              />

              {images.length > 1 && (
                <>
                  <button
                    type="button"
                    onClick={prev}
                    className="absolute left-3 top-1/2 -translate-y-1/2 rounded-full bg-white/15 hover:bg-white/25 text-white w-10 h-10 flex items-center justify-center"
                    aria-label="Anterior"
                    title="Anterior"
                  >
                    ‹
                  </button>
                  <button
                    type="button"
                    onClick={next}
                    className="absolute right-3 top-1/2 -translate-y-1/2 rounded-full bg-white/15 hover:bg-white/25 text-white w-10 h-10 flex items-center justify-center"
                    aria-label="Siguiente"
                    title="Siguiente"
                  >
                    ›
                  </button>

                  <div className="absolute bottom-3 left-0 right-0 flex items-center justify-center gap-2">
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
          </div>
        </div>
      )}

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
                  className="w-full h-[420px] sm:h-[420px] max-sm:h-[360px] object-cover select-none opacity-0 animate-[fadeIn_250ms_ease-out_forwards]"
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

              <div className="mt-5 flex flex-wrap gap-2 justify-end">
                <Link href="/chat" className="rounded-lg border border-gray-200 px-4 py-2 text-sm hover:bg-gray-50">
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
