import { Servicio } from '@/mockData/datosMockPerfil';
import { useState, useEffect, useRef } from 'react';
import { getStateColors } from '@/constants/coloresEstado';
import ImageZoomModal from './ImageZoomModal';
import { guardarPublicacion } from '@/services/guardarPublicacion';
import { obtenerTextoEstado, cambiarEstadoPublicacion } from '@/services/estadosPublicaciones';
import { obtenerImagenesPublicacion, subirYAsociarImagenPublicacion, cambiarImagenPublicacion } from '@/services/servicioImagenesPublicacion';
import GaleriaImagenesPublicacion from './GaleriaImagenesPublicacion';
import AjustadorImagenPublicacion from './AjustadorImagenPublicacion';
import CambiarEliminarImagenPublicacion from './CambiarEliminarImagenPublicacion';

interface FormEditarMiPublicacionProps {
    publicacion: Servicio & { id: number };
    returnTo?: string;
}

export default function FormEditarMiPublicacionCRIS({ publicacion, returnTo = '/verperfil' }: FormEditarMiPublicacionProps) {
    // MODIFICADO: Agregado estado 'tipo' para capturar Servicio/Producto independientemente
    const [titulo, setTitulo] = useState(publicacion.titulo);
    const [descripcion, setDescripcion] = useState(publicacion.descripcion);
    const [costo, setCosto] = useState(String(publicacion.costo).replace(/[^0-9]/g, ''));
    const [tipo, setTipo] = useState(publicacion.tipo); // NUEVO: Capturar cambios de Servicio/Producto
    const [ofertaDemanda, setOfertaDemanda] = useState(publicacion.ofertaDemanda);
    // MODIFICADO: El estado ahora se obtiene desde localStorage usando obtenerTextoEstado()
    const [estado, setEstado] = useState(obtenerTextoEstado(publicacion.id, publicacion.tipo as 'Servicio' | 'Producto'));
    const [imagen, setImagen] = useState(publicacion.imagen);
    const [imagenId, setImagenId] = useState<number | null>(publicacion.imagenId || null); // NUEVO: Track image ID from server
    const [isUploadingImage, setIsUploadingImage] = useState(false); // NUEVO: Track upload progress
    const [isMobile, setIsMobile] = useState(window.innerWidth < 1024); // NUEVO: Detectar si es mobile/tablet

    // NUEVO: Estado para manejar múltiples imágenes
    const [imagenes, setImagenes] = useState<any[]>([]);
    const [cargandoImagenes, setCargandoImagenes] = useState(true);
    const [mostrarModalCambiarPrincipal, setMostrarModalCambiarPrincipal] = useState(false);
    const fileInputAnadirRef = useRef<HTMLInputElement>(null);

    // NUEVO: Estados para los modales de ajuste y eliminación de imágenes
    const [mostrarAjustador, setMostrarAjustador] = useState(false);
    const [imagenSeleccionada, setImagenSeleccionada] = useState<File | null>(null);
    const [mostrarEliminar, setMostrarEliminar] = useState(false);
    
    // NUEVO: Estados para cambiar imagen
    const [mostrarCambiar, setMostrarCambiar] = useState(false);
    const [imagenIdACambiar, setImagenIdACambiar] = useState<number | null>(null);
    const [esCambio, setEsCambio] = useState(false); // true = es cambio, false = es añadir

    // NUEVO: Detectar cambios de tamaño de ventana
    useEffect(() => {
        const handleResize = () => {
            setIsMobile(window.innerWidth < 1024);
        };
        window.addEventListener('resize', handleResize);
        return () => window.removeEventListener('resize', handleResize);
    }, []);

    // NUEVO: Manejar cambios de estado y guardar en localStorage
    const handleEstadoChange = (nuevoEstado: string) => {
        setEstado(nuevoEstado as 'Activo' | 'Desactivado' | 'Agotado');
        // Convertir el texto del estado a ON/OFF y guardar en localStorage
        const estadoOn = nuevoEstado === 'Activo';
        cambiarEstadoPublicacion(publicacion.id, estadoOn);
    };

    // NUEVO: Quando el tipo cambia (Servicio <-> Producto), resetear el estado a Activo (ON)
    const isFirstRender = useRef(true);
    
    useEffect(() => {
        // Skip la primera renderización (cuando se monta el componente)
        if (isFirstRender.current) {
            isFirstRender.current = false;
            return;
        }
        
        // El tipo cambió, resetear a Activo (ON) y actualizar color
        setEstado('Activo');
        cambiarEstadoPublicacion(publicacion.id, true); // true = ON (Activo)
    }, [tipo, publicacion.id]);

    // NUEVO: Cargar imágenes al montar el componente
    useEffect(() => {
        const cargarImagenes = async () => {
            try {
                const resultado = await obtenerImagenesPublicacion(publicacion.id);
                setImagenes(resultado.imagenes);
            } catch (error) {
                console.error('Error al cargar imágenes:', error);
            } finally {
                setCargandoImagenes(false);
            }
        };
        cargarImagenes();
    }, [publicacion.id]);

    const handleImagenChange = async (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (!file) return;

        // Si es mobile/tablet, subir directamente sin ajuste
        if (isMobile) {
            setIsUploadingImage(true);
            try {
                let response;
                
                if (esCambio && imagenIdACambiar) {
                    response = await cambiarImagenPublicacion(file, publicacion.id, imagenIdACambiar);
                } else {
                    response = await subirYAsociarImagenPublicacion(file, publicacion.id);
                }
                
                if (imagenes.length === 0) {
                    setImagen(response.url);
                }
                
                await new Promise(resolve => setTimeout(resolve, 500));
                const resultado = await obtenerImagenesPublicacion(publicacion.id);
                setImagenes(resultado.imagenes);
                
                setEsCambio(false);
                setImagenIdACambiar(null);
            } catch (error) {
                console.error('Error uploading image:', error);
                alert('Error al subir imagen: ' + (error instanceof Error ? error.message : 'Error desconocido'));
            } finally {
                setIsUploadingImage(false);
                if (e.target) {
                    e.target.value = '';
                }
            }
        } else {
            // En desktop, mostrar el ajustador
            setImagenSeleccionada(file);
            setMostrarAjustador(true);
            
            if (e.target) {
                e.target.value = '';
            }
        }
    };

    // Cuando el usuario confirma el ajuste, sube la imagen recortada
    // FALLO DETECTADO (07-03-2026): 
    // Los cambios de imagen (subir, cambiar, eliminar) se aplican INMEDIATAMENTE al confirmar el ajuste,
    // sin esperar a que el usuario presione el botón "Guardar cambios".
    // Esto significa: si el usuario regresa sin guardar la publicación, los cambios de imagen PERSISTEN.
    // 
    // COMPORTAMIENTO ACTUAL (INCORRECTO):
    // 1. Usuario ajusta imagen y presiona confirmar → SUBE INMEDIATAMENTE al backend
    // 2. Usuario regresa sin guardar → cambios de imagen PERSISTEN (❌ fallo)
    //
    // COMPORTAMIENTO ESPERADO (CORRECTO):
    // 1. Usuario ajusta imagen y presiona confirmar → se guarda en estado local SOLAMENTE
    // 2. Usuario presiona "Guardar cambios" → recién ENTONCES se aplican cambios de imagen
    // 3. Usuario regresa sin guardar → cambios de imagen se descartan
    //
    // TODO: REFACTORIZAR para:
    // - Almacenar cambios de imagen en estado "pendiente" (no subir al backend)
    // - Ejecutar cambios de imagen en handleGuardar() junto con guardarPublicacion()
    // - Si usuario no presiona guardar, descartar cambios
    const handleConfirmAjuste = async (croppedFile: File) => {
        setIsUploadingImage(true);
        setMostrarAjustador(false);

        try {
            let response;
            
            if (esCambio && imagenIdACambiar) {
                // Cambiar imagen existente
                response = await cambiarImagenPublicacion(croppedFile, publicacion.id, imagenIdACambiar);
            } else {
                // Añadir nueva imagen
                response = await subirYAsociarImagenPublicacion(croppedFile, publicacion.id);
            }
            
            // Actualizar primera imagen si es la primera subida
            if (imagenes.length === 0) {
                setImagen(response.url);
            }
            
            // Recargar imágenes después de subida exitosa
            await new Promise(resolve => setTimeout(resolve, 500));
            const resultado = await obtenerImagenesPublicacion(publicacion.id);
            setImagenes(resultado.imagenes);
            
            setImagenSeleccionada(null);
            setEsCambio(false);
            setImagenIdACambiar(null);
        } catch (error) {
            console.error('Error uploading image:', error);
            alert('Error al subir imagen: ' + (error instanceof Error ? error.message : 'Error desconocido'));
        } finally {
            setIsUploadingImage(false);
        }
    };

    // Cuando el usuario cancela el ajuste
    const handleCancelAjuste = () => {
        setMostrarAjustador(false);
        setImagenSeleccionada(null);
    };

    // Cuando se elimina una imagen
    const handleImagenEliminada = (imagenId: number) => {
        // Remover de la lista local
        const imagenesActualizadas = imagenes.filter(img => img.id !== imagenId);
        setImagenes(imagenesActualizadas);
        
        // Cerrar el modal de eliminar
        setMostrarEliminar(false);
        
        // Si se borraron todas las imágenes, mostrar placeholder
        if (imagenesActualizadas.length === 0) {
            setImagen(`https://picsum.photos/200?random=${publicacion.id}&t=${Date.now()}`);
        } else if (imagenes.length > 0) {
            // Si la imagen eliminada era la principal, cambiar a la primera disponible
            const primeraImagen = imagenesActualizadas[0];
            if (primeraImagen) {
                setImagen(primeraImagen.url);
            }
        }
    };

    // Manejar click en imagen de la galería
    const handleImagenClickeada = (imagen: any) => {
        setImagen(imagen.url);
    };

    // Botones que triggerean los inputs file
    const handleAnadirImagen = () => {
        if (imagenes.length >= 5) {
            alert('Se ha superado el límite de imágenes para esta publicación');
            return;
        }
        setEsCambio(false);
        fileInputAnadirRef.current?.click();
    };

    const handleCambiarImagen = () => {
        // Mostrar modal para seleccionar qué imagen cambiar
        setMostrarCambiar(true);
    };

    // Cuando el usuario selecciona una imagen para cambiar
    const handleImagenSeleccionadaParaCambiar = (imagenId: number) => {
        setImagenIdACambiar(imagenId);
        setEsCambio(true);
        setMostrarCambiar(false);
        fileInputAnadirRef.current?.click(); // Reutilizar el mismo input
    };

    // Nueva función para mostrar el modal de eliminar imágenes
    const handleMostrarEliminar = () => {
        setMostrarEliminar(true);
    };

    // Guardar cambios
    const handleGuardar = () => {
        guardarPublicacion(publicacion.id, {
            titulo,
            descripcion,
            costo: parseInt(costo) || 0,
            tipo,
            ofertaDemanda,
            ...(imagenId && { imagenId }),
            estado,
        }, returnTo);
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
                            <div className="relative">
                                <ImageZoomModal
                                    src={imagen}
                                    alt={titulo}
                                    className="w-full h-64 md:h-72 rounded-lg object-cover"
                                />
                                
                                {/*Galería de imágenes clickeable dentro de la imagen */}
                                <GaleriaImagenesPublicacion 
                                    imagenes={imagenes}
                                    mostrar={imagenes.length > 0}
                                    onImagenClick={handleImagenClickeada}
                                    editable={true}
                                />
                            </div>
                            
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
                                {/* Tres inputs file: uno para añadir/cambiar */}
                                <input
                                    ref={fileInputAnadirRef}
                                    type="file"
                                    accept="image/*"
                                    onChange={handleImagenChange}
                                    disabled={isUploadingImage}
                                    className="hidden"
                                />
                                
                                {/* Tres botones: Añadir, Cambiar, Eliminar */}
                                <div className="flex flex-col w-full gap-2">
                                    <div className="flex gap-2">
                                        <button 
                                            type="button"
                                            onClick={handleAnadirImagen}
                                            disabled={isUploadingImage}
                                            className={`flex-1 ${isUploadingImage ? 'bg-gray-300 cursor-not-allowed' : 'bg-white hover:bg-gray-100 cursor-pointer'} text-black px-4 py-2 rounded-lg transition font-bold text-sm border border-dashed border-black text-center`}
                                        >
                                            Añadir imagen ({imagenes.length}/5)
                                        </button>
                                        <button 
                                            type="button"
                                            onClick={handleCambiarImagen}
                                            disabled={isUploadingImage}
                                            className={`flex-1 ${isUploadingImage ? 'bg-gray-300 cursor-not-allowed' : 'bg-white hover:bg-gray-100 cursor-pointer'} text-black px-4 py-2 rounded-lg transition font-bold text-sm border border-dashed border-black text-center`}
                                        >
                                            Cambiar imagen
                                        </button>
                                    </div>
                                    <button 
                                        type="button"
                                        onClick={handleMostrarEliminar}
                                        disabled={isUploadingImage || imagenes.length === 0}
                                        className={`w-full ${isUploadingImage || imagenes.length === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-red-500 hover:bg-red-600 cursor-pointer'} text-white px-4 py-2 rounded-lg transition font-bold text-sm border border-dashed border-red-700`}
                                    >
                                        Eliminar imagen
                                    </button>
                                </div>
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
                            <div className="mt-4 border-2 border-dashed border-black rounded-lg p-6 flex flex-col items-center justify-center bg-gray-400">
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
                                
                                {/* Tres botones: Añadir, Cambiar, Eliminar (móvil) */}
                                <div className="flex flex-col w-full gap-2">
                                    <div className="flex gap-2">
                                        <button 
                                            type="button"
                                            onClick={handleAnadirImagen}
                                            disabled={isUploadingImage}
                                            className={`flex-1 ${isUploadingImage ? 'bg-gray-300 cursor-not-allowed' : 'bg-white hover:bg-gray-100 cursor-pointer'} text-black px-4 py-2 rounded-lg transition font-bold text-sm border border-dashed border-black text-center`}
                                        >
                                            Añadir ({imagenes.length}/5)
                                        </button>
                                        <button 
                                            type="button"
                                            onClick={handleCambiarImagen}
                                            disabled={isUploadingImage || imagenes.length === 0}
                                            className={`flex-1 ${isUploadingImage || imagenes.length === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-white hover:bg-gray-100 cursor-pointer'} text-black px-4 py-2 rounded-lg transition font-bold text-sm border border-dashed border-black text-center`}
                                        >
                                            Cambiar
                                        </button>
                                    </div>
                                    <button 
                                        type="button"
                                        onClick={handleMostrarEliminar}
                                        disabled={isUploadingImage || imagenes.length === 0}
                                        className={`w-full ${isUploadingImage || imagenes.length === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-red-500 hover:bg-red-600 cursor-pointer'} text-white px-4 py-2 rounded-lg transition font-bold text-sm border border-dashed border-red-700`}
                                    >
                                        Eliminar
                                    </button>
                                </div>
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
                                        Beneficio
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
                                        Tipo
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
                                        onChange={(e) => handleEstadoChange(e.target.value as any)}
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

            {/* Modal de ajuste de imagen */}
            {mostrarAjustador && imagenSeleccionada && (
                <AjustadorImagenPublicacion
                    imagenFile={imagenSeleccionada}
                    onConfirm={handleConfirmAjuste}
                    onCancel={handleCancelAjuste}
                />
            )}

            {/* Modal de cambio de imagen */}
            {mostrarCambiar && (
                <CambiarEliminarImagenPublicacion
                    imagenes={imagenes}
                    publicacionId={publicacion.id}
                    modo="cambiar"
                    onImagenSeleccionada={handleImagenSeleccionadaParaCambiar}
                    onCancel={() => setMostrarCambiar(false)}
                />
            )}

            {/* Modal de eliminación de imagen */}
            {mostrarEliminar && (
                <CambiarEliminarImagenPublicacion
                    imagenes={imagenes}
                    publicacionId={publicacion.id}
                    modo="eliminar"
                    onImagenEliminada={handleImagenEliminada}
                    onCancel={() => setMostrarEliminar(false)}
                />
            )}
        </div>
    );
}
