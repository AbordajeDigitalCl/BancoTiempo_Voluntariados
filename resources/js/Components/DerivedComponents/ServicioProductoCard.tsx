import { Servicio } from '@/mockData/datosMockPerfil';
import { getStateColors, STATE_COLORS } from '@/constants/coloresEstado';
import { Link } from '@inertiajs/react';
import { obtenerTextoEstado } from '@/services/estadosPublicaciones';
import { obtenerImagenesPublicacion } from '@/services/servicioImagenesPublicacion';
import { useEffect, useState } from 'react';
import GaleriaImagenesPublicacion from './GaleriaImagenesPublicacion';

interface ServicioProductoCardProps {
    servicio: Servicio;
    returnTo?: string;
}

export default function ServicioProductoCard({ servicio, returnTo = '/verperfil' }: ServicioProductoCardProps) {
    // Obtener el estado desde localStorage en lugar de props
    const estadoActual = obtenerTextoEstado(servicio.id, servicio.tipo as 'Servicio' | 'Producto');
    const { hex } = getStateColors(estadoActual);
    
    // Estado para la imagen principal (puede cambiar si clickea otra)
    const [imagenPrincipal, setImagenPrincipal] = useState(servicio.imagen);
    
    // Estado para las imágenes de la publicación
    const [imagenes, setImagenes] = useState<any[]>([]);
    const [cargandoImagenes, setCargandoImagenes] = useState(true);

    const handleImagenClick = (imagen: any) => {
        // Mostrar la imagen clickeada en grande
        setImagenPrincipal(imagen.url);
    };

    // Cargar imágenes cuando el componente se monta
    useEffect(() => {
        const cargarImagenes = async () => {
            try {
                const resultado = await obtenerImagenesPublicacion(servicio.id);
                setImagenes(resultado.imagenes);
                // Si hay imágenes, usar la primera como principal
                if (resultado.imagenes.length > 0) {
                    setImagenPrincipal(resultado.imagenes[0].url);
                } else {
                    // Si no hay imágenes, usar placeholder o imagen del servicio
                    setImagenPrincipal(servicio.imagen || `https://picsum.photos/200?random=${servicio.id}`);
                }
            } catch (error) {
                console.error('Error al cargar imágenes:', error);
                setImagenPrincipal(servicio.imagen);
            } finally {
                setCargandoImagenes(false);
            }
        };
        cargarImagenes();
    }, [servicio.id, servicio.imagen]);
    
    const cardStyle = {
        border: `2px solid ${hex}`
    };
    
    const circleStyle = {
        backgroundColor: hex
    };

    return (
      <div className="w-full max-w-4xl mx-auto p-4 bg-white flex flex-col md:flex-row gap-4" style={cardStyle}>
        <div className="w-full md:w-1/3 md:flex-shrink-0 h-48 md:h-64 relative">
          <img
            src={imagenPrincipal}
            alt={servicio.titulo}
            className="w-full h-full object-cover rounded-lg"
          />
          {/* Mostrar galería de imágenes pequeñas si hay imágenes */}
          <GaleriaImagenesPublicacion 
            imagenes={imagenes} 
            mostrar={imagenes.length > 0}
            onImagenClick={handleImagenClick}
          />
        </div>

        <div className="flex-1 flex flex-col justify-between w-full gap-3">
        <div className="flex items-start justify-between">
          <div className="flex items-center">
            <div className="flex flex-col items-center mr-2">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                className="w-6 h-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                strokeWidth="2"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              <span className="text-xs">Costo</span>
            </div>
            <span className="text-base font-bold">{servicio.costo} horas</span>
          </div>
          <div className="flex gap-4">
            <div className="flex flex-col items-center">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                className="w-6 h-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                strokeWidth="2"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  d="M15 12l4.5 4.5M4 4l7 7m0 0l-7 7"
                />
              </svg>
              <span className="text-xs">{servicio.tipo}</span>
            </div>
            <div className="flex flex-col items-center">
              {servicio.ofertaDemanda === 'Oferta' ? (
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  className="w-6 h-6"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  strokeWidth="2"
                >
                  <circle cx="12" cy="12" r="10" />
                  <text x="12" y="16" textAnchor="middle" fontSize="12" fill="currentColor">$</text>
                </svg>
              ) : (
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  className="w-6 h-6"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  strokeWidth="2"
                >
                  <circle cx="12" cy="12" r="10" />
                  <text x="12" y="15" textAnchor="middle" fontSize="14" fontWeight="bold" fill="currentColor">?</text>
                </svg>
              )}
              <span className="text-xs">{servicio.ofertaDemanda}</span>
            </div>
          </div>
        </div>

        <h3 className="text-lg font-bold text-left">
          {servicio.titulo}
        </h3>

        <p className="text-justify text-sm">
          {servicio.descripcion}
        </p>

        <div className="flex justify-between items-end">
          <div className="flex items-center gap-4">
            <Link href={`/Editar-Publicacion/${servicio.id}?return_to=${encodeURIComponent(returnTo)}`} className="flex flex-col items-start hover:opacity-75 transition">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                className="w-6 h-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                strokeWidth="2"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5a4 4 0 0 1-2.828 1.172L2 21l.5-2.672A4 4 0 0 1 3.5 15.5L17 3z"
                />
              </svg>
              <span className="text-xs">Editar</span>
            </Link>
            <div className="flex flex-col items-start">
              <span className="text-xs text-gray-500">{new Date(servicio.fecha).toLocaleDateString('es-ES')}</span>
            </div>
          </div>
          <div className="flex flex-col items-center">
            <div className="w-6 h-6 rounded-full" style={circleStyle}></div>
            <span className="text-xs" style={{color: hex}}>{estadoActual}</span>
          </div>
        </div>
      </div>
    </div>
    );
}


