<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Models\Advertisement;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Traits\Cruds;


/**
 * Conotrolador encargado de gestionar las publicaciones.
 */


class AdvertisementController extends Controller
{

    use Cruds;

    /**
     * Muestra el listado de publicaciones disponibles.
     *
     * - Procesa y normaliza los filtros de búsqueda recibidos en la petición.
     * - Obtiene las coordenadas del usuario para filtrar publicaciones por ubicación.
     * - Consulta el procedimiento almacenado `obtener_publicaciones_cercanas_filtradas`,
     *   que aplica los filtros y devuelve las publicaciones correspondientes.
     * - Transforma los resultados en un formato adecuado para el frontend.
     * - Renderiza la página `Advertisement` mediante Inertia, enviando las publicaciones,
     *   los filtros aplicados y el estado administrativo de la sesión.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con los filtros de búsqueda.
     * @return \Inertia\Response
     */
    
    public function advertisementIndex(Request $request): Response
    {


        $filters = $this->parseAdvertisementFilters($request);

        //$latitude = $request->query('userLocation.latiude');
        //$longitude = $request->query('userLocation.longitude');
        
        $latitude = -33.4372;
        $longitude = -70.6506;

        $isAdmin = (bool) $request->session()->get('is_admin', false);
    
        // Se ejecuta el stored procedure `obtener_publicaciones` pasando los 4 filtros
        // ya sanitizados por parseAdvertisementFilters(). El SP hace todo el trabajo de
        // WHERE (búsqueda por texto, tipo, distancia/modalidad) y ORDER BY (newest/oldest)
        // directamente en MySQL, devolviendo solo las filas que corresponden.
        /*
        $rows = \DB::select('call obtener_publicaciones_cercanas(?, ?, ?, ?)', [
            $filters['q'],
            $filters['type'],
            $filters['distance'],
            $filters['sort'],
        ]);
        */

        /*
        $rows = \DB::select('call obtener_publicaciones_cercanas_filtradas(?, ?, ?, ?, ?, ?)', [
            $filters['q'],
            $filters['type'],
            $filters['distance'],
            $filters['sort'],
            $latitude,
            $longitude
        ]);
        */

        $perPage = 10;
        
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $offset = ($currentPage - 1) * $perPage;

        $rows = \DB::select(
            'CALL obtener_publicaciones_cercanas_filtradas_paginadas(?,?,?,?,?,?,?,?)',
            [
                $filters['q'],
                $filters['type'],
                $filters['distance'],
                $filters['sort'],
                $latitude,
                $longitude,
                $perPage,
                $offset
            ]
        );

        $totalPublicaciones = \DB::selectOne(
            'CALL contar_publicaciones_cercanas_filtradas(?,?,?,?,?,?)', 
            [
                $filters['q'],
                $filters['type'],
                $filters['distance'],
                $filters['sort'],
                $latitude,
                $longitude
            ])->total;

        $query = Advertisement::hydrate($rows);
    


        $createdAds = $query->map(function ($ad) {
            return [
                'id' => $ad->id,
                'title' => $ad->title,
                'description' => $ad->description,
                'hours' => $ad->hours,
                'type' => $ad->type,
                'mode' => $ad->mode,
                'status' => $ad->status,
                'author' => $ad->author,
                'user_id' => $ad->user_id,
                'imageUrls' => $ad->image_urls ?? [],
                'imageUrl' => !empty($ad->image_urls) ? $ad->image_urls[0] : null,
                'location_id' => $ad->location_id,
                'location_lat' => $ad->location_lat,
                'location_lng' => $ad->location_lng,
                'location_address' => $ad->location_address,
                'created_at' => optional($ad->created_at)?->toDateTimeString(),
                'updated_at' => optional($ad->updated_at)?->toDateTimeString(),
            ];
        })->toArray();

        $publicacionesPaginadas = new LengthAwarePaginator(
            $createdAds,
            $totalPublicaciones,
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'query' => request()->query()
            ]
        );
    
