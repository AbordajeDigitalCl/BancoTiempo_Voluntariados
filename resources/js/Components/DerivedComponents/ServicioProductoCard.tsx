export default function ServicioProductoCard() {
    return (
      <div className="w-full max-w-4xl mx-auto p-4 bg-white border-2 border-solid border-blue-400 flex">
      <div className="w-3/11 aspect-square mr-4">
        <img
          src="https://picsum.photos/200"
          alt="Service"
          className="w-full h-full object-cover rounded-lg"
        />
      </div>

      <div className="flex flex-col justify-start w-full">
        <div className="flex items-start justify-between mb-2">
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
            <span className="text-base font-bold">5 horas</span>
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
              <span className="text-xs">Servicio</span>
            </div>
            <div className="flex flex-col items-center">
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
              <span className="text-xs">Oferta</span>
            </div>
          </div>
        </div>

        <h3 className="text-2xl font-bold text-left">
          Servicio afinación y balanceo
        </h3>

        <p className="text-justify text-sm mt-2">
          Este servicio incluye una revisión detallada del motor y el sistema de suspensión. 
          Asegura un mejor rendimiento del vehículo y extiende la vida útil de los componentes mecánicos. 
          Ideal para mantenimiento preventivo trimestral.
        </p>

        <div className="flex justify-between items-end mt-4">
          <div className="flex flex-col items-start">
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
                d="M11 5h2M12 4v2m0 14v2m0-6l2 2m-2-2l-2 2m-6-6h2m12 0h2"
              />
            </svg>
            <span className="text-xs">Editar</span>
          </div>
          <div className="flex flex-col items-center">
            <div className="w-6 h-6 rounded-full bg-blue-500"></div>
            <span className="text-xs text-blue-500">Desactivado</span>
          </div>
        </div>
      </div>
    </div>
    );
}


