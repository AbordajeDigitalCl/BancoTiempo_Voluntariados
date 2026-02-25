import { Servicio } from '@/mockData/datosMockPerfil';
import { useState } from 'react';
import { getStateColors } from '@/constants/coloresEstado';
import ImageZoomModal from './ImageZoomModal';
import { guardarPublicacion } from '@/services/guardarPublicacion';

interface FormEditarMiPublicacionProps {
    publicacion: Servicio & { id: number };
}

export default function FormEditarMiPublicacionCRIS({ publicacion }: FormEditarMiPublicacionProps) {
    // MODIFICADO: Agregado estado 'tipo' para capturar Servicio/Producto independientemente
    const [titulo, setTitulo] = useState(publicacion.titulo);
    const [descripcion, setDescripcion] = useState(publicacion.descripcion);
    const [costo, setCosto] = useState(String(publicacion.costo).replace(/[^0-9]/g, ''));
    const [tipo, setTipo] = useState(publicacion.tipo); // NUEVO: Capturar cambios de Servicio/Producto
    const [ofertaDemanda, setOfertaDemanda] = useState(publicacion.ofertaDemanda);
    const [estado, setEstado] = useState(publicacion.estado);
    const [imagen, setImagen] = useState(publicacion.imagen);

    const handleImagenChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (event) => {
            const imagenData = event.target?.result as string;
            setImagen(imagenData);
        };
        reader.readAsDataURL(file);
    };

    // MODIFICADO: Guardar ahora incluye 'tipo' (Servicio/Producto) como parámetro independiente
    // Este cambio permite que el servidor maneje cambios de Tipo_Publicacion separadamente de Oferta/Demanda
    const handleGuardar = () => {
        guardarPublicacion(publicacion.id, {
            titulo,
            descripcion,
            costo: parseInt(costo) || 0,
            tipo, // NUEVO: Enviar tipo (Servicio o Producto) independientemente
            ofertaDemanda,
        });
    };

   
    const opcionesEstado = tipo === 'Servicio' 
        ? ['Activo', 'Desactivado']
        : ['Activo', 'Agotado'];

    return (
        <div className="w-full max-w-6xl">
            
            <h1 className="text-3xl md:text-4xl font-bold text-gray-800 mb-8">
                Editar publicación
            </h1>

            
            <div className="bg-white rounded-xl shadow-md border border-gray-200 p-6 md:p-8" style={{borderColor: getStateColors(estado as any).hex, borderWidth: '2px'}}>
                <div className="flex flex-col lg:flex-row gap-8">
                    
                    <div className="hidden lg:flex lg:w-1/3 flex-col">
                        
                        <div className="relative mb-6">
                            <ImageZoomModal
                                src={imagen}
                                alt={titulo}
                                className="w-full h-64 md:h-72 rounded-lg object-cover"
                            />
                            
                            <div className="mt-4 border-2 border-dashed border-black rounded-lg p-6 flex flex-col items-center justify-center bg-gray-400 group-hover:bg-gray-500" id="upload-area">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    className="w-12 h-12 text-gray-600 mb-2"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3v-6"
                                    />
                                </svg>
                                <input
                                    type="file"
                                    accept="image/*"
                                    onChange={handleImagenChange}
                                    className="hidden"
                                    id="file-input-imagen"
                                />
                                <label htmlFor="file-input-imagen" className="bg-white hover:bg-gray-100 text-black px-6 py-2 rounded-lg cursor-pointer transition font-bold text-sm border border-dashed border-black" onMouseEnter={() => document.getElementById('upload-area')?.classList.add('bg-gray-500')} onMouseLeave={() => document.getElementById('upload-area')?.classList.remove('bg-gray-500')}>
                                    Cambiar imagen de producto
                                </label>
                            </div>
                        </div>

                      
                        <div className="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h3 className="font-semibold text-gray-800 mb-3">Resumen de cambios</h3>
                            <div className="space-y-2 text-sm text-gray-700">
                                <p><strong>Título:</strong> {titulo}</p>
                                <p><strong>Tipo:</strong> {tipo}</p>
                                <p><strong>Modo:</strong> {ofertaDemanda}</p>
                                <p><strong>Costo:</strong> {costo}</p>
                                <p><strong>Estado:</strong> {estado}</p>
                            </div>
                        </div>
                    </div>

                    
                    <div className="w-full lg:w-2/3 flex flex-col gap-6">
                        
                        <div className="lg:hidden">
                            <div className="flex items-center gap-3">
                                <div className="flex flex-col items-center">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        className="w-6 h-6 text-gray-600 mb-1"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    <label className="text-sm font-semibold text-gray-700">
                                        Costo
                                    </label>
                                </div>
                                <div className="flex items-center gap-2">
                                    <input
                                        type="text"
                                        value={costo}
                                        onChange={(e) => {
                                            const value = e.target.value.replace(/[^0-9]/g, '').slice(0, 3);
                                            setCosto(value);
                                        }}
                                        maxLength={3}
                                        placeholder="000"
                                        className="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-16 text-center"
                                    />
                                    <span className="text-sm font-semibold text-gray-700">horas</span>
                                </div>
                            </div>
                        </div>

                        
                        <div className="lg:hidden space-y-4">
                            <div>
                                <label className="block text-sm font-semibold text-gray-700 mb-2">
                                    Tipo
                                </label>
                                <select
                                    value={tipo}
                                    onChange={(e) => setTipo(e.target.value as any)}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                                >
                                    <option value="Servicio">Servicio</option>
                                    <option value="Producto">Producto</option>
                                </select>
                            </div>

                            <div>
                                <label className="block text-sm font-semibold text-gray-700 mb-2">
                                    Modo
                                </label>
                                <select
                                    value={ofertaDemanda}
                                    onChange={(e) => setOfertaDemanda(e.target.value as any)}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                                >
                                    <option value="Oferta">Oferta</option>
                                    <option value="Demanda">Demanda</option>
                                </select>
                            </div>
                        </div>

                        
                        <div className="lg:hidden">
                            <ImageZoomModal
                                src={imagen}
                                alt={titulo}
                                className="w-full h-48 rounded-lg object-cover"
                            />
                            <div className="mt-4 border-2 border-dashed border-black rounded-lg p-6 flex flex-col items-center justify-center bg-gray-400" id="upload-area-mobile">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    className="w-12 h-12 text-gray-600 mb-2"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3v-6"
                                    />
                                </svg>
                                <input
                                    type="file"
                                    accept="image/*"
                                    onChange={handleImagenChange}
                                    className="hidden"
                                    id="file-input-imagen-mobile"
                                />
                                <label htmlFor="file-input-imagen-mobile" className="bg-white hover:bg-gray-100 text-black px-6 py-2 rounded-lg cursor-pointer transition font-bold text-sm border border-dashed border-black">
                                    Cambiar imagen de producto
                                </label>
                            </div>
                        </div>

                        
                        <div className="hidden lg:flex items-end justify-between gap-4">
                            
                            <div className="flex items-center gap-3">
                                <div className="flex flex-col items-center">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        className="w-6 h-6 text-gray-600 mb-1"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    <label className="text-sm font-semibold text-gray-700">
                                        Costo
                                    </label>
                                </div>
                                <div className="flex items-center gap-2">
                                    <input
                                        type="text"
                                        value={costo}
                                        onChange={(e) => {
                                            const value = e.target.value.replace(/[^0-9]/g, '').slice(0, 3);
                                            setCosto(value);
                                        }}
                                        maxLength={3}
                                        placeholder="000"
                                        className="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-16 text-center"
                                    />
                                    <span className="text-sm font-semibold text-gray-700">horas</span>
                                </div>
                            </div>

                         
                            <div className="flex gap-4">
                                <div>
                                    <label className="block text-sm font-semibold text-gray-700 mb-2">
                                        Tipo
                                    </label>
                                    <select
                                        value={tipo}
                                        onChange={(e) => setTipo(e.target.value as any)}
                                        className="w-fit min-w-[7.5rem] px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                                    >
                                        <option value="Servicio">Servicio</option>
                                        <option value="Producto">Producto</option>
                                    </select>
                                </div>

                                <div>
                                    <label className="block text-sm font-semibold text-gray-700 mb-2">
                                        Modo
                                    </label>
                                    <select
                                        value={ofertaDemanda}
                                        onChange={(e) => setOfertaDemanda(e.target.value as any)}
                                        className="w-fit min-w-[8.5rem] px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                                    >
                                        <option value="Oferta">Oferta</option>
                                        <option value="Demanda">Demanda</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-2">
                                Nombre
                            </label>
                            <input
                                type="text"
                                value={titulo}
                                onChange={(e) => setTitulo(e.target.value)}
                                placeholder="Escribe un nombre aquí"
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                            />
                        </div>

                        
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-2">
                                Descripción
                            </label>
                            <textarea
                                value={descripcion}
                                onChange={(e) => setDescripcion(e.target.value)}
                                placeholder="Inserte una descripción a su publicación"
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 resize-none h-24"
                            />
                        </div>

                        
                        <div className="flex justify-end">
                            <div className="w-fit flex items-end gap-3">
                                <div className="w-6 h-6 rounded-full flex-shrink-0 mb-2" style={{backgroundColor: getStateColors(estado as any).hex}}></div>
                                <div>
                                    <label className="block text-sm font-semibold text-gray-700 mb-2">
                                        Estado
                                    </label>
                                    <select
                                        value={estado}
                                        onChange={(e) => setEstado(e.target.value as any)}
                                        className="w-fit min-w-[8.5rem] px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                                    >
                                        {opcionesEstado.map((opt) => (
                                            <option key={opt} value={opt}>
                                                {opt}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                            </div>
                        </div>

                       
                        <button
                            onClick={handleGuardar}
                            className="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg transition text-lg"
                        >
                            Guardar cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}
