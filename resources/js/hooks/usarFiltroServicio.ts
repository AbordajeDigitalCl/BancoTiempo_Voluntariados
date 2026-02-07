import { useState, useEffect } from 'react';
import { Servicio } from '@/mockData/datosMockPerfil';

export const useServiceFilter = (servicios: Servicio[]) => {
  const [filtroTipo, setFiltroTipo] = useState<'Servicio' | 'Producto' | null>(null);
  const [filtroOfertaDemanda, setFiltroOfertaDemanda] = useState<'Oferta' | 'Demanda' | null>(null);
  const [filtroOrden, setFiltroOrden] = useState<'precio' | 'popular' | 'reciente' | null>(null);
  const [itemsFiltrados, setItemsFiltrados] = useState<Servicio[]>(servicios);

  
  useEffect(() => {
    let resultado = [...servicios];

    
    if (filtroTipo) {
      resultado = resultado.filter((servicio) => servicio.tipo === filtroTipo);
    }

    
    if (filtroOfertaDemanda) {
      resultado = resultado.filter((servicio) => servicio.ofertaDemanda === filtroOfertaDemanda);
    }

   
    if (filtroOrden === 'precio') {
      resultado.sort((a, b) => {
        const costA = parseInt(a.costo);
        const costB = parseInt(b.costo);
        return costA - costB;
      });
    } else if (filtroOrden === 'reciente') {
      resultado.sort((a, b) => new Date(b.fecha).getTime() - new Date(a.fecha).getTime());
    }

    setItemsFiltrados(resultado);
  }, [servicios, filtroTipo, filtroOfertaDemanda, filtroOrden]);

  return {
    itemsFiltrados,
    filtroTipo,
    setFiltroTipo,
    filtroOfertaDemanda,
    setFiltroOfertaDemanda,
    filtroOrden,
    setFiltroOrden,
  };
};
