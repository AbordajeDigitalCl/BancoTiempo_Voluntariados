// Servicio para manejar múltiples imágenes de publicaciones (0-5)

interface Imagen {
  id: number;
  url: string;
  Direccion?: string;
}

interface RespuestaImagenes {
  imagenes: Imagen[];
  total: number;
}

// Obtener todas las imágenes de una publicación desde el endpoint correcto
export const obtenerImagenesPublicacion = async (publicacionId: number): Promise<RespuestaImagenes> => {
  try {
    const response = await fetch(`/api/publicaciones/${publicacionId}/imagen`);
    if (!response.ok) throw new Error('Error al obtener imágenes');
    const data = await response.json();
    
    // El backend devuelve un array directo, no dentro de una propiedad "imagenes"
    // Estructura: [{ id, Direccion }, ...]
    const imagenesArray = Array.isArray(data) ? data : (data.imagenes || []);
    
    // Transformar para que tengan campo URL
    const imagenesConUrl = imagenesArray.map((img: any) => ({
      id: img.id,
      Direccion: img.Direccion,
      url: `/imagen/${img.Direccion}`, // Construir URL desde ruta web.php
    }));
    
    return { 
      imagenes: imagenesConUrl, 
      total: imagenesConUrl.length 
    };
  } catch (error) {
    console.error('Error en obtenerImagenesPublicacion:', error);
    return { imagenes: [], total: 0 };
  }
};

// Subir imagen y asociarla a una publicación
export const subirYAsociarImagenPublicacion = async (archivo: File, publicacionId: number): Promise<{ id: number; url: string }> => {
  try {
    // Obtener token CSRF fresco
    const refreshResponse = await fetch('/refresh-csrf', {
      method: 'GET',
      credentials: 'include',
    });
    const refreshData = await refreshResponse.json();
    const csrfToken = refreshData.csrf_token;

    // Usar el endpoint /subir-imagen-publicacion/{id} que está en TestControllerCris
    const formData = new FormData();
    formData.append('image', archivo);
    formData.append('_token', csrfToken);

    const uploadResponse = await fetch(`/subir-imagen-publicacion/${publicacionId}`, {
      method: 'POST',
      credentials: 'include',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: formData,
    });

    if (!uploadResponse.ok) {
      const errorText = await uploadResponse.text();
      console.error('Upload error response:', uploadResponse.status, errorText);
      throw new Error(`Error ${uploadResponse.status}: ${errorText.substring(0, 100)}`);
    }

    const uploadData = await uploadResponse.json();
    const imagenId = uploadData.id;
    const imagenDireccion = uploadData.Direccion;

    return {
      id: imagenId,
      url: `/imagen/${imagenDireccion}`,
    };
  } catch (error) {
    console.error('Error en subirYAsociarImagenPublicacion:', error);
    throw error;
  }
};

// Cambiar una imagen existente por otra
export const cambiarImagenPublicacion = async (
  archivo: File,
  publicacionId: number,
  imagenIdACambiar: number
): Promise<{ id: number; url: string }> => {
  try {
    // Obtener token CSRF fresco
    const refreshResponse = await fetch('/refresh-csrf', {
      method: 'GET',
      credentials: 'include',
    });
    const refreshData = await refreshResponse.json();
    const csrfToken = refreshData.csrf_token;

    // Primero elimina la imagen antigua para evitar error 422 cuando hay 5 imágenes y cambiar una
    try {
      const deleteResponse = await fetch(`/eliminar-imagen-publicacion/${publicacionId}/${imagenIdACambiar}`, {
        method: 'DELETE',
        credentials: 'include',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Content-Type': 'application/json',
        },
      });

      if (!deleteResponse.ok) {
        console.warn('Advertencia: No se pudo eliminar la imagen anterior');
      }
    } catch (deleteError) {
      console.warn('Advertencia: Error eliminando imagen anterior:', deleteError);
    }

    // Ahora subir la nueva imagen (después de eliminar, quedan 4 máximo, se sube 1 = máximo 5)
    const formData = new FormData();
    formData.append('image', archivo);
    formData.append('_token', csrfToken);

    const uploadResponse = await fetch(`/subir-imagen-publicacion/${publicacionId}`, {
      method: 'POST',
      credentials: 'include',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: formData,
    });

    if (!uploadResponse.ok) {
      const errorText = await uploadResponse.text();
      throw new Error(`Error ${uploadResponse.status}: ${errorText.substring(0, 100)}`);
    }

    const uploadData = await uploadResponse.json();
    const imagenNuevaId = uploadData.id;
    const imagenNuevaDireccion = uploadData.Direccion;

    // Ahora eliminar la imagen anterior
    try {
      const deleteResponse = await fetch(`/eliminar-imagen-publicacion/${publicacionId}/${imagenIdACambiar}`, {
        method: 'DELETE',
        credentials: 'include',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Content-Type': 'application/json',
        },
      });

      if (!deleteResponse.ok) {
        console.warn('Advertencia: No se pudo eliminar la imagen anterior, pero la nueva fue subida');
      }
    } catch (deleteError) {
      console.warn('Advertencia: Error eliminando imagen anterior:', deleteError);
    }

    return {
      id: imagenNuevaId,
      url: `/imagen/${imagenNuevaDireccion}`,
    };
  } catch (error) {
    console.error('Error en cambiarImagenPublicacion:', error);
    throw error;
  }
};
