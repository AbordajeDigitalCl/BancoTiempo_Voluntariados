export type AdType = 'Servicio' | 'Producto';
export type AdMode = 'Oferta' | 'Demanda';

export interface AdvertisementItem {
  id: number;
  title: string;
  description: string;
  hours: number;
  type: AdType;
  mode: AdMode;
  author: string;
  imageUrl: string;
}

function Pill({ children }: { children: React.ReactNode }) {
  return (
    <span className="inline-flex items-center rounded-full bg-white/80 px-2 py-0.5 text-[11px] font-medium text-gray-900">
      {children}
    </span>
  );
}

export default function AdCard({ item }: { item: AdvertisementItem }) {
  return (
    <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div className="relative">
        <img src={item.imageUrl} alt={item.title} className="w-full h-44 object-cover" />

        <div className="absolute top-2 left-2 flex gap-2">
          <Pill>{item.hours} horas</Pill>
          <Pill>{item.type}</Pill>
          <Pill>{item.mode}</Pill>
        </div>

        <button
          type="button"
          className="absolute top-2 right-2 w-8 h-8 rounded-full bg-white/80 hover:bg-white flex items-center justify-center"
          aria-label="Opciones"
          title="Opciones"
        >
          ⋮
        </button>
      </div>

      <div className="p-3">
        <h2 className="text-sm font-semibold">{item.title}</h2>

        <p className="mt-1 text-xs text-gray-600 leading-relaxed line-clamp-3">
          {item.description}
        </p>

        <div className="mt-2 text-[11px] text-gray-500">
          Ofrecido por <span className="font-medium text-gray-700">{item.author}</span>
        </div>

        <div className="mt-3 flex items-center justify-between">
          <button type="button" className="text-xs font-medium text-gray-700 hover:underline">
            Ver detalle
          </button>

          <div className="flex gap-2">
            <button
              type="button"
              className="text-xs px-2 py-1 rounded-lg border border-gray-300 hover:bg-gray-50"
            >
              Chat
            </button>
            <button
              type="button"
              className="text-xs px-2 py-1 rounded-lg bg-black text-white hover:bg-gray-800"
            >
              Acción
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
