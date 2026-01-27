import AuthenticatedLayout from '../Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { useMemo, useState } from 'react';

import FiltersBar, {
  DistanceFilter,
  OrderFilter,
  TypeFilter,
} from '../Components/Advertisement/FiltersBar';

import AdCard, { AdvertisementItem } from '../Components/Advertisement/AdCard';

type Props = {
  auth: { user: any };
};

const seed: AdvertisementItem[] = [
  {
    id: 1,
    title: 'Servicio Albañilería básico',
    description:
      'Hago cambios de enchufes y arreglos menores. Coordinación por chat. Disponibilidad fines de semana.',
    hours: 8,
    type: 'Servicio',
    mode: 'Oferta',
    author: 'Pedro Juarez',
    imageUrl: 'https://picsum.photos/seed/albanileria/900/600',
  },
  {
    id: 2,
    title: 'Servicio afinación y balanceo',
    description:
      'Afinación completa + balanceo. Retiro en comuna cercana. Se agenda por mensaje.',
    hours: 5,
    type: 'Servicio',
    mode: 'Oferta',
    author: 'Camila R.',
    imageUrl: 'https://picsum.photos/seed/afinacion/900/600',
  },
  {
    id: 3,
    title: 'Ayuda con cambio de tarifa',
    description: 'Te acompaño y asesoro para cambiar tarifa/plan.',
    hours: 23,
    type: 'Servicio',
    mode: 'Demanda',
    author: 'Dominique X.',
    imageUrl: 'https://picsum.photos/seed/tarifa/900/600',
  },
  {
    id: 4,
    title: 'Juguete pollo chillón',
    description: 'Producto en buen estado. Entrego cerca de metro.',
    hours: 3,
    type: 'Producto',
    mode: 'Oferta',
    author: 'Juan Pérez',
    imageUrl: 'https://picsum.photos/seed/pollo/900/600',
  },
];

export default function Advertisement({ auth }: Props) {
  const [query, setQuery] = useState('');
  const [type, setType] = useState<TypeFilter>('Servicio');
  const [distance, setDistance] = useState<DistanceFilter>('Cualquiera');
  const [order, setOrder] = useState<OrderFilter>('Más recientes');

  const filtered = useMemo(() => {
    const q = query.trim().toLowerCase();

    return seed.filter((it) => {
      const matchesQuery =
        q.length === 0 ||
        it.title.toLowerCase().includes(q) ||
        it.description.toLowerCase().includes(q) ||
        it.author.toLowerCase().includes(q);

      const matchesType =
        type === 'Servicio' ? it.type === 'Servicio' : it.type === 'Producto';

      return matchesQuery && matchesType;
    });
  }, [query, type, distance, order]);

  return (
    <AuthenticatedLayout user={auth.user}>
      <Head title="Publicaciones" />

      <div className="w-full flex justify-center px-4 py-6">
        <div className="w-full max-w-md space-y-4">
          {/* Header card */}
          <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div className="flex items-center justify-between">
              <h1 className="text-lg font-semibold">Publicaciones</h1>

              <button
                type="button"
                className="w-9 h-9 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-50"
                onClick={() => console.log('Crear publicación')}
                aria-label="Crear publicación"
              >
                +
              </button>
            </div>

            <p className="mt-2 text-sm text-gray-600">
              Explora ofertas y demandas cerca de ti.
            </p>

            <input
              value={query}
              onChange={(e) => setQuery(e.target.value)}
              placeholder="Buscar"
              className="w-full mt-3 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-200"
            />

            <div className="mt-3">
              <FiltersBar
                type={type}
                onTypeChange={setType}
                distance={distance}
                onDistanceChange={setDistance}
                order={order}
                onOrderChange={setOrder}
              />
            </div>
          </div>

          {/* List */}
          <div className="space-y-3">
            {filtered.map((it) => (
              <AdCard key={it.id} item={it} />
            ))}
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}

