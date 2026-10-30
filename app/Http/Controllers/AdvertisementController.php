<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Models\Advertisement;
use Illuminate\Http\RedirectResponse;


/**
 * Conotrolador encargado de gestionar las publicaciones.
 */


class AdvertisementController extends Controller
{

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

        $latitude = $request->query('userLocation.latiude');
        $longitude = $request->query('userLocation.longitude');
        
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

        
        $rows = \DB::select('call obtener_publicaciones_cercanas_filtradas(?, ?, ?, ?, ?, ?)', [
            $filters['q'],
            $filters['type'],
            $filters['distance'],
            $filters['sort'],
            $latitude,
            $longitude
        ]);
        

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
    
        return Inertia::render('Advertisement', [
            'createdAds' => $createdAds,
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
                    'imageUrls' => $ad->image_urls ?? [],
                    'imageUrl' => !empty($ad->image_urls) ? $ad->image_urls[0] : null,
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
