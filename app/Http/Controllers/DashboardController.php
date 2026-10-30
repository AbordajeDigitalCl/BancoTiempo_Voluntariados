<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * @summary Muestra el dashboard del usuario
     * @description Obtiene las publicaciones asociadas al usuario
     * autenticado, transforma sus datos al formato esperado
     * por el frontend y calcula las estadísticas del dashboard
     * mediante buildDashboardStats()
     * Finalmente, envía las estadísticas y los contadores de mensajes
     * y notificaciones sin leer al componente Dashboard.
     * 
     * @source Modelo Advertisement (tabla publicacions) y
     * usuario autenticado.
     * @view resources/js/Pages/Dashboard.tsx
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function dashboard(Request $request): Response
    {

        $user_id = $request->user()->id;
        
        //Se agregó filtro por id_usuario, ya que previamente se obtenían las publicaciones
        //de todos los usuarios
        $createdAds = Advertisement::query()
        ->where('id_usuario', $user_id)
        ->get()
        ->map(function ($ad) {
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
 
        $stats = $this->buildDashboardStats($request, $createdAds);
 
        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'unreadMessageCount' => $stats['badges']['unreadMessages'] ?? 0,
            'unreadNotificationCount' => $stats['badges']['unreadNotifications'] ?? 0,
        ]);
    }

    /**
     * @summary Calcula estadísticas del Dashboard.
     * @description
     * - Normaliza los estados de las publicaciones, identifica las
     * pertenecientes al usuario autenticado y calcula los totales
     * por estado.
     * También obtiene la cantidad de mensajes sin leer del usuario y prepara
     * los contadores que se mostrarán en el Dashboard.
     * 
     * @notes
     * - Los estados permitidos son: active, paused y finished.
     * - La identificación de las publicaciones utiliza user_id cuando
     *   está disponible. En su ausencia, utiliza author.
     * - El contador de notificaciones sin leer permanece como placeholder.
     * - Los datos usados en el cálculo provienen de base de datos.
     * @source Datos de publicaciones recibidos en $createdAds y tabla
     * mensajes.
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $createdAds Publicaciones utilizadas para calcular
     * las estadísticas.
     * @return array<string,mixed>
     */
    private function buildDashboardStats(Request $request, array $createdAds): array
    {
        $user = $request->user();
 
        $normalizeStatus = function ($x) {
            $s = $x['status'] ?? 'active';
            return in_array($s, ['active', 'paused', 'finished'], true) ? $s : 'active';
        };
 
        $ads = array_map(function ($x) use ($normalizeStatus) {
            $x['status'] = $normalizeStatus($x);
            $x['author'] = $x['author'] ?? null;
            $x['user_id'] = $x['user_id'] ?? null;
            return $x;
        }, $createdAds);
 
        $myAds = array_values(array_filter($ads, function ($x) use ($user) {
            if (!$user) return false;
 
            if (isset($x['user_id']) && $x['user_id'] !== null) {
                return (int)$x['user_id'] === (int)$user->id;
            }
 
            if (isset($x['author']) && $x['author'] !== null) {
                return (string)$x['author'] === (string)$user->name;
            }
 
            return false;
        }));
 
        $countByStatus = function (array $list, string $status) {
            $c = 0;
            foreach ($list as $x) {
                if (($x['status'] ?? 'active') === $status) $c++;
            }
            return $c;
        };
 
        $myTotal = count($myAds);
        $myAvailable = $countByStatus($myAds, 'active');
        $mySoldOut = $countByStatus($myAds, 'finished');
        $myDisabled = $countByStatus($myAds, 'paused');
 
        $adsActiveTotal = $countByStatus($ads, 'active');

        $unreadMessageCount = 0;
        if($user) {
            $unreadMessageCount = \DB::table('mensajes')
            ->where('id_usuario_destino', (int) $user->id)
            ->whereNull('read_at')
            ->count();
        }
 
        return [
            'me' => [
                'name' => $user?->name,
            ],
            'counts' => [
                'myAdsTotal' => $myTotal,
                'myAdsAvailable' => $myAvailable,
                'myAdsSoldOut' => $mySoldOut,
                'myAdsDisabled' => $myDisabled,
                'adsActiveTotal' => $adsActiveTotal,
            ],
            'badges' => [
                'unreadMessages' => $unreadMessageCount,
                'unreadNotifications' => 0,
            ],
        ];
    }
}
