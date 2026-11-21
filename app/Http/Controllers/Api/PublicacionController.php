<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PublicacionController extends Controller
{

    /**
     * @summary Crea una publicación mediante la API y devuelve sus datos en formato JSON.
     * @description
     * - Obtiene al usuario autenticado que realiza la petición.
     * - Valida los datos recibidos, incluyendo el nombre, descripción, tipo de publicación, tipo de beneficio y visibilidad.
     * - Determina el usuario al que se asociará la publicación, utilizando el identificador proporcionado o, en su defecto, el del usuario autenticado.
     * - Comprueba si el usuario asociado tiene privilegios de administrador mediante la tabla `Tipo_Usuario` y rechaza la operación si es administrador.
     * - Verifica que los campos económicos coincidan con el tipo de publicación:
     *   - Oferta (`Tipo_Publicacion = 0`): requiere `Precio` y no permite `Presupuesto`.
     *   - Demanda (`Tipo_Publicacion = 1`): requiere `Presupuesto` y no permite `Precio`.
     * - Inserta los datos principales de la publicación en la tabla `publicacions`.
     * - Registra el precio o presupuesto correspondiente en la tabla `ofertas` o `demandas`, según el tipo de publicación.
     * - Recupera los datos de la publicación creada mediante una consulta con LEFT JOIN a las tablas `ofertas` y `demandas`.
     * - Devuelve los datos de la publicación en formato JSON con código HTTP 201 (Created).
     *
     * @source
     * - Tabla `users`: identificación de los usuarios.
     * - Tabla `Tipo_Usuario`: comprobación de privilegios de administrador.
     * - Tabla `publicacions`: almacenamiento de los datos principales de las publicaciones.
     * - Tabla `ofertas`: almacenamiento del precio de las publicaciones de tipo Oferta.
     * - Tabla `demandas`: almacenamiento del presupuesto de las publicaciones de tipo Demanda.
     *
     * @route POST /api/post
     * @frontend No tiene un componente Inertia asociado. Endpoint destinado al consumo mediante peticiones HTTP.
     *
     * @param Request $request Petición HTTP con los datos necesarios para crear la publicación.
     * @return \Illuminate\Http\JsonResponse Datos de la publicación creada y código HTTP 201.
     * @throws \Illuminate\Validation\ValidationException Si los datos recibidos no cumplen las reglas de validación.
     */
    public function crearPublicacion(Request $request)
    {
        $user = $request->user();

        // CORREGIDO: Tipo_Publicacion es SIEMPRE Oferta/Demanda (0=Oferta, 1=Demanda)
        // NUEVO: Tipo_Beneficio determina si es Servicio/Producto (0=Servicio, 1=Producto)
        // El saldo que se envíe DEBE coincidir con Tipo_Publicacion:
        // - Si Tipo_Publicacion=0 (Oferta) → DEBE enviar "Precio"
        // - Si Tipo_Publicacion=1 (Demanda) → DEBE enviar "Presupuesto"
        $rules = [
            'id_usuario' => ['nullable', 'integer', 'exists:users,id'],
            'Nombre' => ['required', 'string', 'max:255'],
            'Descripcion' => ['required', 'string', 'max:255'],
            'Tipo_Publicacion' => ['required', 'integer', 'in:0,1'], 
            'Tipo_Beneficio' => ['required', 'integer', 'in:0,1'], 
            'visible' => ['required', 'integer', 'in:0,1'],
            'Precio' => ['nullable', 'integer', 'min:0'], 
            'Presupuesto' => ['nullable', 'integer', 'min:0'], 
        ];

        $validated = $request->validate($rules);

        // Si no especifica id_usuario, usa el usuario logueado
        $idUsuario = $validated['id_usuario'] ?? $user->id;

        // TODO: VALIDACIÓN - Bloquear que administradores creen publicaciones
        // Verificar si el usuario es administrador (tiene registro en tipo__usuarios)
        // CORRECCIÓN, en Insomnia todavía se pueden crear publicaciones al no existir un diferenciador entre admins y usuarios de momento.
        $esAdmin = \App\Models\Tipo_Usuario::where('id_usuario', $idUsuario)->exists();
        if ($esAdmin) {
            return response()->json([
                'message' => 'Los administradores no pueden crear publicaciones',
                'error' => 'admin_cannot_publish'
            ], 403);
        }

        
        $tipoPublicacion = (int) $validated['Tipo_Publicacion'];
        $tienePrecio = $request->filled('Precio');
        $tienePresupuesto = $request->filled('Presupuesto');


        if ($tipoPublicacion === 0) {
            if (!$tienePrecio) {
                return response()->json(['message' => 'Oferta (Tipo_Publicacion=0) requiere campo "Precio"'], 400);
            }
            if ($tienePresupuesto) {
                return response()->json(['message' => 'Oferta (Tipo_Publicacion=0) NO puede tener "Presupuesto"'], 400);
            }
        }


        if ($tipoPublicacion === 1) {
            if (!$tienePresupuesto) {
                return response()->json(['message' => 'Demanda (Tipo_Publicacion=1) requiere campo "Presupuesto"'], 400);
            }
            if ($tienePrecio) {
                return response()->json(['message' => 'Demanda (Tipo_Publicacion=1) NO puede tener "Precio"'], 400);
            }
        }

        // Crear publicación con Tipo_Publicacion (Oferta/Demanda) y Tipo_Beneficio (Servicio/Producto)
        $publicacionId = \DB::table('publicacions')->insertGetId([
            'id_usuario' => $idUsuario,
            'Nombre' => $validated['Nombre'],
            'Descripcion' => $validated['Descripcion'],
            'Tipo_Publicacion' => $tipoPublicacion,
            'Tipo_Beneficio' => $validated['Tipo_Beneficio'], 
            'visible' => $validated['visible'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Crear entrada en ofertas o demandas según Tipo_Publicacion
        if ($tipoPublicacion === 0) {
            // Es Oferta: crear en tabla ofertas con Precio
            \DB::table('ofertas')->insert([
                'id_publicacion' => $publicacionId,
                'Precio' => $validated['Precio'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            // Es Demanda: crear en tabla demandas con Presupuesto
            \DB::table('demandas')->insert([
                'id_publicacion' => $publicacionId,
                'Presupuesto' => $validated['Presupuesto'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Obtener la publicación completa para retornar
        // Se especifica 'publicacions.id' explícitamente para evitar ambigüedad en los joins
        $publicacion = \DB::table('publicacions')
            ->where('publicacions.id', $publicacionId) 
            ->leftJoin('ofertas', 'publicacions.id', '=', 'ofertas.id_publicacion')
            ->leftJoin('demandas', 'publicacions.id', '=', 'demandas.id_publicacion')
            ->select(
                'publicacions.id',
                'publicacions.Nombre',
                'publicacions.Descripcion',
                'publicacions.Tipo_Publicacion',
                'publicacions.visible',
                'publicacions.created_at',
                'ofertas.Precio',
                'demandas.Presupuesto'
            )
            ->first();

        return response()->json($publicacion, 201);
    }
}
