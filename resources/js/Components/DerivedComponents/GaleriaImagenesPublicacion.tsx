// Galería de imágenes clickeable en publicaciones
// En VerPerfil: muestra barra negra solo si hay >1 imagen
// En Editar: muestra todas las imágenes clickeables

interface Imagen {
  id: number;
  url: string;
}

interface GaleriaImagenesProps {
  imagenes: Imagen[];
  mostrar: boolean;
  onImagenClick?: (imagen: Imagen) => void;
  editable?: boolean;
}

export default function GaleriaImagenesPublicacion({ imagenes, mostrar, onImagenClick, editable = false }: GaleriaImagenesProps) {
  if (!mostrar || imagenes.length === 0) {
    return null;
  }

  // En VerPerfil solo mostrar si hay > 1 imagen, en edición mostrar todas
  if (!editable && imagenes.length <= 1) {
    return null;
  }

  const imagenesAMostrar = editable ? imagenes : imagenes.slice(0, 5);

  if (imagenesAMostrar.length === 0) {
    return null;
  }

  return (
    <div className="absolute bottom-0 left-0 right-0 h-1/4 bg-black bg-opacity-90 flex items-center justify-start gap-2 p-2 overflow-x-auto overflow-y-hidden rounded-b-lg">
      {imagenesAMostrar.map((img) => (
        <button
          key={img.id}
          onClick={() => onImagenClick?.(img)}
          className={`flex-shrink-0 w-12 h-12 md:w-16 md:h-16 rounded transition-all ${
            onImagenClick ? 'cursor-pointer hover:scale-110 hover:ring-2 hover:ring-white' : 'cursor-default'
          }`}
          type="button"
        >
          <img
            src={img.url}
            alt="Imagen publicación"
            className="w-full h-full object-cover rounded"
          />
        </button>
      ))}
    </div>
  );
}
