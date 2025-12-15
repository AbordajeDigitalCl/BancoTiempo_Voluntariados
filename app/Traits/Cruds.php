<?php

namespace App\Traits;

use Illuminate\Http\Request;
//use Illuminate\View\View;
//use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Transaccion;
use App\Models\Inter_Transa_Public;
use App\Models\Proyecto;
use App\Models\Voluntario;
use App\Models\Tipo_Usuario;
use App\Models\Mensaje;
use App\Models\Publicacion;
use App\Models\Demanda;
use App\Models\Oferta;
use App\Models\Imagen;
use App\Models\Inter_Calif_Public;
use App\Models\Calificacion;
use Exception;
//use Validator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Type\Integer;
use stdClass;

// use PDF;

trait Cruds
{
    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_transaccions(Request $request, $use_for = '-', $offset = 0, $ammount = 20)
    {
        try {
            if (
                !is_numeric($offset) ||
                !is_numeric($ammount)
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            } else if (
                $offset < 0 ||
                $ammount < 0
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            }

            if (!$this->Internal_Validation_Transaccions($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }
            $Transaccion = null;
            //Si es obtener con filtro
            if ($request->has("id") && $request->filled("id")) {
                $Transaccion = Transaccion::where([['id', $request->input("id")]])->get();
            } else if ($request->has("id_usuario_origen") && $request->filled("id_usuario_origen")) {
                $Transaccion = DB::select('select * from transaccions Where id_usuario_origen = ' . $request->input('id_usuario_origen') . ' LIMIT ' . $offset . ',' . $ammount);
            } else if ($request->has("id_usuario_destino") && $request->filled("id_usuario_destino")) {
                $Transaccion = DB::select('select * from transaccions Where id_usuario_destino = ' . $request->input('id_usuario_destino') . ' LIMIT ' . $offset . ',' . $ammount);
            } else {
                //Si es obtener normal y sin filtro
                if ($use_for == "A") {
                    $Transaccion = Transaccion::get();
                } else if (is_numeric($use_for)) {
                    $Transaccion = DB::select('select * from transaccions WHERE Tipo_Transaccion = ' . $use_for . ' LIMIT ' . $offset . ',' . $ammount);
                } else {
                    //Si es obtener TODO
                    $Transaccion = DB::select('select * from transaccions LIMIT ' . $offset . ',' . $ammount);
                }
            }

            return $Transaccion;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener la transaccion, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_transaccions(Request $request)
    {
        try {
            if (!$this->Internal_Validation_Transaccions($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }
            $Transaccion = null;
            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                $Transaccion = new Transaccion();
                $Transaccion->id_usuario_origen = $request->has("id_usuario_origen") ? $request->input("id_usuario_origen") : $Transaccion->id_usuario_origen;
                $Transaccion->id_usuario_destino =  $request->has("id_usuario_destino") ? $request->input("id_usuario_destino") : $Transaccion->id_usuario_destino;
                $Transaccion->Tipo_Transaccion =  $request->has("Tipo_Transaccion") ? $request->input("Tipo_Transaccion") : $Transaccion->Tipo_Transaccion;
                $Transaccion->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $Transaccion = Transaccion::where([['id', $request->input("id")]])->first();
                if ($Transaccion != null) {
                    $Transaccion->id_usuario_origen = $request->has("id_usuario_origen") ? $request->input("id_usuario_origen") : $Transaccion->id_usuario_origen;
                    $Transaccion->id_usuario_destino =  $request->has("id_usuario_destino") ? $request->input("id_usuario_destino") : $Transaccion->id_usuario_destino;
                    $Transaccion->Tipo_Transaccion =  $request->has("Tipo_Transaccion") ? $request->input("Tipo_Transaccion") : $Transaccion->Tipo_Transaccion;
                    $Transaccion->update();
                }
            }

            if ($request->isMethod('delete')) {
                $Transaccion = Transaccion::where([['id', $request->input("id")]])->first();
                if ($Transaccion == null) {
                    return false;
                }

                // $Venta_Detalle = Venta_Detalle::where('id_Venta', $venta->id);
                // if ($Venta_Detalle != null) {
                //     $Venta_Detalle->delete();
                // }

                $Transaccion = $Transaccion->delete();
            }

            return $Transaccion;
        } catch (Exception  $e) {
            DB::rollback();
            session()->flash('failure', "Error al procesar la solicitud: por favor intente de nuevo");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    private function Internal_Validation_Transaccions(Request $request)
    {
        $result = true;
        if ($request->has("id") && $request->filled("id")) {
            if (!is_numeric($request->input("id"))) {
                $result = false;
            } else if ($request->input("id") <= 0) {
                $result = false;
            }
        }
        if ($request->has("id_usuario_origen") && $request->filled("id_usuario_origen")) {
            if (!is_numeric($request->input("id_usuario_origen"))) {
                $result = false;
            } else if ($request->input("id_usuario_origen") <= 0) {
                $result = false;
            }
        }
        if ($request->has("id_usuario_destino") && $request->filled("id_usuario_destino")) {
            if (!is_numeric($request->input("id_usuario_destino"))) {
                $result = false;
            } else if ($request->input("id_usuario_destino") <= 0) {
                $result = false;
            }
        }
        if ($request->has("Tipo_Transaccion") && $request->filled("Tipo_Transaccion")) {
            if (!is_numeric($request->input("Tipo_Transaccion"))) {
                $result = false;
            } else if ($request->input("Tipo_Transaccion") <= 0) {
                $result = false;
            }
        }
        return $result;
    }

    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_inter__transa__publics(Request $request, $use_for = '-', $offset = 0, $ammount = 20)
    {
        try {
            if (
                !is_numeric($offset) ||
                !is_numeric($ammount)
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            } else if (
                $offset < 0 ||
                $ammount < 0
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            }

            if (!$this->Internal_Validation_Inter__Transa__Publics($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }

            $Inter_Transa_Public = null;
            if ($request->has("id") && $request->filled("id")) {
                $Inter_Transa_Public = Inter_Transa_Public::where([['id', $request->input("id")]])->get();
            } else if ($request->has("id_publicacion") && $request->filled("id_publicacion")) {
                $Inter_Transa_Public = DB::select('select * from inter__transa__publics Where id_publicacion = ' . $request->input('id_publicacion') . ' LIMIT ' . $offset . ',' . $ammount);
            } else if ($request->has("id_transaccion") && $request->filled("id_transaccion")) {
                $Inter_Transa_Public = DB::select('select * from inter__transa__publics Where id_transaccion = ' . $request->input('id_transaccion') . ' LIMIT ' . $offset . ',' . $ammount);
            } else {
                //Si es obtener normal y sin filtro
                if ($use_for == "A") {
                    $Inter_Transa_Public = Inter_Transa_Public::get();
                } else {
                    //Si es obtener TODO
                    $Inter_Transa_Public = DB::select('select * from inter__transa__publics LIMIT ' . $offset . ',' . $ammount);
                }
            }
            return $Inter_Transa_Public;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el Inter_Transa_Public, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_inter__transa__publics(Request $request)
    {
        try {
            if (!$this->Internal_Validation_Inter__Transa__Publics($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }
            $Inter_Transa_Public = null;

            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                //To avoid create it twice
                $ifAlreadyExist = Inter_Transa_Public::where([['id_publicacion', $request->input("id_publicacion")], ['id_transaccion', $request->input("id_transaccion")]])->get();
                if ($ifAlreadyExist->count() > 0) {
                    session()->flash('failure', "Dato intermedio ya existe, ignorando la solicitud");
                    return "{}";
                }

                $Inter_Transa_Public = new Inter_Transa_Public();
                $Inter_Transa_Public->id_publicacion = $request->has("id_publicacion") ? $request->input("id_publicacion") : $Inter_Transa_Public->id_publicacion;
                $Inter_Transa_Public->id_transaccion = $request->has("id_transaccion") ? $request->input("id_transaccion") : $Inter_Transa_Public->id_transaccion;
                $Inter_Transa_Public->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $Inter_Transa_Public = Inter_Transa_Public::where([['id', $request->input("id")]])->first();
                if ($Inter_Transa_Public != null) {
                    $Inter_Transa_Public->id_publicacion = $request->has("id_publicacion") ? $request->input("id_publicacion") : $Inter_Transa_Public->id_publicacion;
                    $Inter_Transa_Public->id_transaccion = $request->has("id_transaccion") ? $request->input("id_transaccion") : $Inter_Transa_Public->id_transaccion;
                    $Inter_Transa_Public->update();
                }
            }

            if ($request->isMethod('delete')) {
                $Inter_Transa_Public = Inter_Transa_Public::where([['id', $request->input("id")]])->first();
                if ($Inter_Transa_Public == null) {
                    return false;
                }

                $Inter_Transa_Public = $Inter_Transa_Public->delete();
            }

            return $Inter_Transa_Public;
        } catch (Exception  $e) {
            DB::rollback();
            session()->flash('failure', "Error al procesar la solicitud: por favor intente de nuevo");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    private function Internal_Validation_Inter__Transa__Publics(Request $request)
    {
        $result = true;
        if ($request->has("id") && $request->filled("id")) {
            if (!is_numeric($request->input("id"))) {
                $result = false;
            } else if ($request->input("id") <= 0) {
                $result = false;
            }
        }
        if ($request->has("id_transaccion") && $request->filled("id_transaccion")) {
            if (!is_numeric($request->input("id_transaccion"))) {
                $result = false;
            } else if ($request->input("id_transaccion") <= 0) {
                $result = false;
            }
        }
        if ($request->has("id_publicacion") && $request->filled("id_publicacion")) {
            if (!is_numeric($request->input("id_publicacion"))) {
                $result = false;
            } else if ($request->input("id_publicacion") <= 0) {
                $result = false;
            }
        }
        return $result;
    }









    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_proyectos(Request $request, $use_for = '-', $offset = 0, $ammount = 20)
    {
        try {
            if (
                !is_numeric($offset) ||
                !is_numeric($ammount)
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            } else if (
                $offset < 0 ||
                $ammount < 0
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            }

            if (!$this->Internal_Validation_Proyectos($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }

            $Proyecto = null;
            //Si es obtener con filtro
            if ($request->has("id") && $request->filled("id")) {
                $Proyecto = Proyecto::where([['id', $request->input("id")]])->get();
            } else if ($request->has("id_publicacion") && $request->filled("id_publicacion")) {
                $Proyecto = DB::select('select * from proyectos Where id_publicacion = ' . $request->input('id_publicacion') . ' LIMIT ' . $offset . ',' . $ammount);
            } else {
                //Si es obtener normal y sin filtro
                if ($use_for == "A") {
                    $Proyecto = Proyecto::get();
                } else if (is_numeric($use_for)) {
                    $Proyecto = DB::select('select * from proyectos WHERE Porcentaje_Avance = ' . $use_for . ' LIMIT ' . $offset . ',' . $ammount);
                } else {
                    //Si es obtener TODO
                    $Proyecto = DB::select('select * from proyectos LIMIT ' . $offset . ',' . $ammount);
                }
            }

            foreach ($Proyecto as $key => $value) {
                if (is_array($Proyecto)) { //Is an array
                    if ($Proyecto != null) {
                        $Publicacion = Publicacion::where([['id', $value->id_publicacion]])->get();
                        $Proyecto[$key]->id_usuario = $Publicacion[0]->id_usuario;
                        $Proyecto[$key]->Nombre = $Publicacion[0]->Nombre;
                        $Proyecto[$key]->Descripcion =  $Publicacion[0]->Descripcion;
                        $Proyecto[$key]->Tipo_Publicacion = $Publicacion[0]->Tipo_Publicacion;
                    }
                } else { //Is an instanceof Collection
                    if (!$Proyecto->isEmpty()) {
                        $Publicacion = Publicacion::where([['id', $value->id_publicacion]])->get();
                        $Proyecto[$key]->id_usuario = $Publicacion[0]->id_usuario;
                        $Proyecto[$key]->Nombre = $Publicacion[0]->Nombre;
                        $Proyecto[$key]->Descripcion =  $Publicacion[0]->Descripcion;
                        $Proyecto[$key]->Tipo_Publicacion = $Publicacion[0]->Tipo_Publicacion;
                    }
                }
            }

            return $Proyecto;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_proyectos(Request $request)
    {
        try {

            if (!$this->Internal_Validation_Proyectos($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }

            $Proyecto = null;
            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                $Proyecto = new Proyecto();
                // $Proyecto->id_publicacion = $request->has("id_publicacion") ? $request->input("id_publicacion") : $Proyecto->id_publicacion;
                $Proyecto->id_publicacion = 0;
                $Proyecto->Porcentaje_Avance =  $request->has("Porcentaje_Avance") ? $request->input("Porcentaje_Avance") : $Proyecto->Porcentaje_Avance;

                $this->Internal_Editar_publicacions($Proyecto, $request);
                $ProyectoNw = new Proyecto();
                $ProyectoNw->id_publicacion = $Proyecto->id_publicacion;
                $ProyectoNw->Porcentaje_Avance = $Proyecto->Porcentaje_Avance;
                $ProyectoNw->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $Proyecto = Proyecto::where([['id', $request->input("id")]])->first();
                if ($Proyecto != null) {
                    $Proyecto->id_publicacion = $request->has("id_publicacion") ? $request->input("id_publicacion") : $Proyecto->id_publicacion;
                    $Proyecto->Porcentaje_Avance =  $request->has("Porcentaje_Avance") ? $request->input("Porcentaje_Avance") : $Proyecto->Porcentaje_Avance;
                    $Proyecto->update();

                    //dd($Proyecto);
                    $this->Internal_Editar_publicacions($Proyecto, $request);
                }
            }

            if ($request->isMethod('delete')) {
                $Proyecto = Proyecto::where([['id', $request->input("id")]])->first();
                if ($Proyecto == null) {
                    return false;
                }

                $this->Internal_Editar_publicacions($Proyecto, $request);
                $Proyecto = $Proyecto->delete();
            }

            return $Proyecto;
        } catch (Exception  $e) {
            DB::rollback();
            session()->flash('failure', "Error al procesar la solicitud: por favor intente de nuevo");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    private function Internal_Validation_Proyectos(Request $request)
    {
        $result = true;
        if ($request->has("id") && $request->filled("id")) {
            if (!is_numeric($request->input("id"))) {
                $result = false;
            } else if ($request->input("id") <= 0) {
                $result = false;
            }
        }
        if ($request->has("id_publicacion") && $request->filled("id_publicacion")) {
            if (!is_numeric($request->input("id_publicacion"))) {
                $result = false;
            } else if ($request->input("id_publicacion") <= 0) {
                $result = false;
            }
        }
        if ($request->has("Porcentaje_Avance") && $request->filled("Porcentaje_Avance")) {
            if (!is_numeric($request->input("Porcentaje_Avance"))) {
                $result = false;
            } else if ($request->input("Porcentaje_Avance") <= 0) {
                $result = false;
            }
        }
        return $result;
    }


    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_voluntario(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            if (
                !is_numeric($offset) ||
                !is_numeric($ammount)
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            } else if (
                $offset < 0 ||
                $ammount < 0
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            }

            if (!$this->Internal_Validation_Voluntarios($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }

            $Voluntario = null;
            //Si es obtener con filtro
            if ($request->has("id") && $request->filled("id")) {
                $Voluntario = Voluntario::where([['id', $request->input("id")]])->get();
            } else if ($request->has("id_proyecto") && $request->filled("id_proyecto")) {
                $Voluntario = DB::select('select * from voluntarios Where id_proyecto = ' . $request->input('id_proyecto') . ' LIMIT ' . $offset . ',' . $ammount);
            } else if ($request->has("id_usuario") && $request->filled("id_usuario")) {
                $Voluntario = DB::select('select * from voluntarios Where id_usuario = ' . $request->input('id_usuario') . ' LIMIT ' . $offset . ',' . $ammount);
            } else {
                //Si es obtener normal y sin filtro
                if ($use_for == "A") {
                    $Voluntario = Voluntario::get();
                } else {
                    //Si es obtener TODO
                    $Voluntario = DB::select('select * from voluntarios LIMIT ' . $offset . ',' . $ammount);
                }
            }

            return $Voluntario;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el Voluntario, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_voluntario(Request $request, $hard_delete = true)
    {
        try {
            $Voluntario = null;

            if (!$this->Internal_Validation_Voluntarios($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }

            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                //To avoid create it twice
                $ifAlreadyExist = Voluntario::where([['id_proyecto', $request->input("id_proyecto")], ['id_usuario', $request->input("id_usuario")]])->get();
                if ($ifAlreadyExist->count() > 0) {
                    session()->flash('failure', "Dato intermedio ya existe, ignorando la solicitud");
                    return "{}";
                }

                $Voluntario = new Voluntario();
                $Voluntario->id_proyecto = $request->has("id_proyecto") ? $request->input("id_proyecto") : $Voluntario->id_proyecto;
                $Voluntario->id_usuario = $request->has("id_usuario") ? $request->input("id_usuario") : $Voluntario->id_usuario;
                $Voluntario->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $Voluntario = Voluntario::where([['id', $request->input("id")]])->first();
                if ($Voluntario != null) {
                    $Voluntario->id_proyecto = $request->has("id_proyecto") ? $request->input("id_proyecto") : $Voluntario->id_proyecto;
                    $Voluntario->id_usuario = $request->has("id_usuario") ? $request->input("id_usuario") : $Voluntario->id_usuario;
                    $Voluntario->update();
                }
            }

            if ($request->isMethod('delete')) {
                $Voluntario = Voluntario::where([['id', $request->input("id")]])->first();
                if ($Voluntario == null) {
                    return false;
                }

                $Voluntario = $Voluntario->delete();
            }

            return $Voluntario;
        } catch (Exception  $e) {
            DB::rollback();
            session()->flash('failure', "Error al procesar la solicitud: por favor intente de nuevo");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    private function Internal_Validation_Voluntarios(Request $request)
    {
        $result = true;
        if ($request->has("id") && $request->filled("id")) {
            if (!is_numeric($request->input("id"))) {
                $result = false;
            } else if ($request->input("id") <= 0) {
                $result = false;
            }
        }
        if ($request->has("id_proyecto") && $request->filled("id_proyecto")) {
            if (!is_numeric($request->input("id_proyecto"))) {
                $result = false;
            } else if ($request->input("id_proyecto") <= 0) {
                $result = false;
            }
        }
        if ($request->has("id_usuario") && $request->filled("id_usuario")) {
            if (!is_numeric($request->input("id_usuario"))) {
                $result = false;
            } else if ($request->input("id_usuario") <= 0) {
                $result = false;
            }
        }
        return $result;
    }










    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_mensaje(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            if (
                !is_numeric($offset) ||
                !is_numeric($ammount)
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            } else if (
                $offset < 0 ||
                $ammount < 0
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            }

            if (!$this->Internal_Validation_Message($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }

            $Mensaje = null;
            //Si es obtener con filtro
            if ($request->has("id") && $request->filled("id")) {
                $Mensaje = Mensaje::where([['id', $request->input("id")]])->get();
            } else if ($request->has("id_usuario_origen") && $request->filled("id_usuario_origen")) {
                $Mensaje = DB::select('select * from mensajes Where id_usuario_origen = ' . $request->input('id_usuario_origen') . ' LIMIT ' . $offset . ',' . $ammount);
            } else if ($request->has("id_usuario_destino") && $request->filled("id_usuario_destino")) {
                $Mensaje = DB::select('select * from mensajes Where id_usuario_destino = ' . $request->input('id_usuario_destino') . ' LIMIT ' . $offset . ',' . $ammount);
            } else if ($request->has("Tipo_Mensaje") && $request->filled("Tipo_Mensaje")) {
                $Mensaje = DB::select('select * from mensajes Where Tipo_Mensaje = ' . $request->input('Tipo_Mensaje') . ' LIMIT ' . $offset . ',' . $ammount);
            } else {
                //Si es obtener normal y sin filtro
                if ($use_for == "A") {
                    $Mensaje = Mensaje::get();
                } else {
                    //Si es obtener TODO
                    $Mensaje = DB::select('select * from mensajes LIMIT ' . $offset . ',' . $ammount);
                }
            }

            return $Mensaje;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el Mensaje, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_mensaje(Request $request)
    {
        try {
            if (!$this->Internal_Validation_Message($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }
            $Mensaje = null;

            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                $Mensaje = new Mensaje();
                $Mensaje->id_usuario_origen = $request->has("id_usuario_origen") ? $request->input("id_usuario_origen") : $Mensaje->id_usuario_origen;
                $Mensaje->id_usuario_destino = $request->has("id_usuario_destino") ? $request->input("id_usuario_destino") : $Mensaje->id_usuario_destino;
                $Mensaje->Mensaje = $request->has("Mensaje") ? $request->input("Mensaje") : $Mensaje->Mensaje;
                $Mensaje->Tipo_Mensaje = $request->has("Tipo_Mensaje") ? $request->input("Tipo_Mensaje") : $Mensaje->Tipo_Mensaje;
                $Mensaje->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $Mensaje = Mensaje::where([['id', $request->input("id")]])->first();
                if ($Mensaje != null) {
                    $Mensaje->id_usuario_origen = $request->has("id_usuario_origen") ? $request->input("id_usuario_origen") : $Mensaje->id_usuario_origen;
                    $Mensaje->id_usuario_destino = $request->has("id_usuario_destino") ? $request->input("id_usuario_destino") : $Mensaje->id_usuario_destino;
                    $Mensaje->Mensaje = $request->has("Mensaje") ? $request->input("Mensaje") : $Mensaje->Mensaje;
                    $Mensaje->Tipo_Mensaje = $request->has("Tipo_Mensaje") ? $request->input("Tipo_Mensaje") : $Mensaje->Tipo_Mensaje;
                    $Mensaje->update();
                }
            }

            if ($request->isMethod('delete')) {
                $Mensaje = Mensaje::where([['id', $request->input("id")]])->first();
                if ($Mensaje == null) {
                    return false;
                }

                $Mensaje = $Mensaje->delete();
            }

            return $Mensaje;
        } catch (Exception  $e) {
            DB::rollback();
            session()->flash('failure', "Error al procesar la solicitud: por favor intente de nuevo");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    private function Internal_Validation_Message(Request $request)
    {
        $result = true;
        if ($request->has("id") && $request->filled("id")) {
            if (!is_numeric($request->input("id"))) {
                $result = false;
            } else if ($request->input("id") <= 0) {
                $result = false;
            }
        }
        if ($request->has("id_usuario_origen") && $request->filled("id_usuario_origen")) {
            if (!is_numeric($request->input("id_usuario_origen"))) {
                $result = false;
            } else if ($request->input("id_usuario_origen") <= 0) {
                $result = false;
            }
        }
        if ($request->has("id_usuario_destino") && $request->filled("id_usuario_destino")) {
            if (!is_numeric($request->input("id_usuario_destino"))) {
                $result = false;
            } else if ($request->input("id_usuario_destino") <= 0) {
                $result = false;
            }
        }
        if ($request->has("Mensaje") && $request->filled("Mensaje")) {
            if (!is_string($request->input("Mensaje"))) {
                $result = false;
            } else if ($request->input("Mensaje") <= 0) {
                $result = false;
            }
        }
        if ($request->has("Tipo_Mensaje") && $request->filled("Tipo_Mensaje")) {
            if (!is_numeric($request->input("Tipo_Mensaje"))) {
                $result = false;
            } else if ($request->input("Tipo_Mensaje") <= 0) {
                $result = false;
            }
        }
        return $result;
    }


    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_publicacions(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            if (
                !is_numeric($offset) ||
                !is_numeric($ammount)
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            } else if (
                $offset < 0 ||
                $ammount < 0
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            }

            if (!$this->Internal_Validation_Publicacions($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }

            $Publicacion = null;
            //Si es obtener con filtro
            if ($request->has("id") && $request->filled("id")) {
                $Publicacion = Publicacion::where([['id', $request->input("id")]])->get();
            } else if ($request->has("id_usuario") && $request->filled("id_usuario")) {
                $Publicacion = DB::select('select * from publicacions Where id_usuario = ' . $request->input('id_usuario') . ' LIMIT ' . $offset . ',' . $ammount);
            } else if ($request->has("Nombre") && $request->filled("Nombre")) {
                $Publicacion = DB::select('select * from publicacions Where Nombre = \'' . $request->input('Nombre') . '\' LIMIT ' . $offset . ',' . $ammount);
            } else if ($request->has("Tipo_Publicacion") && $request->filled("Tipo_Publicacion")) {
                $Publicacion = DB::select('select * from publicacions Where Tipo_Publicacion = ' . $request->input('Tipo_Publicacion') . ' LIMIT ' . $offset . ',' . $ammount);
            } else {
                //Si es obtener normal y sin filtro
                if ($use_for == "A") {
                    $Publicacion = Publicacion::get();
                } else {
                    //Si es obtener TODO
                    $Publicacion = DB::select('select * from publicacions LIMIT ' . $offset . ',' . $ammount);
                }
            }

            return $Publicacion;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener la Publicacion, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_publicacions(Request $request)
    {
        Log::info("on Editar_publicacions");
        try {
            $Publicacion = null;
            /*
            Already validated in controller
            if (!$this->Internal_Validation_Publicacions($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }
            */
            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                Log::info("create a new publicacion");
                $Publicacion = new Publicacion();
                $Publicacion->id_usuario = $request->has("id_usuario") ? $request->input("id_usuario") : $Publicacion->id_usuario;
                $Publicacion->Nombre = $request->has("Nombre") ? $request->input("Nombre") : $Publicacion->Nombre;
                $Publicacion->Descripcion = $request->has("Descripcion") ? $request->input("Descripcion") : $Publicacion->Descripcion;
                $Publicacion->Tipo_Publicacion = $request->has("Tipo_Publicacion") ? $request->input("Tipo_Publicacion") : $Publicacion->Tipo_Publicacion;
                $Publicacion->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $Publicacion = Publicacion::where([['id', $request->input("id")]])->first();
                if ($Publicacion != null) {
                    $Publicacion->id_usuario = $request->has("id_usuario") ? $request->input("id_usuario") : $Publicacion->id_usuario;
                    $Publicacion->Nombre = $request->has("Nombre") ? $request->input("Nombre") : $Publicacion->Nombre;
                    $Publicacion->Descripcion = $request->has("Descripcion") ? $request->input("Descripcion") : $Publicacion->Descripcion;
                    $Publicacion->Tipo_Publicacion = $request->has("Tipo_Publicacion") ? $request->input("Tipo_Publicacion") : $Publicacion->Tipo_Publicacion;
                    $Publicacion->update();
                }
            }

            if ($request->isMethod('delete')) {
                $Publicacion = Publicacion::where([['id', $request->input("id")]])->first();
                if ($Publicacion == null) {
                    return false;
                }

                $Publicacion = $Publicacion->delete();
            }

            return $Publicacion;
        } catch (Exception  $e) {
            DB::rollback();
            session()->flash('failure', "Error al procesar la solicitud: por favor intente de nuevo");
            Log::info("Publicacion Error ".$e->getMessage());
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    public function Internal_Editar_publicacions(&$stdClass, $request)
    {
        try {
            $Publicacion = null;

            if (!$this->Internal_Validation_Publicacions($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }

            if ($request->isMethod('post')) {
                //Crear objeto nuevo
                if ($stdClass->id_publicacion == 0) {
                    $Publicacion = new Publicacion();
                    $Publicacion->id_usuario = $request->input('id_usuario');
                    $Publicacion->Nombre = $request->input('Nombre');
                    $Publicacion->Descripcion = $request->input('Descripcion');
                    $Publicacion->Tipo_Publicacion = $request->input('Tipo_Publicacion');
                    $Publicacion->save();

                    $stdClass->id_usuario = $Publicacion->id_usuario;
                    $stdClass->Nombre = $Publicacion->Nombre;
                    $stdClass->Descripcion = $Publicacion->Descripcion;
                    $stdClass->Tipo_Publicacion = $Publicacion->Tipo_Publicacion;
                    $stdClass->id_publicacion = $Publicacion->id;
                }
            }

            if ($request->isMethod('put')) {
                //Actualizar objeto
                if ($stdClass->id_publicacion != 0) {
                    $Publicacion = Publicacion::where([['id', $stdClass->id_publicacion]])->first();
                    if ($Publicacion != null) {
                        $Publicacion->id_usuario = $request->input('id_usuario') != 0 ? $request->input('id_usuario') : $Publicacion->id_usuario;
                        $Publicacion->Nombre = $request->input('Nombre') != 0 ? $request->input('Nombre') : $Publicacion->Nombre;
                        $Publicacion->Descripcion = $request->input('Descripcion') != 0 ? $request->input('Descripcion') : $Publicacion->Descripcion;
                        $Publicacion->Tipo_Publicacion = $request->input('Tipo_Publicacion') != 0 ? $request->input('Tipo_Publicacion') : $Publicacion->Tipo_Publicacion;
                        $Publicacion->update();

                        $stdClass->id_usuario = $Publicacion->id_usuario;
                        $stdClass->Nombre = $Publicacion->Nombre;
                        $stdClass->Descripcion = $Publicacion->Descripcion;
                        $stdClass->Tipo_Publicacion = $Publicacion->Tipo_Publicacion;
                    }
                }
            }

            if ($request->isMethod('delete')) {
                $Publicacion = Publicacion::where([['id', $stdClass->id_publicacion]])->first();
                if ($Publicacion == null) {
                    return false;
                }
                $Publicacion = $Publicacion->delete();
            }

            return $Publicacion;
        } catch (Exception  $e) {
            DB::rollback();
            session()->flash('failure', "Error al procesar la solicitud: por favor intente de nuevo");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    private function Internal_Validation_Publicacions(Request $request)
    {
        $result = true;
        if ($request->has("id") && $request->filled("id")) {
            if (!is_numeric($request->input("id"))) {
                $result = false;
            } else if ($request->input("id") <= 0) {
                $result = false;
            }
        }
        if ($request->has("id_usuario") && $request->filled("id_usuario")) {
            if (!is_numeric($request->input("id_usuario"))) {
                $result = false;
            } else if ($request->input("id_usuario") <= 0) {
                $result = false;
            }
        }
        if ($request->has("Nombre") && $request->filled("Nombre")) {
            if (!is_string($request->input("Nombre"))) {
                $result = false;
            }
        }
        if ($request->has("Descripcion") && $request->filled("Descripcion")) {
            if (!is_string($request->input("Descripcion"))) {
                $result = false;
            }
        }
        if ($request->has("Tipo_Publicacion") && $request->filled("Tipo_Publicacion")) {
            if (!is_numeric($request->input("Tipo_Publicacion"))) {
                $result = false;
            } else if ($request->input("Tipo_Publicacion") <= 0) {
                $result = false;
            }
        }
        return $result;
    }


    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_demandas(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            if (
                !is_numeric($offset) ||
                !is_numeric($ammount)
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            } else if (
                $offset < 0 ||
                $ammount < 0
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            }


            if (!$this->Internal_Validation_Demandas($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }

            $Demanda = null;
            //Si es obtener con filtro
            if ($request->has("id") && $request->filled("id")) {
                $Demanda = Demanda::where([['id', $request->input("id")]])->get();
            } else if ($request->has("id_publicacion") && $request->filled("id_publicacion")) {
                $Demanda = DB::select('select * from demandas Where id_publicacion = ' . $request->input('id_publicacion') . ' LIMIT ' . $offset . ',' . $ammount);
            } else {
                //Si es obtener normal y sin filtro
                if ($use_for == "A") {
                    $Demanda = Demanda::get();
                } else {
                    //Si es obtener TODO
                    $Demanda = DB::select('select * from demandas LIMIT ' . $offset . ',' . $ammount);
                }
            }

            foreach ($Demanda as $key => $value) {
                if (is_array($Demanda)) { //Is an array
                    if ($Demanda != null) {
                        $Publicacion = Publicacion::where([['id', $value->id_publicacion]])->get();
                        $Demanda[$key]->id_usuario = $Publicacion[0]->id_usuario;
                        $Demanda[$key]->Nombre = $Publicacion[0]->Nombre;
                        $Demanda[$key]->Descripcion =  $Publicacion[0]->Descripcion;
                        $Demanda[$key]->Tipo_Publicacion = $Publicacion[0]->Tipo_Publicacion;
                    }
                } else { //Is an instanceof Collection
                    if (!$Demanda->isEmpty()) {
                        $Publicacion = Publicacion::where([['id', $value->id_publicacion]])->get();
                        $Demanda[$key]->id_usuario = $Publicacion[0]->id_usuario;
                        $Demanda[$key]->Nombre = $Publicacion[0]->Nombre;
                        $Demanda[$key]->Descripcion =  $Publicacion[0]->Descripcion;
                        $Demanda[$key]->Tipo_Publicacion = $Publicacion[0]->Tipo_Publicacion;
                    }
                }
            }

            return $Demanda;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el Demanda, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_demandas(Request $request)
    {
        try {

            if (!$this->Internal_Validation_Demandas($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }
            $Demanda = null;

            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                $Demanda = new Demanda();
                $Demanda->id_publicacion = 0;
                $Demanda->Presupuesto = $request->has("Presupuesto") ? $request->input("Presupuesto") : $Demanda->Presupuesto;

                $this->Internal_Editar_publicacions($Demanda, $request);
                $DemandaNw = new Demanda();
                $DemandaNw->id_publicacion = $Demanda->id_publicacion;
                $DemandaNw->Presupuesto = $Demanda->Presupuesto;
                $DemandaNw->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $Demanda = Demanda::where([['id', $request->input("id")]])->first();
                if ($Demanda != null) {
                    $Demanda->id_publicacion = $request->has("id_publicacion") ? $request->input("id_publicacion") : $Demanda->id_publicacion;
                    $Demanda->Presupuesto = $request->has("Presupuesto") ? $request->input("Presupuesto") : $Demanda->Presupuesto;
                    $Demanda->update();

                    $this->Internal_Editar_publicacions($Demanda, $request);
                }
            }

            if ($request->isMethod('delete')) {
                $Demanda = Demanda::where([['id', $request->input("id")]])->first();
                if ($Demanda == null) {
                    return false;
                }

                $this->Internal_Editar_publicacions($Demanda, $request);
                $Demanda = $Demanda->delete();
            }

            return $Demanda;
        } catch (Exception  $e) {
            DB::rollback();
            session()->flash('failure', "Error al procesar la solicitud: por favor intente de nuevo");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    private function Internal_Validation_Demandas(Request $request)
    {
        $result = true;
        if ($request->has("id") && $request->filled("id")) {
            if (!is_numeric($request->input("id"))) {
                $result = false;
            } else if ($request->input("id") <= 0) {
                $result = false;
            }
        }
        if ($request->has("id_publicacion") && $request->filled("id_publicacion")) {
            if (!is_numeric($request->input("id_publicacion"))) {
                $result = false;
            } else if ($request->input("id_publicacion") <= 0) {
                $result = false;
            }
        }
        if ($request->has("Presupuesto") && $request->filled("Presupuesto")) {
            if (!is_numeric($request->input("Presupuesto"))) {
                $result = false;
            } else if ($request->input("Presupuesto") <= 0) {
                $result = false;
            }
        }
        return $result;
    }



    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_ofertas(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            if (
                !is_numeric($offset) ||
                !is_numeric($ammount)
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            } else if (
                $offset < 0 ||
                $ammount < 0
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            }

            if (!$this->Internal_Validation_Ofertas($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }
            $Ofertas = null;
            //Si es obtener con filtro
            if ($request->has("id") && $request->filled("id")) {
                $Ofertas = Oferta::where([['id', $request->input("id")]])->get();
            } else if ($request->has("id_publicacion") && $request->filled("id_publicacion")) {
                $Ofertas = DB::select('select * from ofertas Where id_publicacion = ' . $request->input('id_publicacion') . ' LIMIT ' . $offset . ',' . $ammount);
            } else {
                //Si es obtener normal y sin filtro
                if ($use_for == "A") {
                    $Ofertas = Oferta::get();
                } else {
                    //Si es obtener TODO
                    $Ofertas = DB::select('select * from ofertas LIMIT ' . $offset . ',' . $ammount);
                }
            }

            foreach ($Ofertas as $key => $value) {
                if (is_array($Ofertas)) { //Is an array
                    if ($Ofertas != null) {
                        $Publicacion = Publicacion::where([['id', $value->id_publicacion]])->get();
                        $Ofertas[$key]->id_usuario = $Publicacion[0]->id_usuario;
                        $Ofertas[$key]->Nombre = $Publicacion[0]->Nombre;
                        $Ofertas[$key]->Descripcion =  $Publicacion[0]->Descripcion;
                        $Ofertas[$key]->Tipo_Publicacion = $Publicacion[0]->Tipo_Publicacion;
                    }
                } else { //Is an instanceof Collection
                    if (!$Ofertas->isEmpty()) {
                        $Publicacion = Publicacion::where([['id', $value->id_publicacion]])->get();
                        $Ofertas[$key]->id_usuario = $Publicacion[0]->id_usuario;
                        $Ofertas[$key]->Nombre = $Publicacion[0]->Nombre;
                        $Ofertas[$key]->Descripcion =  $Publicacion[0]->Descripcion;
                        $Ofertas[$key]->Tipo_Publicacion = $Publicacion[0]->Tipo_Publicacion;
                    }
                }
            }

            return $Ofertas;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener las ofertas, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_ofertas(Request $request)
    {
        try {
            if (!$this->Internal_Validation_Ofertas($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }
            $Oferta = null;

            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                $Oferta = new Oferta();
                $Oferta->id_publicacion = 0;
                $Oferta->Precio = $request->has("Precio") ? $request->input("Precio") : $Oferta->Precio;

                $this->Internal_Editar_publicacions($Oferta, $request);
                $OfertaNw = new Oferta();
                $OfertaNw->id_publicacion = $Oferta->id_publicacion;
                $OfertaNw->Precio = $Oferta->Precio;
                $OfertaNw->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $Oferta = Oferta::where([['id', $request->input("id")]])->first();
                if ($Oferta != null) {
                    $Oferta->id_publicacion = $request->has("id_publicacion") ? $request->input("id_publicacion") : $Oferta->id_publicacion;
                    $Oferta->Precio = $request->has("Precio") ? $request->input("Precio") : $Oferta->Precio;
                    $Oferta->update();

                    $this->Internal_Editar_publicacions($Oferta, $request);
                }
            }

            if ($request->isMethod('delete')) {
                $Oferta = Oferta::where([['id', $request->input("id")]])->first();
                if ($Oferta == null) {
                    return false;
                }

                $this->Internal_Editar_publicacions($Oferta, $request);
                $Oferta = $Oferta->delete();
            }

            return $Oferta;
        } catch (Exception  $e) {
            DB::rollback();
            session()->flash('failure', "Error al procesar la solicitud: por favor intente de nuevo");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    private function Internal_Validation_Ofertas(Request $request)
    {
        $result = true;
        if ($request->has("id") && $request->filled("id")) {
            if (!is_numeric($request->input("id"))) {
                $result = false;
            } else if ($request->input("id") <= 0) {
                $result = false;
            }
        }
        if ($request->has("id_publicacion") && $request->filled("id_publicacion")) {
            if (!is_numeric($request->input("id_publicacion"))) {
                $result = false;
            } else if ($request->input("id_publicacion") <= 0) {
                $result = false;
            }
        }
        if ($request->has("Precio") && $request->filled("Precio")) {
            if (!is_numeric($request->input("Precio"))) {
                $result = false;
            } else if ($request->input("Precio") <= 0) {
                $result = false;
            }
        }
        return $result;
    }

    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_imagens(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            if (
                !is_numeric($offset) ||
                !is_numeric($ammount)
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            } else if (
                $offset < 0 ||
                $ammount < 0
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            }

            if (!$this->Internal_Imagens($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }

            $Imagen = null;
            //Si es obtener con filtro
            if ($request->has("id") && $request->filled("id")) {
                $Imagen = Imagen::where([['id', $request->input("id")]])->get();
            } else if ($request->has("id_usuario_origen") && $request->filled("id_usuario_origen")) {
                $Imagen = DB::select('select * from imagens Where id_usuario_origen = ' . $request->input('id_usuario_origen') . ' LIMIT ' . $offset . ',' . $ammount);
            } else if ($request->has("Tipo") && $request->filled("Tipo")) {
                $Imagen = DB::select('select * from imagens Where Tipo = ' . $request->input('Tipo') . ' LIMIT ' . $offset . ',' . $ammount);
            } else {
                //Si es obtener normal y sin filtro
                if ($use_for == "A") {
                    $Imagen = Imagen::get();
                } else {
                    //Si es obtener TODO
                    $Imagen = DB::select('select * from imagens LIMIT ' . $offset . ',' . $ammount);
                }
            }

            return $Imagen;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener la imagen, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_imagens(Request $request)
    {
        try {
            if (!$this->Internal_Imagens($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }
            $Imagen = null;

            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                $Imagen = new Imagen();
                $Imagen->id_usuario_origen = $request->has("id_usuario_origen") ? $request->input("id_usuario_origen") : $Imagen->id_usuario_origen;
                $Imagen->Tipo = $request->has("Tipo") ? $request->input("Tipo") : $Imagen->Tipo;
                $Imagen->Direccion = $request->has("Direccion") ? $request->input("Direccion") : $Imagen->Direccion;
                $Imagen->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $Imagen = Imagen::where([['id', $request->input("id")]])->first();
                if ($Imagen != null) {
                    $Imagen->id_usuario_origen = $request->has("id_usuario_origen") ? $request->input("id_usuario_origen") : $Imagen->id_usuario_origen;
                    $Imagen->Tipo = $request->has("Tipo") ? $request->input("Tipo") : $Imagen->Tipo;
                    $Imagen->Direccion = $request->has("Direccion") ? $request->input("Direccion") : $Imagen->Direccion;
                    $Imagen->update();
                }
            }

            if ($request->isMethod('delete')) {
                $Imagen = Imagen::where([['id', $request->input("id")]])->first();
                if ($Imagen == null) {
                    return false;
                }

                $Imagen = $Imagen->delete();
            }

            return $Imagen;
        } catch (Exception  $e) {
            DB::rollback();
            session()->flash('failure', "Error al procesar la solicitud: por favor intente de nuevo");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    private function Internal_Imagens(Request $request)
    {
        $result = true;
        if ($request->has("id") && $request->filled("id")) {
            if (!is_numeric($request->input("id"))) {
                $result = false;
            } else if ($request->input("id") <= 0) {
                $result = false;
            }
        }
        if ($request->has("id_usuario_origen") && $request->filled("id_usuario_origen")) {
            if (!is_numeric($request->input("id_usuario_origen"))) {
                $result = false;
            } else if ($request->input("id_usuario_origen") <= 0) {
                $result = false;
            }
        }
        if ($request->has("Tipo") && $request->filled("Tipo")) {
            if (!is_numeric($request->input("Tipo"))) {
                $result = false;
            } else if ($request->input("Tipo") <= 0) {
                $result = false;
            }
        }
        if ($request->has("Direccion") && $request->filled("Direccion")) {
            if (!is_string($request->input("Direccion"))) {
                $result = false;
            } else if ($request->input("Direccion") <= 0) {
                $result = false;
            }
        }
        return $result;
    }









    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_inter__calif__publics(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            if (
                !is_numeric($offset) ||
                !is_numeric($ammount)
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            } else if (
                $offset < 0 ||
                $ammount < 0
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            }

            if (!$this->Internal_Validation_Inter__Calif__Publics($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }

            $Inter_Calif_Public = null;
            //Si es obtener con filtro
            if ($request->has("id") && $request->filled("id")) {
                $Inter_Calif_Public = Inter_Calif_Public::where([['id', $request->input("id")]])->get();
            } else if ($request->has("id_calificacion") && $request->filled("id_calificacion")) {
                $Inter_Calif_Public = DB::select('select * from inter__calif__publics Where id_calificacion = ' . $request->input('id_calificacion') . ' LIMIT ' . $offset . ',' . $ammount);
            } else if ($request->has("id_publicacion") && $request->filled("id_publicacion")) {
                $Inter_Calif_Public = DB::select('select * from inter__calif__publics Where id_publicacion = ' . $request->input('id_publicacion') . ' LIMIT ' . $offset . ',' . $ammount);
            } else {
                //Si es obtener normal y sin filtro
                if ($use_for == "A") {
                    $Inter_Calif_Public = Inter_Calif_Public::get();
                } else {
                    //Si es obtener TODO
                    $Inter_Calif_Public = DB::select('select * from inter__calif__publics LIMIT ' . $offset . ',' . $ammount);
                }
            }

            return $Inter_Calif_Public;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el inter__calif__publics, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_inter__calif__publics(Request $request)
    {
        try {
            if (!$this->Internal_Validation_Inter__Calif__Publics($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }
            $Inter_Calif_Public = null;
            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                //To avoid create it twice
                $ifAlreadyExist = Inter_Calif_Public::where([['id_calificacion', $request->input("id_calificacion")], ['id_publicacion', $request->input("id_publicacion")]])->get();
                if ($ifAlreadyExist->count() > 0) {
                    session()->flash('failure', "Dato intermedio ya existe, ignorando la solicitud");
                    return "{}";
                }

                $Inter_Calif_Public = new Inter_Calif_Public();
                $Inter_Calif_Public->id_calificacion =  $request->has("id_calificacion") ? $request->input("id_calificacion") : $Inter_Calif_Public->id_calificacion;
                $Inter_Calif_Public->id_publicacion =  $request->has("id_publicacion") ? $request->input("id_publicacion") : $Inter_Calif_Public->id_publicacion;
                $Inter_Calif_Public->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $Inter_Calif_Public = Inter_Calif_Public::where([['id', $request->input("id")]])->first();
                if ($Inter_Calif_Public != null) {
                    $Inter_Calif_Public->id_calificacion =  $request->has("id_calificacion") ? $request->input("id_calificacion") : $Inter_Calif_Public->id_calificacion;
                    $Inter_Calif_Public->id_publicacion =  $request->has("id_publicacion") ? $request->input("id_publicacion") : $Inter_Calif_Public->id_publicacion;
                    $Inter_Calif_Public->update();
                }
            }

            if ($request->isMethod('delete')) {
                $Inter_Calif_Public = Inter_Calif_Public::where([['id', $request->input("id")]])->first();
                if ($Inter_Calif_Public == null) {
                    return false;
                }
                return $Inter_Calif_Public = $Inter_Calif_Public->delete();
            }

            return $Inter_Calif_Public;
        } catch (Exception  $e) {
            DB::rollback();
            session()->flash('failure', "Error al procesar la solicitud: por favor intente de nuevo");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    private function Internal_Validation_Inter__Calif__Publics(Request $request)
    {
        $result = true;
        if ($request->has("id") && $request->filled("id")) {
            if (!is_numeric($request->input("id"))) {
                $result = false;
            } else if ($request->input("id") <= 0) {
                $result = false;
            }
        }
        if ($request->has("id_calificacion") && $request->filled("id_calificacion")) {
            if (!is_numeric($request->input("id_calificacion"))) {
                $result = false;
            } else if ($request->input("id_calificacion") <= 0) {
                $result = false;
            }
        }
        if ($request->has("id_publicacion") && $request->filled("id_publicacion")) {
            if (!is_numeric($request->input("id_publicacion"))) {
                $result = false;
            } else if ($request->input("id_publicacion") <= 0) {
                $result = false;
            }
        }
        return $result;
    }

    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_calificacions(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            if (
                !is_numeric($offset) ||
                !is_numeric($ammount)
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            } else if (
                $offset < 0 ||
                $ammount < 0
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            }

            if (!$this->Internal_Validation_Calificacions($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }

            $Calificacion = null;
            //Si es obtener con filtro
            if ($request->has("id") && $request->filled("id")) {
                $Calificacion = Calificacion::where([['id', $request->input("id")]])->get();
            } else if ($request->has("id_usuario_origen") && $request->filled("id_usuario_origen")) {
                $Calificacion = DB::select('select * from calificacions Where id_usuario_origen = ' . $request->input('id_usuario_origen') . ' LIMIT ' . $offset . ',' . $ammount);
            } else if ($request->has("id_usuario_destino") && $request->filled("id_usuario_destino")) {
                $Calificacion = DB::select('select * from calificacions Where id_usuario_destino = ' . $request->input('id_usuario_destino') . ' LIMIT ' . $offset . ',' . $ammount);
            } else if ($request->has("id_publicacion") && $request->filled("id_publicacion")) {
                $Calificacion = DB::select('select * from calificacions Where id_publicacion = ' . $request->input('id_publicacion') . ' LIMIT ' . $offset . ',' . $ammount);
            } else if ($request->has("Tipo_Calificacion") && $request->filled("Tipo_Calificacion")) {
                $Calificacion = DB::select('select * from calificacions Where Tipo_Calificacion = ' . $request->input('Tipo_Calificacion') . ' LIMIT ' . $offset . ',' . $ammount);
            } else if ($request->has("Puntuacion") && $request->filled("Puntuacion")) {
                $Calificacion = DB::select('select * from calificacions Where Puntuacion = ' . $request->input('Puntuacion') . ' LIMIT ' . $offset . ',' . $ammount);
            } else {
                //Si es obtener normal y sin filtro
                if ($use_for == "A") {
                    $Calificacion = Calificacion::get();
                } else {
                    //Si es obtener TODO
                    $Calificacion = DB::select('select * from calificacions LIMIT ' . $offset . ',' . $ammount);
                }
            }

            return $Calificacion;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el calificacions, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_calificacions(Request $request)
    {
        try {
            if (!$this->Internal_Validation_Calificacions($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }
            $Calificacion = null;
            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                //To avoid create it twice
                $ifAlreadyExist = Calificacion::where([
                    ['id_usuario_origen', $request->input("id_usuario_origen")],
                    ['id_usuario_destino', $request->input("id_usuario_destino")],
                    ['id_publicacion', $request->input("id_publicacion")]
                ])->get();
                if ($ifAlreadyExist->count() > 0) {
                    session()->flash('failure', "Dato intermedio ya existe, ignorando la solicitud");
                    return "{}";
                }

                $Calificacion = new Calificacion();
                $Calificacion->id_usuario_origen =  $request->has("id_usuario_origen") ? $request->input("id_usuario_origen") : $Calificacion->id_usuario_origen;
                $Calificacion->id_usuario_destino =  $request->has("id_usuario_destino") ? $request->input("id_usuario_destino") : $Calificacion->id_usuario_destino;
                $Calificacion->id_publicacion =  $request->has("id_publicacion") ? $request->input("id_publicacion") : $Calificacion->id_publicacion;
                $Calificacion->Puntuacion =  $request->has("Puntuacion") ? $request->input("Puntuacion") : $Calificacion->Puntuacion;
                $Calificacion->Tipo_Calificacion =  $request->has("Tipo_Calificacion") ? $request->input("Tipo_Calificacion") : $Calificacion->Tipo_Calificacion;
                $Calificacion->Comentario =  $request->has("Comentario") ? $request->input("Comentario") : $Calificacion->Comentario;
                $Calificacion->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $Calificacion = Calificacion::where([['id', $request->input("id")]])->first();
                if ($Calificacion != null) {
                    $Calificacion->id_usuario_origen =  $request->has("id_usuario_origen") ? $request->input("id_usuario_origen") : $Calificacion->id_usuario_origen;
                    $Calificacion->id_usuario_destino =  $request->has("id_usuario_destino") ? $request->input("id_usuario_destino") : $Calificacion->id_usuario_destino;
                    $Calificacion->id_publicacion =  $request->has("id_publicacion") ? $request->input("id_publicacion") : $Calificacion->id_publicacion;
                    $Calificacion->Puntuacion =  $request->has("Puntuacion") ? $request->input("Puntuacion") : $Calificacion->Puntuacion;
                    $Calificacion->Tipo_Calificacion =  $request->has("Tipo_Calificacion") ? $request->input("Tipo_Calificacion") : $Calificacion->Tipo_Calificacion;
                    $Calificacion->Comentario =  $request->has("Comentario") ? $request->input("Comentario") : $Calificacion->Comentario;
                    $Calificacion->update();
                }
            }

            if ($request->isMethod('delete')) {
                $Calificacion = Calificacion::where([['id', $request->input("id")]])->first();
                if ($Calificacion == null) {
                    return false;
                }
                return $Calificacion = $Calificacion->delete();
            }

            return $Calificacion;
        } catch (Exception  $e) {
            DB::rollback();
            session()->flash('failure', "Error al procesar la solicitud: por favor intente de nuevo");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    private function Internal_Validation_Calificacions(Request $request)
    {
        $result = true;
        if ($request->has("id") && $request->filled("id")) {
            if (!is_numeric($request->input("id"))) {
                $result = false;
            } else if ($request->input("id") <= 0) {
                $result = false;
            }
        }
        if ($request->has("id_usuario_origen") && $request->filled("id_usuario_origen")) {
            if (!is_numeric($request->input("id_usuario_origen"))) {
                $result = false;
            } else if ($request->input("id_usuario_origen") <= 0) {
                $result = false;
            }
        }
        if ($request->has("id_usuario_destino") && $request->filled("id_usuario_destino")) {
            if (!is_numeric($request->input("id_usuario_destino"))) {
                $result = false;
            } else if ($request->input("id_usuario_destino") <= 0) {
                $result = false;
            }
        }
        if ($request->has("id_publicacion") && $request->filled("id_publicacion")) {
            if (!is_numeric($request->input("id_publicacion"))) {
                $result = false;
            } else if ($request->input("id_publicacion") <= 0) {
                $result = false;
            }
        }
        if ($request->has("Puntuacion") && $request->filled("Puntuacion")) {
            if (!is_numeric($request->input("Puntuacion"))) {
                $result = false;
            } else if ($request->input("Puntuacion") <= 0) {
                $result = false;
            }
        }
        if ($request->has("Tipo_Calificacion") && $request->filled("Tipo_Calificacion")) {
            if (!is_numeric($request->input("Tipo_Calificacion"))) {
                $result = false;
            } else if ($request->input("Tipo_Calificacion") <= 0) {
                $result = false;
            }
        }
        if ($request->has("Comentario") && $request->filled("Comentario")) {
            if (!is_string($request->input("Comentario"))) {
                $result = false;
            } else if ($request->input("Comentario") <= 0) {
                $result = false;
            }
        }
        return $result;
    }

    public function getUserTypeByUserId(int $userId){
        return Tipo_Usuario::where([["id_usuario", $userId]])->get();
    }




    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_tipo__usuarios(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            if (
                !is_numeric($offset) ||
                !is_numeric($ammount)
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            } else if (
                $offset < 0 ||
                $ammount < 0
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            }

            if (!$this->Internal_Validation_Tipo__Usuarios($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }

            $Tipo_Usuario = null;
            //Si es obtener con filtro
            if ($request->has("id") && $request->filled("id")) {
                $Tipo_Usuario = Tipo_Usuario::where([['id', $request->input("id")]])->get();
            } else if ($request->has("id_usuario") && $request->filled("id_usuario")) {
                $Tipo_Usuario = DB::select('select * from tipo__usuarios Where id_usuario = ' . $request->input('id_usuario') . ' LIMIT ' . $offset . ',' . $ammount);
            } else if ($request->has("Nombre_Tipo_Usuario") && $request->filled("Nombre_Tipo_Usuario")) {
                $Tipo_Usuario = DB::select('select * from tipo__usuarios Where Nombre_Tipo_Usuario = \'' . $request->input('Nombre_Tipo_Usuario') . '\' LIMIT ' . $offset . ',' . $ammount);
            } else {
                //Si es obtener normal y sin filtro
                if ($use_for == "A") {
                    $Tipo_Usuario = Tipo_Usuario::get();
                } else {
                    //Si es obtener TODO
                    $Tipo_Usuario = DB::select('select * from tipo__usuarios LIMIT ' . $offset . ',' . $ammount);
                }
            }

            return $Tipo_Usuario;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el tipo__usuarios, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_tipo__usuarios(Request $request)
    {
        try {
            if (!$this->Internal_Validation_Tipo__Usuarios($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }

            $Tipo_Usuario = null;
            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                //To avoid create it twice
                $ifAlreadyExist = Tipo_Usuario::where([
                    ['id_usuario', $request->input("id_usuario")],
                    ['Nombre_Tipo_Usuario', $request->input("Nombre_Tipo_Usuario")]
                ])->get();
                if ($ifAlreadyExist->count() > 0) {
                    session()->flash('failure', "Dato intermedio ya existe, ignorando la solicitud");
                    return "{}";
                }

                $Tipo_Usuario = new Tipo_Usuario();
                $Tipo_Usuario->id_usuario =  $request->has("id_usuario") ? $request->input("id_usuario") : $Tipo_Usuario->id_usuario;
                $Tipo_Usuario->Nombre_Tipo_Usuario =  $request->has("Nombre_Tipo_Usuario") ? $request->input("Nombre_Tipo_Usuario") : $Tipo_Usuario->Nombre_Tipo_Usuario;
                $Tipo_Usuario->Etiqueta =  $request->has("Etiqueta") ? $request->input("Etiqueta") : $Tipo_Usuario->Etiqueta;
                $Tipo_Usuario->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $Tipo_Usuario = Tipo_Usuario::where([['id_usuario', $request->input("id_usuario")]])->first();
                if ($Tipo_Usuario != null) {
                    $Tipo_Usuario->id_usuario =  $request->has("id_usuario") ? $request->input("id_usuario") : $Tipo_Usuario->id_usuario;
                    $Tipo_Usuario->Nombre_Tipo_Usuario =  $request->has("Nombre_Tipo_Usuario") ? $request->input("Nombre_Tipo_Usuario") : $Tipo_Usuario->Nombre_Tipo_Usuario;
                    $Tipo_Usuario->Etiqueta =  $request->has("Etiqueta") ? $request->input("Etiqueta") : $Tipo_Usuario->Etiqueta;
                    $Tipo_Usuario->update();
                }
            }

            if ($request->isMethod('delete')) {
                $Tipo_Usuario = Tipo_Usuario::where([['id', $request->input("id")]])->first();
                if ($Tipo_Usuario == null) {
                    return false;
                }
                return $Tipo_Usuario = $Tipo_Usuario->delete();
            }

            return $Tipo_Usuario;
        } catch (Exception  $e) {
            Log::error("Error on tipo!!!");
            DB::rollback();
            session()->flash('failure', "Error al procesar la solicitud: por favor intente de nuevo");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    private function Internal_Validation_Tipo__Usuarios(Request $request)
    {
        $result = true;
        if ($request->has("id") && $request->filled("id")) {
            if (!is_numeric($request->input("id"))) {
                $result = false;
            } else if ($request->input("id") <= 0) {
                $result = false;
            }
        }
        if ($request->has("Nombre_Tipo_Usuario") && $request->filled("Nombre_Tipo_Usuario")) {
            if (!is_string($request->input("Nombre_Tipo_Usuario"))) {
                $result = false;
            } else if ($request->input("Nombre_Tipo_Usuario") <= 0) {
                $result = false;
            }
        }
        if ($request->has("Etiqueta") && $request->filled("Etiqueta")) {
            if (!is_numeric($request->input("Etiqueta"))) {
                $result = false;
            } else if ($request->input("Etiqueta") <= 0) {
                $result = false;
            }
        }
        return $result;
    }

    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_usuarios(Request $request, $use_for = '-', $offset = 0, $ammount = 20)
    {
        try {
            if (
                !is_numeric($offset) ||
                !is_numeric($ammount)
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            } else if (
                $offset < 0 ||
                $ammount < 0
            ) {
                session()->flash('failure', "Error al obtener la Proyecto, por favor intente nuevamente");
                //print_r("Error ---: " . $e->getMessage());
                return "{}";
            }

            if (!$this->Internal_Validation_Usuarios($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }

            $usuarios = null;
            //Valida que use_for no sea nulo, dado que el valor viene generalmente de un parámetro opcional
            if (!isset($use_for) || $use_for == null) {
                $use_for = 'A';
            }
            //Si es obtener con filtro
            if ($request->has("id") && $request->filled("id")) {
                $usuarios = User::where([['id', $request->input("id")]])->get();
            } else if ($request->has('Nombre_Tipo_Usuario') && $request->filled('Nombre_Tipo_Usuario')) {
                $usuarios = DB::select('SELECT users.* FROM users JOIN tipo__usuarios ON users.id = tipo__usuarios.id_usuario WHERE tipo__usuarios.Nombre_Tipo_Usuario = \'' . $request->input('Nombre_Tipo_Usuario') . '\' LIMIT ' . $offset . ',' . $ammount);
            } else {
                //Si es obtener normal y sin filtro
                if ($use_for == 'A') {
                    $usuarios = User::get();
                } else {
                    //Si es obtener TODO
                    $usuarios = DB::select('select * from users LIMIT ' . $offset . ',' . $ammount);
                }
            }

            return $usuarios;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el users, por favor intente nuevamente");
            //print_r("Error InterceptParaSolicitud: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_usuarios(Request $request, $hard_delete = false)
    {
        try {
            //dd($request);
            if (!$this->Internal_Validation_Usuarios($request)) {
                session()->flash('failure', "Data enviada incorrecta: por favor corroja intente nuevamente");
                return "{}";
            }
            $User = null;
            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                $User = new User();
                $User->name =  $request->has("name") ? $request->input("name") : $User->name;
                $User->Apellido_Materno =  $request->has("Apellido_Materno") ? $request->input("Apellido_Materno") : $User->Apellido_Materno;
                $User->Apellido_Paterno =  $request->has("Apellido_Paterno") ? $request->input("Apellido_Paterno") : $User->Apellido_Paterno;

                $User->Age =  $request->has("Age") ? $request->input("Age") : $User->Age;

                $User->Telefono =  $request->has("Telefono") ? $request->input("Telefono") : $User->Telefono;
                $User->Direccion =  $request->has("Direccion") ? $request->input("Direccion") : $User->Direccion;
                $User->Ciudad =  $request->has("Ciudad") ? $request->input("Ciudad") : $User->Ciudad;

                $User->email =  $request->has("email") ? $request->input("email") : $User->email;
                $User->email_verified_at =  $request->has("email_verified_at") ? $request->input("email_verified_at") : $User->email_verified_at;
                $User->password =  $request->has("password") ? $request->input("password") : $User->password;

                $User->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $User = User::where([['id', $request->input("id")]])->first();
                if ($User != null) {
                    $User->name =  $request->has("name") ? $request->input("name") : $User->name;
                    $User->Apellido_Materno =  $request->has("Apellido_Materno") ? $request->input("Apellido_Materno") : $User->Apellido_Materno;
                    $User->Apellido_Paterno =  $request->has("Apellido_Paterno") ? $request->input("Apellido_Paterno") : $User->Apellido_Paterno;

                    $User->Age =  $request->has("Age") ? $request->input("Age") : $User->Age;

                    $User->Telefono =  $request->has("Telefono") ? $request->input("Telefono") : $User->Telefono;
                    $User->Direccion =  $request->has("Direccion") ? $request->input("Direccion") : $User->Direccion;
                    $User->Ciudad =  $request->has("Ciudad") ? $request->input("Ciudad") : $User->Ciudad;

                    $User->email =  $request->has("email") ? $request->input("email") : $User->email;
                    $User->email_verified_at =  $request->has("email_verified_at") ? $request->input("email_verified_at") : $User->email_verified_at;
                    $User->password =  $request->has("password") ? $request->input("password") : $User->password;

                    $User->update();
                }
            }

            if ($request->isMethod('delete')) {
                $User = User::where([['id', $request->input("id")]])->first();
                if ($User == null) {
                    return false;
                }

                return $User = $User->delete();
            }

            return $User;
        } catch (Exception  $e) {
            DB::rollback();
            session()->flash('failure', "Error al procesar la solicitud: por favor intente de nuevo");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    private function Internal_Validation_Usuarios(Request $request)
    {
        $result = true;
        if ($request->has("id") && $request->filled("id")) {
            if (!is_numeric($request->input("id"))) {
                $result = false;
            } else if ($request->input("id") <= 0) {
                $result = false;
            }
        }
        if ($request->has("name") && $request->filled("name")) {
            if (!is_string($request->input("name"))) {
                $result = false;
            } else if ($request->input("name") <= 0) {
                $result = false;
            }
        }
        if ($request->has("Apellido_Materno") && $request->filled("Apellido_Materno")) {
            if (!is_string($request->input("Apellido_Materno"))) {
                $result = false;
            } else if ($request->input("Apellido_Materno") <= 0) {
                $result = false;
            }
        }
        if ($request->has("Apellido_Paterno") && $request->filled("Apellido_Paterno")) {
            if (!is_string($request->input("Apellido_Paterno"))) {
                $result = false;
            } else if ($request->input("Apellido_Paterno") <= 0) {
                $result = false;
            }
        }
        if ($request->has("Age") && $request->filled("Age")) {
            if (!is_string($request->input("Age"))) {
                $result = false;
            } else if ($request->input("Age") <= 0) {
                $result = false;
            }
        }
        if ($request->has("Telefono") && $request->filled("Telefono")) {
            if (!is_string($request->input("Telefono"))) {
                $result = false;
            } else if ($request->input("Telefono") <= 0) {
                $result = false;
            }
        }
        if ($request->has("Direccion") && $request->filled("Direccion")) {
            if (!is_string($request->input("Direccion"))) {
                $result = false;
            } else if ($request->input("Direccion") <= 0) {
                $result = false;
            }
        }
        if ($request->has("Ciudad") && $request->filled("Ciudad")) {
            if (!is_string($request->input("Ciudad"))) {
                $result = false;
            } else if ($request->input("Ciudad") <= 0) {
                $result = false;
            }
        }
        if ($request->has("email") && $request->filled("email")) {
            if (!is_string($request->input("email"))) {
                $result = false;
            } else if ($request->input("email") <= 0) {
                $result = false;
            }
        }
        if ($request->has("password") && $request->filled("password")) {
            if (!is_string($request->input("password"))) {
                $result = false;
            } else if ($request->input("password") <= 0) {
                $result = false;
            }
        }
        return $result;
    }

    public function Revisar_Acceso($userid, $eval)
    {
        try {
            $permisos = DB::select('SELECT tipo__usuarios.* FROM tipo__usuarios WHERE  id_usuario = ' . $userid . ' AND Etiqueta = ' . $eval);
            foreach ($permisos as $key => $value) {
                if (str_contains($value->Etiqueta, $eval)) {
                    return true;
                }
            }
            return false;
        } catch (Exception  $e) {
            print_r("Error ---: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Funciones extras para funciones con tipos de datos especificos
     */

    /**
     * Busca si ya existe un usuario con ese mail.
     *
     * @param Request $request Objeto del request con el mail
     * @return User|null registro de usuario encontrado por el mail o null si no existe.
     */
    public function findUserByEmail(Request $request) : User|null{
        $usuarios = null;
        if($request->has("email")){
            $usuarios = User::where([['email', $request->input("email")]])->get();
        }
        return sizeof($usuarios) > 0 ? $usuarios[0] : null;
    }

    /**
     * Busca una oferta por el id y regresa la información del join con la tabla publicacions
     *
     * @param int $ofertaId id de la tabla oferta a buscar
     * @return Oferta Registro de oferta join con publicacions
     */
    public function findOfertaById(int $ofertaId){
        DB::enableQueryLog();
        LOG::info("search for oferta");
        $oferta = null;
        try{
            //$oferta =  Oferta::where(['id', $ofertaId]);
            //$oferta = Oferta::with("Publicacion:id,nombre,descripcion")->select("Publicacion:*")->find($ofertaId);
            $oferta = DB::table("ofertas")
                        ->join('publicacions', "ofertas.id_publicacion", "=", "publicacions.id")
                        ->select("publicacions.*", "ofertas.Precio")
                        ->where("ofertas.id", "=", $ofertaId)
                        ->get()
                        ->first();
            $queries = DB::getQueryLog();
            $last_query = end($queries);
            LOG::info("Used Query ");
            LOG::info($last_query);
            LOG::info("with oferId ".$ofertaId);
            LOG::info("Oferta found");
            LOG::info(json_encode($oferta));
        }
        catch(Exception $e){
            Log::info("oferta search failed ".$e->getMessage());
        }
        return $oferta;
    }

    /**
     * Funcion para encontrar una demanda por su ID
     *
     * @param int $demandaId Id de la demanda
     * @return Demanda registro de la demanda, join con publicacion
     */

    public function findDemandaById(int $demandaId){
        $demanda = DB::table("demandas")
            ->join('publicacions', "demandas.id_publicacion", "=", "publicacions.id")
            ->select("publicacions.*", "demandas.Presupuesto")
            ->where("demandas.id", "=", $demandaId)
            ->get()
            ->first();
        LOG::info("with demandaId ".$demandaId);
        return $demanda;
    }

    /**
     * Funcion para crear una oferta
     *
     * @param Request $request Request con la informacion de la oferta
     * @return Oferta registro de la oferta creada
     */
    public function crearOferta(Request $request){
        $oferta = new Oferta();
        $oferta->id_publicacion = $request->id_publicacion;
        $oferta->Precio = $request->Precio;
        $oferta->save();
        return $oferta;
    }

    /**
     * Funcion para crear una demanda
     *
     * @param Request $request Request con la información de la demanda
     * @return Demanda registro de la demanda creada
     */
    public function crearDemanda(Request $request){
        $demanda = new Demanda();
        $demanda->id_publicacion = $request->id_publicacion;
        $demanda->Presupuesto = $request->Presupuesto;
        $demanda->save();
        return $demanda;
    }

    /**
     * Funcion para actualizar una oferta
     *
     * @param Request $request Información de la oferta
     */
    public function updatePublicacion(Request $request){
        $publicacionId = $this->findPublicacionIdFromOferta($request->id);
        $publicacion = $this->findSimplePublicacion($publicacionId->id);
        $publicacion->Nombre = $request->Nombre;
        $publicacion->Descripcion = $request->Descripcion;
        $publicacion->save();
    }

    /**
     * Funcion para actualizar una demanda
     *
     * @param Request $request Request con la información de la demanda
     */
    public function updatePublicacionDemanda(Request $request){
        $publicacionId = $this->findPublicacionIdFromDemanda($request->id);
        $publicacion = $this->findSimplePublicacion($publicacionId->id);
        $publicacion->Nombre = $request->Nombre;
        $publicacion->Descripcion = $request->Descripcion;
        $publicacion->save();
    }

    /**
     * Busqueda de una publicacion por su ID
     *
     * @param int $idPublicacion
     * @return Publicacion publicacion encontrada
     */
    public function findSimplePublicacion(int $idPublicacion){
        $publicacion = Publicacion::find($idPublicacion);
        LOG::info("Found publicacion");
        LOG::info(json_encode($publicacion));
        return $publicacion;
    }

    /**
     * Funcion para guardar una oferta
     *
     * @param Request $request informacion de la oferta a guardar
     */
    public function saveOferta(Request $request){
        $oferta = $this->findSimpleOferta($request->id);
        $oferta->Precio = $request->Precio;
        $oferta->save();
    }

    /**
     * Funcion para guardar una demanda
     *
     * @param Request $request informacion de la demanda a guardar
     */
    public function saveDemanda(Request $request){
        $demanda = $this->findSimpleDemanda($request->id);
        $demanda->Presupuesto = $request->Presupuesto;
        $demanda->save();
    }

    /**
     * Busqueda de una demanda por su ID
     *
     * @param int $demandaId id de la demanda a buscar
     * @return Demanda demanda encontrada
     */
    public function findSimpleDemanda(int $demandaId){
        $demanda = Demanda::find($demandaId);
        LOG::info("Single demanda found");
        LOG::info(json_encode($demanda));
        return $demanda;
    }

    /**
     * Busqueda de una oferta por su ID
     *
     * @param int $ofertaId
     * @return Oferta oferta encontrada
     */
    public function findSimpleOferta(int $ofertaId){
        $oferta = Oferta::find($ofertaId);
        LOG::info("Single oferta found");
        LOG::info(json_encode($oferta));
        return $oferta;
    }

    /**
     * Busqueda de publicacion join con Ofertas
     *
     * @param $ofertaId
     * @return Publicacion publicacion encontrada por id de oferta
     */
    public function findPublicacionIdFromOferta(int $ofertaId){
        $oferta = DB::table("ofertas")
            ->join('publicacions', "ofertas.id_publicacion", "=", "publicacions.id")
            ->select("publicacions.id")
            ->where("ofertas.id", "=", $ofertaId)
            ->get()
            ->first();
        LOG::info("Single oferta found");
        LOG::info(json_encode($oferta));
        return $oferta;
    }

    /**
     * Busqueda de publicacion join demandas
     *
     * @param int $ofertaId id de la oferta
     * @return Publicacion publicacion encontrada por id de demanda
     */
    public function findPublicacionIdFromDemanda(int $ofertaId){
        $oferta = DB::table("demandas")
            ->join('publicacions', "demandas.id_publicacion", "=", "publicacions.id")
            ->select("publicacions.id")
            ->where("demandas.id", "=", $ofertaId)
            ->get()
            ->first();
        LOG::info("Single demanda found");
        LOG::info(json_encode($oferta));
        return $oferta;
    }

    /**
     * Busqueda de todas las publicaciones para mostrar en lista
     *
     * @param String $orderBy nombre del campo respecto al cual se ordenará
     * @param String $direction dirección de ordenamiento ASC o DESC
     * @param int $type filtro por tipo -1: ambos, 0: ofertas, 1: demandas
     * @param String $str filtro por wildcard para el campo publicacions.nombre
     * @param int $visible filtro para publicaciones visibles, 1: solo visibles
     * @return Publicacion[] Lista de publicaciones
     */
    public function findAllPublicacion($orderBy, $direction, $type, $str, $user, $visible = 1){
        $publicaciones = DB::table("publicacions")
        ->join('users', 'users.id', '=', 'publicacions.id_usuario')
        ->leftJoin('demandas', 'demandas.id_publicacion', '=', 'publicacions.id')
        ->leftJoin('ofertas', 'ofertas.id_publicacion', '=', 'publicacions.id')
        ->select(
            'publicacions.id as publicacion_id',
            'publicacions.Nombre',
            'publicacions.Descripcion',
            'users.id as user_id',
            'users.name',
            'users.Apellido_Paterno',
            'users.Apellido_Materno',
            'demandas.Presupuesto',
            'ofertas.Precio',
            'publicacions.Tipo_Publicacion',
            'publicacions.visible'
        )->orderBy($orderBy, $direction);
        if($type > -1)
            $publicaciones->where("publicacions.Tipo_Publicacion", $type);
        if($str != "")
            $publicaciones->where("publicacions.Nombre", "like", "%$str%");
        if($visible == 1)
            $publicaciones->where("publicacions.visible", true);
        if($user != null)
            $publicaciones->where("publicacions.id_usuario", $user);
        return $publicaciones->paginate(10);
    }

    /**
     * Consulta para balance, consta en dos consultas, saldo positivo y negativo a partir de los registros de transacciones
     * de la tabla transaccions y la cantidad registrada en inter__transa__publics
     * Consulta preparada para validación o saldo informativo
     *
     * @param int $userId Id del usuario para crear su balance
     * @return int Saldo total
     */
    public function balance($userId){
        $incomingTransactions = DB::table('transaccions')
            ->join('inter__transa__publics', 'inter__transa__publics.id_transaccion', '=', 'transaccions.id')
            ->where('transaccions.id_usuario_destino', $userId)
            ->select(DB::raw('SUM(COALESCE(inter__transa__publics.cantidad, 0)) as saldo'))
            ->groupBy('transaccions.id_usuario_origen', 'transaccions.id_usuario_destino');

        $outgoingTransactions = DB::table('transaccions')
            ->join('inter__transa__publics', 'inter__transa__publics.id_transaccion', '=', 'transaccions.id')
            ->where('transaccions.id_usuario_origen', $userId)
            ->select(DB::raw('SUM(COALESCE(inter__transa__publics.cantidad, 0)) * -1 as saldo'))
            ->groupBy('transaccions.id_usuario_origen', 'transaccions.id_usuario_destino');

        $balanceQuery = DB::table(DB::raw("({$incomingTransactions->toSql()}) as incoming"))
            ->mergeBindings($incomingTransactions)
            ->unionAll($outgoingTransactions);

        $totalBalance = DB::table(DB::raw("({$balanceQuery->toSql()}) as balance"))
            ->mergeBindings($balanceQuery)
            ->select(DB::raw('SUM(saldo) as saldo'))
            ->first();

        $totalSaldo = $totalBalance->saldo ?? 0;
        return $totalSaldo;
    }

    public function makeTransaction($userOriginId, $userTargetId, $type){
        $transaction = new Transaccion();
        $transaction->id_usuario_origen = $userOriginId;
        $transaction->id_usuario_destino = $userTargetId;
        $transaction->Tipo_Transaccion = 0;
        $transaction->save();
        return $transaction;
    }

    public function savePurchase($idPublicacion, $idTransaccion, $cantidad){
        $itp = new Inter_Transa_Public();
        $itp->id_publicacion = $idPublicacion;
        $itp->id_transaccion = $idTransaccion;
        $itp->cantidad = $cantidad;
        $itp->save();
        return $itp;
    }

    public function saveImagen($filename, $idUsuario){
        $image = new Imagen();
        $image->Direccion = $filename;
        $image->id_usuario_origen = $idUsuario;
        $image->Tipo = 0;
        $image->save();
        return $image;
    }

}
