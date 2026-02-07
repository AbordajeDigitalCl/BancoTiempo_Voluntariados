import { useMemo } from "react";
import ServiceCard from "./ServicioProductoCard";
import { Servicio } from "@/mockData/datosMockPerfil";

interface ServiceCardListviewProps {
  servicios: Servicio[];
  filtroTipo?: 'Servicio' | 'Producto' | null;
  filtroOfertaDemanda?: 'Oferta' | 'Demanda' | null;
  filtroOrden?: 'precio' | 'popular' | 'reciente' | null;
}

export default function ServiceCardListview({
  servicios,
  filtroTipo = null,
  filtroOfertaDemanda = null,
  filtroOrden = null,
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
      if (filtroOrden === 'precio') {
        resultado.sort((a, b) => {
          const costA = parseInt(a.costo.split(' ')[0]);
          const costB = parseInt(b.costo.split(' ')[0]);
          return costA - costB;
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
            <ServiceCard key={servicio.id} servicio={servicio} />
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