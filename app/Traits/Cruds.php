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

    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_inter__transa__publics(Request $request, $use_for = '-', $offset = 0, $ammount = 20)
    {
        try {
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











    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_proyectos(Request $request, $use_for = '-', $offset = 0, $ammount = 20)
    {
        try {
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
            $Proyecto = null;
            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                $Proyecto = new Proyecto();
                $Proyecto->id_publicacion = $request->has("id_publicacion") ? $request->input("id_publicacion") : $Proyecto->id_publicacion;
                $Proyecto->Porcentaje_Avance =  $request->has("Porcentaje_Avance") ? $request->input("Porcentaje_Avance") : $Proyecto->Porcentaje_Avance;
                $Proyecto->save();

                $this->Internal_Editar_publicacions($Proyecto);
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $Proyecto = Proyecto::where([['id', $request->input("id")]])->first();
                if ($Proyecto != null) {
                    $Proyecto->id_publicacion = $request->has("id_publicacion") ? $request->input("id_publicacion") : $Proyecto->id_publicacion;
                    $Proyecto->Porcentaje_Avance =  $request->has("Porcentaje_Avance") ? $request->input("Porcentaje_Avance") : $Proyecto->Porcentaje_Avance;
                    $Proyecto->update();
                }
            }

            if ($request->isMethod('delete')) {
                $Proyecto = Proyecto::where([['id', $request->input("id")]])->first();
                if ($Proyecto == null) {
                    return false;
                }

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

    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_voluntario(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
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











    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_mensaje(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
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

    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_publicacions(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
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
        try {
            $Publicacion = null;

            //Crear objeto nuevo
            if ($request->isMethod('post')) {
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
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }











    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_demandas(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
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
            $Demanda = null;

            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                $Demanda = new Demanda();
                $Demanda->id_publicacion = $request->has("id_publicacion") ? $request->input("id_publicacion") : $Demanda->id_publicacion;
                $Demanda->Presupuesto = $request->has("Presupuesto") ? $request->input("Presupuesto") : $Demanda->Presupuesto;
                $Demanda->save();

                $this->Internal_Editar_publicacions($Demanda);
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $Demanda = Demanda::where([['id', $request->input("id")]])->first();
                if ($Demanda != null) {
                    $Demanda->id_publicacion = $request->has("id_publicacion") ? $request->input("id_publicacion") : $Demanda->id_publicacion;
                    $Demanda->Presupuesto = $request->has("Presupuesto") ? $request->input("Presupuesto") : $Demanda->Presupuesto;
                    $Demanda->update();
                }
            }

            if ($request->isMethod('delete')) {
                $Demanda = Demanda::where([['id', $request->input("id")]])->first();
                if ($Demanda == null) {
                    return false;
                }

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

    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_ofertas(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
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
            $Oferta = null;

            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                $Oferta = new Oferta();
                $Oferta->id_publicacion = $request->has("id_publicacion") ? $request->input("id_publicacion") : $Oferta->id_publicacion;
                $Oferta->Precio = $request->has("Precio") ? $request->input("Precio") : $Oferta->Precio;
                $Oferta->save();

                $this->Internal_Editar_publicacions($Oferta);
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $Oferta = Oferta::where([['id', $request->input("id")]])->first();
                if ($Oferta != null) {
                    $Oferta->id_publicacion = $request->has("id_publicacion") ? $request->input("id_publicacion") : $Oferta->id_publicacion;
                    $Oferta->Precio = $request->has("Precio") ? $request->input("Precio") : $Oferta->Precio;
                    $Oferta->update();
                }
            }

            if ($request->isMethod('delete')) {
                $Oferta = Oferta::where([['id', $request->input("id")]])->first();
                if ($Oferta == null) {
                    return false;
                }

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

    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_imagens(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
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











    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_inter__calif__publics(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
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

    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_calificacions(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
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








    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_tipo__usuarios(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
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
                $Tipo_Usuario = Tipo_Usuario::where([['id', $request->input("id")]])->first();
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
            DB::rollback();
            session()->flash('failure', "Error al procesar la solicitud: por favor intente de nuevo");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    public function findUserByEmail(Request $request) : User | null{
        $usuarios = null;
        if($request->has("email")){
            $usuarios = User::where([['email', $request->input("email")]])->get();
        }
        return sizeof($usuarios) > 0 ? $usuarios[0] : null;
    }

    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_usuarios(Request $request, $use_for = '-', $offset = 0, $ammount = 20)
    {
        try {

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

    public function Internal_Editar_publicacions($stdClass)
    {
        try {
            $Publicacion = null;

            //Crear objeto nuevo
            if ($stdClass->id_publicacion == 0) {
                $Publicacion = new Publicacion();
                $Publicacion->id_usuario = $stdClass->id_usuario;
                $Publicacion->Nombre = $stdClass->Nombre;
                $Publicacion->Descripcion = $stdClass->Descripcion;
                $Publicacion->Tipo_Publicacion = $stdClass->Tipo_Publicacion;
                $Publicacion->save();
            }

            //Actualizar objeto
            if ($stdClass->id_publicacion != 0) {
                $Publicacion = Publicacion::where([['id', $Publicacion->id_publicacion]])->first();
                if ($Publicacion != null) {
                    $Publicacion->id_usuario = $stdClass->id_usuario != 0 ? $stdClass->id_usuario : $Publicacion->id_usuario;
                    $Publicacion->Nombre = $stdClass->Nombre != 0 ? $stdClass->Nombre : $Publicacion->Nombre;
                    $Publicacion->Descripcion = $stdClass->Descripcion != 0 ? $stdClass->Descripcion : $Publicacion->Descripcion;
                    $Publicacion->Tipo_Publicacion = $stdClass->Tipo_Publicacion != 0 ? $stdClass->Tipo_Publicacion : $Publicacion->Tipo_Publicacion;
                    $Publicacion->update();
                }
            }

            return $Publicacion;
        } catch (Exception  $e) {
            DB::rollback();
            session()->flash('failure', "Error al procesar la solicitud: por favor intente de nuevo");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
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
}
