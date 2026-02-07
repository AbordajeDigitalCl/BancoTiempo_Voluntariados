import { Link, router } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';

export type AdvertisementItem = {
  id: number;
  title: string;
  description: string;
  hours: number;
  type: 'Servicio' | 'Producto';
  mode: 'Oferta' | 'Demanda';
  author: string;
  imageUrl: string;
};

function Pill({ children }: { children: React.ReactNode }) {
  return (
    <span className="rounded-full bg-white/90 px-2 py-1 text-xs text-gray-800 shadow-sm">
      {children}
    </span>
  );
}

export default function AdCard({ item }: { item: AdvertisementItem }) {
  const [open, setOpen] = useState(false);
  const menuRef = useRef<HTMLDivElement | null>(null);

  useEffect(() => {
    function onDocClick(e: MouseEvent) {
      if (!menuRef.current) return;
      if (!menuRef.current.contains(e.target as Node)) setOpen(false);
    }
    document.addEventListener('mousedown', onDocClick);
    return () => document.removeEventListener('mousedown', onDocClick);
  }, []);

  function goDetail() {
    setOpen(false);
    router.visit('/ServiceView', {
      method: 'get',
      data: { item },
      preserveScroll: true,
    });
  }

  function goEdit() {
    setOpen(false);
    router.visit('/EditarPublicacion', {
      method: 'get',
      data: { item },
      preserveScroll: true,
    });
  }

  function destroy() {
    setOpen(false);
    router.delete(`/Advertisement/${item.id}`, {
      preserveScroll: true,
    });
  }

  return (
    <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div className="relative">
        <button type="button" onClick={goDetail} className="block w-full text-left">
          <img src={item.imageUrl} alt={item.title} className="w-full h-44 object-cover" />
        </button>

        <div className="absolute top-2 left-2 flex gap-2">
          <Pill>{item.hours} horas</Pill>
          <Pill>{item.type}</Pill>
          <Pill>{item.mode}</Pill>
        </div>

        <div className="absolute top-2 right-2" ref={menuRef}>
          <button
            type="button"
            onClick={() => setOpen((v) => !v)}
            className="w-8 h-8 rounded-full bg-white/90 flex items-center justify-center shadow"
            aria-label="Opciones"
          >
            ⋮
          </button>

          {open && (
            <div className="absolute right-0 mt-2 w-44 rounded-lg border border-gray-200 bg-white shadow-lg overflow-hidden z-20">
              <button
                type="button"
                onClick={goDetail}
                className="w-full px-3 py-2 text-sm text-left hover:bg-gray-50"
              >
                Ver detalle
              </button>

              <button
                type="button"
                onClick={goEdit}
                className="w-full px-3 py-2 text-sm text-left hover:bg-gray-50"
              >
                Editar
              </button>

              <button
                type="button"
                onClick={destroy}
                className="w-full px-3 py-2 text-sm text-left hover:bg-gray-50 text-red-600"
              >
                Eliminar
              </button>
            </div>
          )}
        </div>
      </div>

      <button type="button" onClick={goDetail} className="w-full text-left">
        <div className="p-4">
          <h3 className="text-sm font-semibold text-gray-900">{item.title}</h3>

          <p className="mt-1 text-xs text-gray-600 line-clamp-2">{item.description}</p>

          <p className="mt-2 text-xs text-gray-500">
            Ofrecido por <span className="font-medium">{item.author}</span>
          </p>

          <div className="mt-3 flex items-center justify-end gap-2">
            <button
              type="button"
              onClick={(e) => {
                e.stopPropagation();
              }}
              className="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs hover:bg-gray-50"
            >
              Chat
            </button>

            <button
              type="button"
              onClick={(e) => {
                e.stopPropagation();
              }}
              className="rounded-lg bg-black text-white px-3 py-1.5 text-xs hover:bg-gray-800"
            >
              Acción
            </button>
          </div>
        </div>
      </button>
    </div>
  );
}
