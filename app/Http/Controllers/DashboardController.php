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
}
