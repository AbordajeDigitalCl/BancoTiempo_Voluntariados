import { useMemo } from "react";
import ServiceCard from "./ServicioProductoCard";
import { Servicio } from "@/mockData/datosMockPerfil";

interface ServiceCardListviewProps {
  servicios: Servicio[];
  filtroTipo?: 'Servicio' | 'Producto' | null;
  filtroOfertaDemanda?: 'Oferta' | 'Demanda' | null;
  filtroOrden?: 'precioMayor' | 'precioMenor' | 'popular' | 'reciente' | null;
  returnTo?: string;
}

export default function ServiceCardListview({
  servicios,
  filtroTipo = null,
  filtroOfertaDemanda = null,
  filtroOrden = null,
  returnTo = '/verperfil',
}: ServiceCardListviewProps) {
  const itemsFiltrados = useMemo(() => {
    let resultado = [...servicios];

    if (filtroTipo) {
      resultado = resultado.filter(item => item.tipo === filtroTipo);
    }

    if (filtroOfertaDemanda) {
      resultado = resultado.filter(item => item.ofertaDemanda === filtroOfertaDemanda);
    }

    
    if (filtroOrden) {
      if (filtroOrden === 'precioMenor') {
        resultado.sort((a, b) => {
          const costA = typeof a.costo === 'string' ? parseInt(a.costo.split(' ')[0]) : parseInt(a.costo) || 0;
          const costB = typeof b.costo === 'string' ? parseInt(b.costo.split(' ')[0]) : parseInt(b.costo) || 0;
          return costA - costB;
        });
      } else if (filtroOrden === 'precioMayor') {
        resultado.sort((a, b) => {
          const costA = typeof a.costo === 'string' ? parseInt(a.costo.split(' ')[0]) : parseInt(a.costo) || 0;
          const costB = typeof b.costo === 'string' ? parseInt(b.costo.split(' ')[0]) : parseInt(b.costo) || 0;
          return costB - costA;
        });
      } else if (filtroOrden === 'reciente') {
        resultado.sort((a, b) => new Date(b.fecha).getTime() - new Date(a.fecha).getTime());
      }
    }

    return resultado;
  }, [servicios, filtroTipo, filtroOfertaDemanda, filtroOrden]);

  return (
    <div className="w-full flex flex-col items-center">
      <div className="w-full max-w-5xl px-4 space-y-6">
        {itemsFiltrados.length > 0 ? (
          itemsFiltrados.map(servicio => (
            <ServiceCard key={servicio.id} servicio={servicio} returnTo={returnTo} />
          ))
        ) : (
          <div className="text-center py-8 text-gray-500">
            No hay servicios que coincidan con los filtros seleccionados.
          </div>
        )}
      </div>
    </div>
  );
}