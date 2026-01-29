import React from 'react';

export type TypeFilter = 'Servicio' | 'Producto';
export type DistanceFilter = 'Cualquiera' | '1km' | '10km' | '20km' | '50km';
export type OrderFilter = 'Precio popular' | 'Más populares' | 'Más recientes';

type Props = {
  type: TypeFilter;
  onTypeChange: (v: TypeFilter) => void;
  distance: DistanceFilter;
  onDistanceChange: (v: DistanceFilter) => void;
  order: OrderFilter;
  onOrderChange: (v: OrderFilter) => void;
};

function Tab({
  active,
  children,
  onClick,
}: {
  active: boolean;
  children: React.ReactNode;
  onClick: () => void;
}) {
  return (
    <button
      type="button"
      onClick={onClick}
      className={[
        'w-full rounded-lg px-3 py-2 text-xs font-medium transition',
        active
          ? 'bg-white text-gray-900 shadow-sm border border-gray-200'
          : 'bg-gray-100 text-gray-600 hover:bg-gray-200',
      ].join(' ')}
    >
      {children}
    </button>
  );
}

export default function FiltersBar({
  type,
  onTypeChange,
  distance,
  onDistanceChange,
  order,
  onOrderChange,
}: Props) {
  return (
    <div className="space-y-2">
      <div className="grid grid-cols-2 gap-2 rounded-xl bg-gray-50 p-2 border border-gray-200">
        <Tab active={type === 'Servicio'} onClick={() => onTypeChange('Servicio')}>
          Servicio
        </Tab>
        <Tab active={type === 'Producto'} onClick={() => onTypeChange('Producto')}>
          Producto
        </Tab>
      </div>

      <div className="grid grid-cols-2 gap-2">
        <select
          value={distance}
          onChange={(e) => onDistanceChange(e.target.value as DistanceFilter)}
          className="rounded-lg border border-gray-300 bg-white px-2 py-2 text-xs"
        >
          <option value="Cualquiera">Distancia</option>
          <option value="1km">1 km</option>
          <option value="10km">10 km</option>
          <option value="20km">20 km</option>
          <option value="50km">50 km</option>
        </select>

        <select
          value={order}
          onChange={(e) => onOrderChange(e.target.value as OrderFilter)}
          className="rounded-lg border border-gray-300 bg-white px-2 py-2 text-xs"
        >
          <option value="Precio popular">Precio popular</option>
          <option value="Más populares">Más populares</option>
          <option value="Más recientes">Más recientes</option>
        </select>
      </div>
    </div>
  );
}
