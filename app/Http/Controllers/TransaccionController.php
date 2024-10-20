<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use App\Traits\Cruds;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class TransaccionController extends Controller{

    use Cruds;

    public function find(Request $request){
        $result = $this->Obtener_transaccions($request, $use_for = '-', $offset = 0, $ammount = 20);
        return response()->json($result, 200);
    }

    public function buy(Request $request){
        Log::info("On buy function");
        $validator = Validator::make($request->all(), [
            'ofertaId' => 'required|integer|present',
        ]);
        if($validator->fails()){
            LOG::info("Validator failed");
            return response()->json(["message" => $validator->errors()->all()], 400);
        }
        $buyerId = $request->user()->id;
        $oferta = $this->findOfertaById($request->ofertaId);
        if($oferta == null){
            LOG::info("Oferta not found");
            return response()->json(["message" => "Oferta $request->ofertaId not found"], 404);
        }
        if(!Gate::allows('can-user-buy', $oferta->Precio)){
            LOG::info("Not enough hours to purchase");
            return response()->json(["message" => "insufficient funds"], 402);
        }
        LOG::info("Save transaction");
        $transaction = $this->makeTransaction($buyerId, $oferta->id_usuario, 0);
        LOG::info("save purchase");
        $purchase = $this->savePurchase($oferta->id, $transaction->id, $oferta->Precio);
        return response()->json([
            "vendorId" => $oferta->id_usuario,
            "Precio" => $oferta->Precio,
            "timestamp" => $transaction->created_at
        ], 200);
    }

    public function pay(Request $request){
        $validator = Validator::make($request->all(), [
            'demandaId' => 'required|integer|present',
        ]);
        if($validator->fails()){
            LOG::info("Validator failed");
            return response()->json(["message" => $validator->errors()->all()], 400);
        }
        $clientId = $request->user()->id;
        $demanda = $this->findDemandaById($request->demandaId);
        Log::info("Demanda found ".json_encode($demanda));
        if($demanda == null){
            LOG::info("demanda not found");
            return response()->json(["message" => "Oferta $request->demandaId not found"], 404);
        }
        if(!Gate::allows('can-user-buy', $demanda->Presupuesto)){
            LOG::info("Not enough hours to purchase");
            return response()->json(["message" => "insufficient funds"], 402);
        }
        LOG::info("Save transaction");
        $transaction = $this->makeTransaction($clientId, $demanda->id_usuario, 0);
        LOG::info("save purchase");
        $purchase = $this->savePurchase($demanda->id, $transaction->id, $demanda->Presupuesto);
        return response()->json([
            "vendorId" => $demanda->id_usuario,
            "Precio" => $demanda->Presupuesto,
            "timestamp" => $transaction->created_at
        ], 200);
    }

    public function edit(Request $request){
        $result = $this->Editar_transaccions($request);
        return response()->json($result, 200);
    }
}