        return Inertia::render('Advertisement', [
            'createdAds' => $publicacionesPaginadas,
            'filters' => $filters,
            'isAdmin' => $isAdmin,
        ]);
    }
    
    /**
     * @summary Obtiene y valida los filtros de búsqueda de publicaciones.
     * @description Lee los filtros de la URL y sustituye los valores no permitidos
     * por valores predeterminados para garantizar que tengan un formato esperado.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con los filtros.
     * @return array{q:string,type:string,sort:string,distance:string}
     */
    private function parseAdvertisementFilters(Request $request): array
    {
        $q = (string) $request->query('q', '');
        $type = (string) $request->query('type', 'all');
        $sort = (string) $request->query('sort', 'newest');
        $distance = (string) $request->query('distance', 'Cualquiera');
 
        if (!in_array($type, ['all', 'Servicio', 'Producto'], true)) $type = 'all';
        if (!in_array($sort, ['newest', 'oldest'], true)) $sort = 'newest';
        if (!in_array($distance, ['Cualquiera', '0', '1', '5', '10', '20', '30', '50'], true)) $distance = 'Cualquiera';
        return [
            'q' => $q,
            'type' => $type,
            'sort' => $sort,
            'distance' => $distance,
        ];
    }

    /**
     * @summary Muestra las publicaciones del usuario autenticado
     * @description Obtiene las publicaciones asociadas al usuario que inició sesión
     * las ordena por fecha de creación descendente, aplica paginación de 10 registros
     * y trasnforma los resultados al formato esperado por el frontend.
     * 
     * @source Modelo Advertisement (tabla publicacions).
     * @view resources/js/Pages/MyAdvertisements.tsx
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function myAdvertisementsIndex(Request $request): Response
    {
        $user_id = $request->user()->id;

        //Para esta query se creó el index:  idx_publicacions_usuario_created, ya que sin ella
        //la consulta arroja un error al ordenar las filas con latest().
        $createdAds = Advertisement::query()
            ->where('id_usuario', $user_id)
            ->with('images:id,Direccion')
            ->latest()
            ->paginate(10) 
            ->through(function ($ad) {
                return [
                    'id' => $ad->id,
                    'title' => $ad->title,
                    'description' => $ad->description,
                    'hours' => $ad->hours,
                    'type' => $ad->type,
                    'mode' => $ad->mode,
                    'status' => $ad->status,
                    'author' => $ad->author,
                    'user_id' => $ad->user_id,
                    //Este atributo almacenará las imágenes asociadas
                    //con la respectiva publicación
                    //'imageUrls' => $ad->image_urls ?? [],
                    'imageUrls' => $ad->images->map(function ($image) {
                        return url('imagen/' . basename($image->Direccion));
                    })->toArray(),

                    //'imageUrl' => !empty($ad->image_urls) ? $ad->image_urls[0] : null,
                    'imageUrl' => $ad->images->isNotEmpty()
                    ? url('imagen/' . basename($ad->images->first()->Direccion)) : null,

                    'location_id' => $ad->location_id,
                    'location_lat' => $ad->location_lat,
                    'location_lng' => $ad->location_lng,
                    'location_address' => $ad->location_address,
                    'created_at' => optional($ad->created_at)?->toDateTimeString(),
                    'updated_at' => optional($ad->updated_at)?->toDateTimeString(),
                ];
            })
            ->toArray();
        
 
        return Inertia::render('MyAdvertisements', [
            'createdAds' => $createdAds,
        ]);
    }


    /**
     * @summary Muestra el detalle de una publicación
     * @description Busca una publicación por id y transforma sus datos al
     * formato esperado por el frontend. Si no existe, envía item con valor null.
     * 
     * @source Modelo Advertisement (tabla publicacions)
     * @view resources/js/Pages/AdvertisementShow.tsx
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id identificador de la publicación
     * @return \Inertia\Response item=null si no existe.
     */
    public function advertisementShow(Request $request, int $id): Response
    {
        $ad = Advertisement::query()
        ->with('images:id,Direccion')
        ->find($id);
 
        $item = $ad ? [
            'id' => $ad->id,
            'title' => $ad->title,
            'description' => $ad->description,
            'hours' => $ad->hours,
            'type' => $ad->type,
            'mode' => $ad->mode,
            'status' => $ad->status,
            'author' => $ad->author,
            'user_id' => $ad->user_id,
            
            //Este atributo almacenará las imágenes asociadas
            //con la respectiva publicación
            //'imageUrls' => $ad->image_urls ?? [],
            'imageUrls' => $ad->images->map(function ($image) {
                return url('imagen/' . basename($image->Direccion));
            })->toArray(),


            //'imageUrl' => !empty($ad->image_urls) ? $ad->image_urls[0] : null,
            'imageUrl' => $ad->images->isNotEmpty()
                    ? url('imagen/' . basename($ad->images->first()->Direccion)) : null,

            'location_id' => $ad->location_id,
            'location_lat' => $ad->location_lat,
            'location_lng' => $ad->location_lng,
            'location_address' => $ad->location_address,
            'created_at' => optional($ad->created_at)?->toDateTimeString(),
            'updated_at' => optional($ad->updated_at)?->toDateTimeString(),
        ] : null;
 
        return Inertia::render('AdvertisementShow', [
            'item' => $item,
        ]);
    }

    /*
     |--------------------------------------------------------------------------
     | PUBLICACIÓN (EDITAR FORM)
     |--------------------------------------------------------------------------
     | - Vista: resources/js/Pages/EditarPublicacion.tsx
     | - Fuente: tabla `publicacions` mediante modelo Advertisement
     | - Guardado real: PATCH /Advertisement/{id} en routes/web.php
     */
 
    /**
     * @summary Formulario de edición de publicación.
     * @description Carga una publicación por id desde base de datos para editarla en el frontend.
     *
     * @source tabla `advertisements`
     * @view resources/js/Pages/EditarPublicacion.tsx
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Inertia\Response item=null si no existe.
     */
    public function advertisementEdit(Request $request, int $id): Response
    {
        $ad = Advertisement::query()->find($id);
 
        $item = $ad ? [
            'id' => $ad->id,
            'title' => $ad->title,
            'description' => $ad->description,
            'hours' => $ad->hours,
            'type' => $ad->type,
            'mode' => $ad->mode,
            'status' => $ad->status,
            'author' => $ad->author,
            'user_id' => $ad->user_id,
            'imageUrls' => $ad->image_urls ?? [],
            'imageUrl' => !empty($ad->image_urls) ? $ad->image_urls[0] : null,
            'location_id' => $ad->location_id,
            'location_lat' => $ad->location_lat,
            'location_lng' => $ad->location_lng,
            'location_address' => $ad->location_address,
            'created_at' => optional($ad->created_at)?->toDateTimeString(),
            'updated_at' => optional($ad->updated_at)?->toDateTimeString(),
        ] : null;
 
        return Inertia::render('EditarPublicacion', [
            'item' => $item,
        ]);
    }

    /**
     * @summary Actualiza una publicación existente.
     * @description Valida los datos recibidos del formulario, comprueba que el
     * usuario autenticado tenga permisos para modificar la publicación, procesa
     * las imágenes y actualiza sus datos en la base de datos. Finalmente,
     * redirige al listado de publicaciones del usuario.
     * 
     * @source Modelo Advertisement (tabla publicacions)
     * @route PATCH /Advertisement/{id}
     * @param \Illuminate\Http\Request $request
     */
    public function advertisementUpdate(Request $request, int $id): RedirectResponse {
        $isAdmin = (bool) $request->session()->get('is_admin', false);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:80'],
            'description' => ['required', 'string', 'max:1000'],
            'hours' => ['required', 'integer', 'min:1', 'max:999'],
            'type' => ['required', 'in:Servicio,Producto'],
            'mode' => ['required', 'in:Oferta,Demanda'],
            'imageUrls' => ['nullable', 'array', 'max:5'],
            'imageUrls.*' => ['nullable', 'string'],
            'location_id' => ['nullable', 'integer'],
            'location_lat' => ['nullable', 'numeric', 'min:-90', 'max:90'],
            'location_lng' => ['nullable', 'numeric', 'min:-180', 'max:180'],
            'location_address' => ['nullable', 'string', 'max:500'],
        ]);

        $ad = Advertisement::find($id);
        if (!$ad) {
            return redirect()->route('myadvertisements');
        }

        if (
            !$isAdmin &&
            (int) ($ad->user_id ?? 0) !== (int) ($request->user()->id ?? 0) &&
            (string) ($ad->author ?? '') !== (string) ($request->user()->name ?? '')
        ) {
            return redirect()->route('myadvertisements');
        }

        $imgs = $data['imageUrls'] ?? [];
        $imgs = array_values(array_filter($imgs, fn ($x) => is_string($x) && trim($x) !== ''));
        $imgs = array_slice($imgs, 0, 5);

        
        $ad->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'hours' => (int) $data['hours'],
            'type' => $data['type'],
            'mode' => $data['mode'],
            'image_urls' => $imgs,
            'location_id' => array_key_exists('location_id', $data) ? $data['location_id'] : null,
            'location_lat' => array_key_exists('location_lat', $data) ? $data['location_lat'] : null,
            'location_lng' => array_key_exists('location_lng', $data) ? $data['location_lng'] : null,
            'location_address' => array_key_exists('location_address', $data) ? $data['location_address'] : null,
        ]);

        return redirect()->route('myadvertisements');   
    }

    /**
     * @summary Muestra el formulario de creación de publicación.
     * @description Comprueba si el usuario tiene el indicador de 
     * administrador en la sesión. Si es administrador, obtiene la
     * lista de usuarios (id y nombre) para permitir la asignación de
     * la publicación. Finalmente, carga el formulario de creación
     * en el frontend.
     *
     * @source session('is_admin') y modelo User (tabla users)
     * @view resources/js/Pages/CreateAdvertisement.tsx
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function advertisementCreate(Request $request): Response
    {
        $isAdmin = (bool) $request->session()->get('is_admin', false);
 
        $users = [];
 
        if ($isAdmin) {
            $users = User::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get()
                ->toArray();
        }
 
        return Inertia::render('CreateAdvertisement', [
            'users' => $users,
        ]);
    }

    /**
     * @summary Sube una imagen y la asocia a una publicación existente.
     * @description
     * - Obtiene al usuario autenticado y verifica que haya iniciado sesión.
     * - Comprueba los permisos de acceso a la publicación:
     *   - Los usuarios normales únicamente pueden subir imágenes a sus propias publicaciones.
     *   - Los administradores pueden subir imágenes a cualquier publicación.
     * - Verifica que la publicación exista y que el usuario tenga permisos para modificarla.
     * - Valida que se haya proporcionado un archivo de imagen con un formato permitido (JPEG, PNG, JPG, GIF o SVG) y un tamaño máximo de 2 MB.
     * - Guarda físicamente el archivo en el directorio `storage/app/uploads`, utilizando un nombre de archivo único.
     * - Registra la imagen mediante el método `saveImagen()` y establece su tipo como imagen de publicación (`Tipo = 1`).
     * - Consulta el número de imágenes asociadas a la publicación mediante el procedimiento almacenado `contar_imagenes_publicacion_por_tipo`.
     * - Comprueba que la publicación no supere el límite de cinco imágenes. Si se alcanza el límite, elimina el archivo y su registro de la base de datos.
     * - Crea una asociación entre la publicación y la imagen en la tabla `inter__imagen__public`.
     * - Registra información de la operación en el log de Laravel.
     * - Devuelve los datos de la imagen, su URL de acceso y un token CSRF actualizado en formato JSON.
     *
     * @source
     * - Tabla `publicacions`: identificación y propiedad de la publicación.
     * - Tabla `imagens`: almacenamiento de los registros de imágenes.
     * - Tabla `inter__imagen__public`: asociación entre publicaciones e imágenes.
     * - Procedimiento almacenado `contar_imagenes_publicacion_por_tipo`: contabilización de imágenes de publicación.
     * - Método `saveImagen()`: registro de la imagen en la base de datos.
     *
     * @route Definida en routes/web.php.
     * @frontend
     * - actualizarCSRFToken.ts
     * - estadosPublicaciones.ts
     * - subirImagenPublicacion.ts
     * - servicioImagenesPublicacion.ts
     * - FormEditarMiPublicacionCRIS.tsx
     * - EditarMiPublicacionCRIS.tsx
     * - AjustadorImagenPublicacion.tsx
     *
     * @param Request $request Petición HTTP que contiene el archivo de imagen.
     * @param int $publicacion_id Identificador de la publicación a la que se asociará la imagen.
     * @return \Illuminate\Http\JsonResponse Datos de la imagen guardada, su URL y el token CSRF actualizado.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Si el usuario no está autenticado (401) o no tiene permisos para modificar la publicación (403).
     * @throws \Illuminate\Validation\ValidationException Si el archivo no cumple las reglas de validación.
     */
    public function subirImagenPublicacion(Request $request, $publicacion_id)
    {
        $usuarioActual = $request->user();
        if (!$usuarioActual) {
            abort(401, 'No autenticado');
        }

        $isAdmin = (bool) $request->session()->get('is_admin', false);
        
        $query = \DB::table('publicacions')
            ->where('id', $publicacion_id);
        
        // Solo usuarios normales solo pueden subir imágenes a sus propias publicaciones
        // Los admins pueden subir a cualquier publicación
        if (!$isAdmin) {
            $query = $query->where('id_usuario', $usuarioActual->id);
        }
        
        $publicacion = $query->first();

        if (!$publicacion) {
            abort(403, 'No tienes permiso para subir imágenes a esta publicación');
        }

        
        $validated = $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Guardar la imagen en storage
        $image = $request->file('image');
        $filename = uniqid() . '.' . $image->extension();
        
        try {
            
            $uploadDir = storage_path('app/uploads');
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }
            
            $rutaOriginal = $image->getRealPath();

            \Log::info('Antes de mover', [
                'ruta_original'=>$rutaOriginal,
                'existe_original'=> file_exists($rutaOriginal),
            ]);

            $hashOriginal = hash_file('sha256', $rutaOriginal);

            $image->move($uploadDir, $filename);

            $rutaGuardada = $uploadDir . DIRECTORY_SEPARATOR . $filename;

            \Log::info('Después de mover', [
                'ruta_guardada'=> $rutaGuardada,
                'existe_guardada'=> file_exists($rutaGuardada),
            ]);
            
            $hashGuardado = hash_file('sha256', $rutaGuardada);

            \Log::info('Comparación de integridad de imagen', [
            'hash_original' => $hashOriginal,
            'hash_guardado' => $hashGuardado,
            'identicos' => $hashOriginal === $hashGuardado,
            ]);

            \Log::info('Imagen guardada exitosamente', [
                'filename' => $filename,
                'path' => $uploadDir . DIRECTORY_SEPARATOR . $filename,
                'exists' => file_exists($uploadDir . DIRECTORY_SEPARATOR . $filename),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error al guardar imagen', [
                'error' => $e->getMessage(),
                'filename' => $filename,
                'uploadDir' => $uploadDir ?? 'undefined',
            ]);
            return response()->json(['error' => 'Error al guardar imagen: ' . $e->getMessage()], 500);
        }

        
        $imagenGuardada = $this->saveImagen($filename, $usuarioActual->id);

        // Cambiar Tipo a 1 (imagen de publicación)
        \DB::table('imagens')
            ->where('id', $imagenGuardada->id)
            ->update(['Tipo' => 1]);

        // Verificar cuántas imágenes ya tiene esta publicación, máximo 5.
        /*
        $conteoImagenes = \DB::table('inter__imagen__public')
            ->where('id_publicacion', $publicacion_id)
            ->join('imagens', 'inter__imagen__public.id_imagen', '=', 'imagens.id')
            ->where('imagens.Tipo', 1)  // Solo buscar imágenes de tipo publicación
            ->count();
        */
        $conteoImagenes = \DB::selectOne(
            'CALL contar_imagenes_publicacion_por_tipo(?)',
            [$publicacion_id]
        );
        
        $conteoImagenesTotal = (int) $conteoImagenes->conteo_imagenes;

        if ($conteoImagenesTotal >= 5) {
           
            \Storage::disk('uploads')->delete($filename);
            \DB::table('imagens')->where('id', $imagenGuardada->id)->delete();
            
            return response()->json(['error' => 'La publicación ya tiene el máximo de imágenes (5)'], 422);
        }

        // Crear nueva asociación en `inter__imagen__public` sin borrar las anteriores
        
        \DB::table('inter__imagen__public')->insert([
            'id_publicacion' => $publicacion_id,
            'id_imagen' => $imagenGuardada->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        \Log::info('Nueva imagen agregada a publicacion', [
            'id_publicacion' => $publicacion_id,
            'id_imagen' => $imagenGuardada->id,
            'total_imagenes' => $conteoImagenesTotal + 1,  
        ]);

        // Devolver imagen + CSRF token actualizado
        $response = [
            'id' => $imagenGuardada->id,
            'Direccion' => $imagenGuardada->Direccion,
            'imagenUrl' => '/imagen/' . $imagenGuardada->Direccion, 
            'csrf_token' => csrf_token(),
        ];

        return response()->json($response);
    }

    /**
     * Desasocia una imagen de una publicación sin eliminar el archivo físico.
     *
     * - Obtiene el usuario autenticado y comprueba que haya iniciado sesión.
     * - Identifica si el usuario tiene privilegios de administrador.
     * - Verifica que la publicación exista y que el usuario tenga permiso
     *   para modificarla. Los administradores pueden modificar cualquier publicación.
     * - Comprueba que la imagen esté asociada a la publicación y que corresponda
     *   al tipo de imagen destinado a publicaciones (Tipo = 1).
     * - Elimina la relación entre la imagen y la publicación en
     *   inter__imagen__public.
     * - Registra la operación en el log de la aplicación.
     * - Devuelve una respuesta JSON indicando que la imagen fue desasociada.
     *
     * Importante:
     * Esta operación no elimina el archivo físico almacenado en storage/app/uploads/
     * ni elimina su registro de la tabla imagens. Únicamente elimina la asociación
     * con la publicación.
     *
     * Tablas involucradas:
     * - publicacions: verifica la existencia y propiedad de la publicación.
     * - inter__imagen__public: elimina la asociación entre publicación e imagen.
     * - imagens: verifica que la imagen corresponda a una imagen de publicación.
     *
     * Archivos relacionados:
     * - routes/web.php: definición de la ruta.
     * - servicioImagenesPublicacion.ts: servicio para gestionar imágenes de publicaciones.
     * - eliminarImagenPublicacion.tsx: lógica de eliminación de imágenes.
     * - CambiarEliminarImagenPublicacion.tsx: componente de interacción.
     * - FormEditarMiPublicacionCRIS.tsx y EditarMiPublicacionCRIS.tsx:
     *   componentes relacionados con la edición de publicaciones.
     *
     * @param \Illuminate\Http\Request $request Solicitud del usuario autenticado.
     * @param int $publicacion_id Identificador de la publicación.
     * @param int $imagen_id Identificador de la imagen que se desea desasociar.
     *
     * @return \Illuminate\Http\JsonResponse
     *         Devuelve un mensaje de confirmación con estado HTTP 200.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Si el usuario
     *         no está autenticado (401) o no tiene permisos (403).
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *         Si la imagen no está asociada a la publicación (404).
     */
    public function eliminarImagenPublicacion(Request $request, $publicacion_id, $imagen_id)
    {
        $usuarioActual = $request->user();
        if (!$usuarioActual) {
            abort(401, 'No autenticado');
        }

        $isAdmin = (bool) $request->session()->get('is_admin', false);
        
        $query = \DB::table('publicacions')
            ->where('id', $publicacion_id);
        
        // Solo usuarios normales solo pueden eliminar imágenes de sus propias publicaciones
        // Los admins pueden eliminar de cualquier publicación
        if (!$isAdmin) {
            $query = $query->where('id_usuario', $usuarioActual->id);
        }
        
        $publicacion = $query->first();

        if (!$publicacion) {
            abort(403, 'No tienes permiso para modificar esta publicación');
        }

        
        $asociacion = \DB::table('inter__imagen__public')
            ->where('id_publicacion', $publicacion_id)
            ->where('id_imagen', $imagen_id)
            ->join('imagens', 'inter__imagen__public.id_imagen', '=', 'imagens.id')
            ->where('imagens.Tipo', 1)  // Solo imágenes de publicación
            ->first();

        if (!$asociacion) {
            abort(404, 'Imagen no encontrada en esta publicación');
        }

        
        \DB::table('inter__imagen__public')
            ->where('id_publicacion', $publicacion_id)
            ->where('id_imagen', $imagen_id)
            ->delete();

        \Log::info('Imagen desasociada de publicación', [
            'publicacion_id' => $publicacion_id,
            'imagen_id' => $imagen_id,
            'usuario_id' => $usuarioActual->id,
        ]);

        return response()->json(['message' => 'Imagen eliminada correctamente'], 200);
    }

    /**
     * @summary Obtiene los datos de una publicación y muestra el formulario para editarla.
     * @description
     * - Obtiene al usuario autenticado y verifica si tiene privilegios de administrador mediante la sesión.
     * - Recupera los datos de la publicación mediante el procedimiento almacenado `obtener_publicacion_para_editar`.
     * - Comprueba los permisos de acceso a la publicación:
     *   - Los usuarios normales únicamente pueden editar sus propias publicaciones.
     *   - Los administradores pueden editar cualquier publicación.
     * - Devuelve un error 404 si la publicación no existe o el usuario no tiene permisos para editarla.
     * - Determina si la publicación corresponde a una Oferta o una Demanda mediante la información obtenida de las tablas `ofertas` y `demandas`.
     * - Obtiene el valor económico correspondiente: `Precio` para las ofertas o `Presupuesto` para las demandas.
     * - Convierte los valores numéricos de `Tipo_Beneficio` y `Tipo_Publicacion` a las denominaciones esperadas por el frontend (Servicio/Producto y Oferta/Demanda).
     * - Obtiene la URL de la imagen asociada a la publicación. Si no existe una imagen, utiliza una imagen placeholder de Picsum.
     * - Organiza los datos de la publicación en un objeto con la estructura esperada por el formulario de edición.
     * - Recupera el parámetro `return_to` de la URL para determinar a qué vista regresar después de editar la publicación. Utiliza `/verperfil` como destino predeterminado.
     * - Renderiza el componente `EditarMiPublicacionCRIS` mediante Inertia, enviándole los datos de la publicación y la ruta de retorno.
     *
     * @source
     * - Tabla `publicacions`: información general de la publicación, propietario y tipo de beneficio.
     * - Tablas `ofertas` y `demandas`: obtención del precio o presupuesto según el tipo de publicación.
     * - Tabla `imagens`: obtención de la dirección y el identificador de la imagen asociada.
     * - Tabla `inter__imagen__public`: relación entre publicaciones e imágenes.
     * - Procedimiento almacenado `obtener_publicacion_para_editar`: recuperación de los datos de la publicación y aplicación de las restricciones de acceso.
     *
     * @route Definida en routes/web.php.
     * @frontend
     * - estadosPublicaciones.ts
     * - EditarMiPublicacionCRIS.tsx
     * - FormEditarMiPublicacionCRIS.tsx
     * - EditarPublicacion.tsx
     * - guardarPublicacion.ts
     * - servicioImagenesPublicacion.ts
     * - subirImagenPublicacion.ts
     * - MyAdvertisements.tsx
     * - VerPerfil.tsx
     * - AuthenticatedLayout.tsx
     * - usarFiltroServicio.ts
     * - SeleccionarPublicacionEditarCRIS.tsx
     * - ServicioProductoCard.tsx
     * - ServicioProductoCardSelectable.tsx
     * - ServiceCardListview.tsx
     * - ServiceCardListviewSelectable.tsx
     *
     * @views
     * - Editar publicación.
     * - Ver perfil (enlace para acceder a la edición de una publicación).
     */
    public function editarPublicacion(Request $request, $id)
    {
        $user = $request->user();
        $isAdmin = (bool) $request->session()->get('is_admin', false);
        $returnTo = $request->query('return_to', '/verperfil');
        
        /*
        // Subquery para obtener la primera imagen de la publicación
        $imagenSubquery = \DB::table('inter__imagen__public')
            ->join('imagens', 'inter__imagen__public.id_imagen', '=', 'imagens.id')
            ->where('imagens.Tipo', 1)
            ->selectRaw('inter__imagen__public.id_publicacion, MAX(imagens.Direccion) as Direccion, MAX(imagens.id) as id')
            ->groupBy('inter__imagen__public.id_publicacion');

        $publicacion = \App\Models\Publicacion::where('publicacions.id', $id);
        
        // Solo usuarios normales solo pueden editar sus propias publicaciones
        // Los admins pueden editar cualquier publicación
        if (!$isAdmin) {
            $publicacion = $publicacion->where('id_usuario', $user->id);
        }
        
        $publicacion = $publicacion
            ->leftJoin('ofertas', 'publicacions.id', '=', 'ofertas.id_publicacion')
            ->leftJoin('demandas', 'publicacions.id', '=', 'demandas.id_publicacion')
            ->leftJoinSub($imagenSubquery, 'imagen_primera', function($join) {
                $join->on('publicacions.id', '=', 'imagen_primera.id_publicacion');
            })
            ->select(
                'publicacions.id',
                'publicacions.Nombre',
                'publicacions.Descripcion',
                'publicacions.Tipo_Publicacion',
                'publicacions.Tipo_Beneficio',
                'ofertas.Precio',
                'demandas.Presupuesto',
                'imagen_primera.Direccion as imagenDireccion',
                'imagen_primera.id as imagenId'
            )
            ->first();
        */

        $publicacion = collect(\DB::select(
            'CALL obtener_publicacion_para_editar(?,?,?)',
            [$id,
            $user->id,
            $isAdmin ? 1 : 0
            ]
        ))
        ->first();

        if (!$publicacion) {
            abort(404);
        }

        // CORREGIDO: Determinar Tipo_Publicacion consultando las tablas
        // Tipo_Publicacion: 0=Oferta (Precio), 1=Demanda (Presupuesto)
        $esOferta = $publicacion->Precio !== null;
        $tipoPublicacionCalculado = $esOferta ? 0 : 1;
        $saldo = $esOferta ? ($publicacion->Precio ?? 0) : ($publicacion->Presupuesto ?? 0);
        
        // Convertir números a strings de tipo para el componente frontend
        $tipoBeneficioText = (int) $publicacion->Tipo_Beneficio === 0 ? 'Servicio' : 'Producto';
        $tipoOfertaDemandaText = $esOferta ? 'Oferta' : 'Demanda';

        // NUEVO: Obtener URL de imagen real o usar placeholder
        // Usar endpoint alternativo /imagen/ para mayor compatibilidad
        $imagenUrl = $publicacion->imagenDireccion 
            ? '/imagen/' . $publicacion->imagenDireccion
            : 'https://picsum.photos/200?random=' . $publicacion->id;
            
        //Traduce los valores de status de la tabla publicacions en
        //cadenas de texto en Español
        $determinarEstado = fn($status) => match ($status) {
            'active' => 'Activo',
            'paused' => 'Pausado',
            'finished' => 'Finalizado',
            'deleted' => 'Borrado',
            default => 'Desconocido',
        };
        dd($publicacion);
            $datosPublicacion = [
            'id' => $publicacion->id,
            'titulo' => $publicacion->Nombre,
            'descripcion' => $publicacion->Descripcion,
            'costo' => $saldo,
            'tipo' => $tipoBeneficioText, 
            'ofertaDemanda' => $tipoOfertaDemandaText, 
            'imagen' => $imagenUrl,
            'imagenId' => $publicacion->imagenId, 
            //'estado' => 'Activo',
            'estado' => $determinarEstado($publicacion->status),
            'tipoPublicacion' => $tipoPublicacionCalculado, 
            'tipoBeneficio' => (int) $publicacion->Tipo_Beneficio, 
            'Precio' => $esOferta ? $saldo : null,
            'Presupuesto' => !$esOferta ? $saldo : null,
        ];

        return Inertia::render('EditarMiPublicacionCRIS', [
            'datosPublicacion' => $datosPublicacion,
            'returnTo' => $returnTo,
        ]);
    }


    /**
     * Los siguientes métodos se encontraban en el controlador previo a la inclusión de los demás
     * métodos: index(Request $request), my(Request $request), showw(Request $request),
     * create(Request $request, $id), create(Request $request), store(Request $request), status(Request $request, $id),
     * destroy(Request $request, $id).
     */
    public function index(Request $request)
    {
        $createdAds = session('created_ads', []);

        return Inertia::render('Advertisement', [
            'createdAds' => $createdAds,
        ]);
    }

    public function my(Request $request)
    {
        $createdAds = session('created_ads', []);

        return Inertia::render('MyAdvertisements', [
            'createdAds' => $createdAds,
        ]);
    }

    public function show(Request $request, $id)
    {
        $createdAds = session('created_ads', []);

        $ad = null;
        foreach ($createdAds as $item) {
            if ((string)($item['id'] ?? '') === (string)$id) {
                $ad = $item;
                break;
            }
        }

        if (!$ad) {
            abort(404);
        }

        return Inertia::render('AdvertisementShow', [
            'ad' => $ad,
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('CreateAdvertisement');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:2000'],
            'hours' => ['required', 'integer', 'min:1'],
            'type' => ['required', 'in:Servicio,Producto'],
            'mode' => ['required', 'in:Oferta,Demanda'],
            'images' => ['nullable', 'array'],
            'images.*' => ['file', 'image', 'max:4096'],
        ]);

        $imageUrls = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('ads', 'public');
                $imageUrls[] = Storage::url($path);
            }
        }

        $createdAds = session('created_ads', []);

        $newAd = [
            'id' => now()->timestamp . rand(10, 99),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'hours' => (int)$validated['hours'],
            'type' => $validated['type'],
            'mode' => $validated['mode'],
            'status' => 'active',
            'author' => $request->user()->name,
            'imageUrl' => $imageUrls[0] ?? null,
            'images' => $imageUrls,
        ];

        $createdAds[] = $newAd;
        session(['created_ads' => $createdAds]);

        return redirect()->route('myadvertisements');
    }

    public function status(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'in:active,paused,finished'],
        ]);

        $createdAds = session('created_ads', []);
        $updated = [];

        foreach ($createdAds as $ad) {
            if ((string)($ad['id'] ?? '') === (string)$id) {
                if (($ad['author'] ?? null) === $request->user()->name) {
                    $ad['status'] = $request->input('status');
                }
            }
            $updated[] = $ad;
        }

        session(['created_ads' => $updated]);

        return back();
    }

    public function destroy(Request $request, $id)
    {
        $createdAds = session('created_ads', []);

        $createdAds = array_values(array_filter($createdAds, function ($ad) use ($id, $request) {

            if (($ad['author'] ?? null) !== $request->user()->name) return true;
            return (string)($ad['id'] ?? '') !== (string)$id;
        }));

        session(['created_ads' => $createdAds]);

        return back();
    }

    



}
