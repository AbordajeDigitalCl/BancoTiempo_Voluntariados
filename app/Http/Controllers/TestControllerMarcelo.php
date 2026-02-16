<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Cruds;


class TestControllerMarcelo extends Controller
{
    use Cruds;

    public function editar(Request $request)
    {
        //dd($request);
        // $result = $this->Editar_publicacions($request);
        // $result = $this->Editar_proyectos($request);
        // $result = $this->Editar_voluntario($request, $hard_delete = true);
        // $result = $this->Editar_tipo__usuarios($request);
        // $result = $this->Editar_calificacions($request);
        // $result = $this->Editar_demandas($request);
        // $result = $this->Editar_ofertas($request);
        // $result = $this->Editar_transaccions($request);
        // $result = $this->Editar_mensaje($request);
        // $result = $this->Editar_inter__calif__publics($request);
        // $result = $this->Editar_inter__transa__publics($request);
        // $result = $this->Editar_imagens($request);

        // $result = $this->Editar_usuarios($request, $hard_delete = false);
        
        // $result = "A";
        return response()->json($result, 200);
    }

    public function obtener(Request $request)
    {
        // $result = $this->Obtener_publicacions($request, $use_for = 0, $offset = 0, $ammount = 20);
        // $result = $this->Obtener_proyectos($request, $use_for = '-', $offset = 0, $ammount = 20);
        // $result = $this->Obtener_voluntario($request, $use_for = 0, $offset = 0, $ammount = 20);
        // $result = $this->Obtener_tipo__usuarios($request, $use_for = 0, $offset = 0, $ammount = 20);
        // $result = $this->Obtener_calificacions($request, $use_for = 0, $offset = 0, $ammount = 20);
        // $result = $this->Obtener_demandas($request, $use_for = 0, $offset = 0, $ammount = 20);
        // $result = $this->Obtener_ofertas($request, $use_for = 0, $offset = 0, $ammount = 20);
        // $result = $this->Obtener_transaccions($request, $use_for = '-', $offset = 0, $ammount = 20);
        // $result = $this->Obtener_mensaje($request, $use_for = 0, $offset = 0, $ammount = 20);
        // $result = $this->Obtener_inter__calif__publics($request, $use_for = 0, $offset = 0, $ammount = 20);
        // $result = $this->Obtener_inter__transa__publics($request, $use_for = '-', $offset = 0, $ammount = 20);
        // $result = $this->Obtener_imagens($request, $use_for = 0, $offset = 0, $ammount = 20);

        // $result = $this->Obtener_usuarios($request, $use_for = '-', $offset = 0, $ammount = 20);
        
        // $result = $this->Revisar_Acceso(1, 1);
        // $result = "A";
        // return response()->json(is_numeric($request->input('Nombre_Tipo_Usuario')), 200);
        return response()->json($result, 200);
    }
    
    //Este cógido pertenece a Marcelo
    /*
    Comentario
    De
    prueba
    */
    public function nombre(Request $request)
    {
        // Implementar aquí
    }
}

