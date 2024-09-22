<?php

namespace App\Http\Controllers;

use App\Models\Demanda;
use App\Models\Oferta;
use App\Traits\Cruds;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Request;

class PublicacionesController extends Controller{

    use Cruds;

    public function find(Request $request){
        $result = $this->Obtener_publicacions($request, $use_for = 0, $offset = 0, $ammount = 20);
        return response()->json($result, 200);
    }

    public function findOferta(int $ofertaId){
        return response()->json($this->findOfertaById($ofertaId), 200);
    }

    public function findDemanda(int $demandaId){
        return response()->json($this->findDemandaById($demandaId), 200);
    }

    public function create(Request $request){
        Log::info("creating a post show request info");
        Log::info(json_encode($request));
        if(Gate::allows('admin-action', $request->user())){
            Log::info("admin user request");
            $validator = Validator::make($request->all(), [
                'id_usuario' => 'required|integer|present'
            ]);
            if($validator->fails()){
                Log::info("invalid admin request!!!!");
                return response()->json(["message" => $validator->errors()->all()], 400);
            }
            Log::info("valid admin request");
        }else{
            Log::info("auth user request");
            $request->request->set("id_usuario", $request->user()->id);
            Log::info("Found user ");
            Log::info(json_encode($request->user()));
        }
        $validator = Validator::make($request->all(), [
            'Nombre' => 'required|string|present',
            'Descripcion' => "required|string|present",
            'Tipo_Publicacion' => 'required|int|present'
        ]);
        if($validator->fails()){
            Log::info("invalid publicacion info");
            return response()->json(["message" => $validator->errors()->all()], 400);
        }
        LOG::info("Tipo publicacion ".$request->Tipo_Publicacion);
        if($request->Tipo_Publicacion === 0){
            Log::info("create an Oferta");
            //Oferta?
            $validator = Validator::make($request->all(), [
                'Precio' => 'required|numeric|min:0|present'
            ]);
            if($validator->fails()){
                Log::info("invalid oferta info");
                return response()->json(["message" => $validator->errors()->all()], 400);
            }
            $publicacion = $this->Editar_publicacions($request);
            Log::info("publicacion created");
            Log::info(json_encode($publicacion));
            $request->request->set("id_publicacion", $publicacion->id);
            $nOferta = $this->crearOferta($request);
            return response()->json($this->findOfertaById($nOferta->id), 200);
        }
        elseif ($request->Tipo_Publicacion === 1) {
            Log::info("create a demanda");
            //demanda?
            $validator = Validator::make($request->all(), [
                'Presupuesto' => 'required|numeric|min:0|present'
            ]);
            if($validator->fails()){
                Log::info("failed demanda info");
                return response()->json(["message" => $validator->errors()->all()], 400);
            }
            $publicacion = $this->Editar_publicacions($request);
            LOG::info("Demanda publicacion created");
            LOG::info(json_encode($publicacion));
            $request->request->set("id_publicacion", $publicacion->id);
            $nDemanda = $this->crearDemanda($request);
            LOG::info("Demanda created");
            LOG::info(json_encode($nDemanda));
            return response()->json($this->findDemandaById($nDemanda->id), 200);
        }
        else{
            Log::info("Unkown tipo");
            return response()->json(["message" => "Unkown type"], 400);
        }
        return response()->json($publicacion, 200);
    }

}
