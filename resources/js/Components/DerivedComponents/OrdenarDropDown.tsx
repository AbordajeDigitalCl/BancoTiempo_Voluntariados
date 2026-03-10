import React from 'react';
import Dropdown from '../Dropdown';

interface OrdenarDropDownProps {
    onOrdenChange: (orden: 'precioMayor' | 'precioMenor' | 'popular' | 'reciente' | null) => void;
    ordenActual: 'precioMayor' | 'precioMenor' | 'popular' | 'reciente' | null;
}

export default function OrdenarDropDown({ onOrdenChange, ordenActual }: OrdenarDropDownProps) {
    const labelOrden = 
        ordenActual === 'precioMayor' ? 'Mayor Precio' :
        ordenActual === 'precioMenor' ? 'Menor Precio' :
        ordenActual === 'popular' ? 'Más Popular' :
        ordenActual === 'reciente' ? 'Más Reciente' :
        'Ordenar';

    const handleOrdenChange = (orden: 'precioMayor' | 'precioMenor' | 'popular' | 'reciente' | null) => {
        onOrdenChange(orden);
    };

    return (
        <div className="flex justify-end">
            <Dropdown>
                <Dropdown.Trigger>
                    <button className="inline-flex items-center px-3 py-2 border text-sm leading-4 font-medium rounded-md bg-white hover:bg-gray-50">
                    {labelOrden}
                        <svg
                            className="ml-2 -mr-0.5 h-4 w-4"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                        <path
                            fillRule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.64a.75.75 0 01-1.08 0l-4.25-4.64a.75.75 0 01.02-1.06z"
                            clipRule="evenodd"
                        />
                        </svg>
                    </button>
                </Dropdown.Trigger>

                <Dropdown.Content align="left" width="48">
                    <button 
                        onClick={() => handleOrdenChange(null)}
                        className="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100"
                    >
                        Sin ordenar
                    </button>
                    <button 
                        onClick={() => handleOrdenChange('precioMayor')}
                        className="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100"
                    >
                        Mayor Precio
                    </button>
                    <button 
                        onClick={() => handleOrdenChange('precioMenor')}
                        className="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100"
                    >
                        Menor Precio
                    </button>
                    <button 
                        onClick={() => handleOrdenChange('popular')}
                        className="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100"
                    >
                        Más Popular
                    </button>
                    <button 
                        onClick={() => handleOrdenChange('reciente')}
                        className="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100"
                    >
                        Más Reciente
                    </button>
                </Dropdown.Content>
            </Dropdown>
        </div>
    );
}

