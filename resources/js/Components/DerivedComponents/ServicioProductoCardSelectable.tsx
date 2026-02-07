import { Servicio } from '@/mockData/datosMockPerfil';
import { getStateColors } from '@/constants/coloresEstado';
import { Link } from '@inertiajs/react';

interface ServicioProductoCardSelectableProps {
    servicio: Servicio;
}

export default function ServicioProductoCardSelectable({ servicio }: ServicioProductoCardSelectableProps) {
    const { hex } = getStateColors(servicio.estado);
    
    const cardStyle = {
        border: `2px solid ${hex}`
    };
    
    const circleStyle = {
        backgroundColor: hex
    };

    return (
      <Link href={`/Editar-Publicacion/${servicio.id}`}>
        <div className="w-full max-w-4xl mx-auto p-4 bg-white flex flex-col md:flex-row gap-4 cursor-pointer hover:shadow-lg hover:scale-105 transition-all" style={cardStyle}>
          <div className="w-full md:w-1/3 md:flex-shrink-0 h-48 md:h-64">
            <img
              src={servicio.imagen}
              alt={servicio.titulo}
              className="w-full h-full object-cover rounded-lg"
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
                <span className="text-base font-bold">{servicio.costo}</span>
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
                <div className="flex flex-col items-start">
                  <span className="text-xs text-gray-500">{new Date(servicio.fecha).toLocaleDateString('es-ES')}</span>
                </div>
              </div>
              <div className="flex flex-col items-center">
                <div className="w-6 h-6 rounded-full" style={circleStyle}></div>
                <span className="text-xs" style={{color: hex}}>{servicio.estado}</span>
              </div>
            </div>
          </div>
        </div>
      </Link>
    );
}
