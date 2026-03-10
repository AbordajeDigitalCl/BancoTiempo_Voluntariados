import { useState } from 'react';

interface Imagen {
  id: number;
  url: string;
  Direccion?: string;
}

interface CambiarEliminarImagenProps {
  imagenes: Imagen[];
  publicacionId: number;
  modo: 'cambiar' | 'eliminar';
  onImagenSeleccionada?: (imagenId: number) => void; // Para cambiar imagen
  onImagenEliminada?: (imagenId: number) => void; // Para eliminar imagen
  onCancel: () => void;
}

export default function CambiarEliminarImagenPublicacion({
  imagenes,
  publicacionId,
  modo,
  onImagenSeleccionada,
  onImagenEliminada,
  onCancel,
}: CambiarEliminarImagenProps) {
  const [imagenSeleccionada, setImagenSeleccionada] = useState<Imagen | null>(null);
  const [isDeleting, setIsDeleting] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handleSeleccionarImagen = (imagen: Imagen) => {
    setImagenSeleccionada(imagen);
  };

  const handleConfirmarEliminar = async () => {
    if (!imagenSeleccionada) return;

    setIsDeleting(true);
    setError(null);

    try {
      // Obtener token CSRF fresco
      const refreshResponse = await fetch('/refresh-csrf', {
        method: 'GET',
        credentials: 'include',
      });
      const refreshData = await refreshResponse.json();
      const csrfToken = refreshData.csrf_token;

      // DELETE /eliminar-imagen-publicacion/{publicacion_id}/{imagen_id}
      const response = await fetch(`/eliminar-imagen-publicacion/${publicacionId}/${imagenSeleccionada.id}`, {
        method: 'DELETE',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
      });

      if (!response.ok) {
        throw new Error('Error al eliminar la imagen');
      }

      onImagenEliminada?.(imagenSeleccionada.id);
      setImagenSeleccionada(null);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Error desconocido');
    } finally {
      setIsDeleting(false);
    }
  };

  const handleConfirmarCambiar = () => {
    if (imagenSeleccionada) {
      onImagenSeleccionada?.(imagenSeleccionada.id);
      setImagenSeleccionada(null);
    }
  };

  // Si mostrar confirmación para eliminar
  if (imagenSeleccionada && modo === 'eliminar') {
    return (
      <div className="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4">
        <div className="bg-white rounded-lg shadow-2xl max-w-md w-full">
          {/* Header */}
          <div className="bg-red-50 px-6 py-4 border-b border-red-200">
            <h2 className="text-xl font-bold text-red-800">¿Estás seguro?</h2>
            <p className="text-sm text-red-700 mt-1">No se puede deshacer esta acción</p>
          </div>

          {/* Preview de imagen a eliminar */}
          <div className="p-6">
            <div className="mb-4 flex justify-center">
              <img
                src={imagenSeleccionada.url}
                alt="Imagen a eliminar"
                className="w-32 h-32 object-cover rounded-lg border-2 border-red-300"
              />
            </div>
            <p className="text-center text-gray-700 mb-4">Esta imagen será eliminada permanentemente</p>

            {error && <div className="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">{error}</div>}
          </div>

          {/* Botones */}
          <div className="bg-gray-50 px-6 py-4 border-t border-gray-200 flex gap-3 justify-end">
            <button
              onClick={() => setImagenSeleccionada(null)}
              disabled={isDeleting}
              className="px-4 py-2 bg-gray-300 hover:bg-gray-400 disabled:bg-gray-300 text-gray-800 font-medium rounded-lg transition"
            >
              Volver
            </button>
            <button
              onClick={handleConfirmarEliminar}
              disabled={isDeleting}
              className="px-4 py-2 bg-red-600 hover:bg-red-700 disabled:bg-red-400 text-white font-medium rounded-lg transition"
            >
              {isDeleting ? 'Eliminando...' : 'Eliminar'}
            </button>
          </div>
        </div>
      </div>
    );
  }

  // Si mostrar confirmación para cambiar
  if (imagenSeleccionada && modo === 'cambiar') {
    return (
      <div className="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4">
        <div className="bg-white rounded-lg shadow-2xl max-w-md w-full">
          {/* Header */}
          <div className="bg-blue-50 px-6 py-4 border-b border-blue-200">
            <h2 className="text-xl font-bold text-blue-800">¿Cambiar esta imagen?</h2>
            <p className="text-sm text-blue-700 mt-1">Se reemplazará con la nueva imagen que selecciones</p>
          </div>

          {/* Preview de imagen a cambiar */}
          <div className="p-6">
            <div className="mb-4 flex justify-center">
              <img
                src={imagenSeleccionada.url}
                alt="Imagen a cambiar"
                className="w-32 h-32 object-cover rounded-lg border-2 border-blue-300"
              />
            </div>
            <p className="text-center text-gray-700">Esta imagen será reemplazada</p>
          </div>

          {/* Botones */}
          <div className="bg-gray-50 px-6 py-4 border-t border-gray-200 flex gap-3 justify-end">
            <button
              onClick={() => setImagenSeleccionada(null)}
              className="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium rounded-lg transition"
            >
              Volver
            </button>
            <button
              onClick={handleConfirmarCambiar}
              className="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition"
            >
              Continuar
            </button>
          </div>
        </div>
      </div>
    );
  }

  // Vista de galería para seleccionar imagen
  return (
    <div className="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-lg shadow-2xl max-w-2xl w-full max-h-screen overflow-y-auto">
        {/* Header */}
        <div className="bg-gray-100 px-6 py-4 border-b border-gray-200 sticky top-0">
          <h2 className="text-2xl font-bold text-gray-800">
            {modo === 'eliminar' ? 'Eliminar imagen' : 'Cambiar imagen'}
          </h2>
          <p className="text-sm text-gray-600 mt-1">
            {modo === 'eliminar'
              ? 'Selecciona la imagen que deseas eliminar'
              : 'Selecciona la imagen que deseas cambiar'}
          </p>
        </div>

        {/* Galería de imágenes */}
        <div className="p-6">
          {imagenes.length === 0 ? (
            <div className="text-center py-12">
              <p className="text-gray-500 text-lg mb-2">No hay imágenes disponibles en estos momentos</p>
              <p className="text-gray-400">Añade una imagen primero si quieres editarla</p>
            </div>
          ) : (
            <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
              {imagenes.map((imagen) => (
                <button
                  key={imagen.id}
                  onClick={() => handleSeleccionarImagen(imagen)}
                  className="relative group overflow-hidden rounded-lg border-2 border-transparent hover:border-blue-500 transition-all"
                >
                  <img
                    src={imagen.url}
                    alt="Imagen publicación"
                    className="w-full h-32 object-cover group-hover:brightness-75 transition-all"
                  />
                  <div className="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all">
                    <span className="text-white font-medium text-sm bg-blue-600 px-3 py-1 rounded">
                      Seleccionar
                    </span>
                  </div>
                </button>
              ))}
            </div>
          )}
        </div>

        {/* Footer */}
        <div className="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end">
          <button
            onClick={onCancel}
            className="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium rounded-lg transition"
          >
            Cancelar
          </button>
        </div>
      </div>
    </div>
  );
}
