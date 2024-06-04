<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
//use Illuminate\View\View;
//use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\Storage;
use App\Models\User;
//use App\Models\Producto;
use App\Models\Venta;
//use App\Models\Venta__detalles;
//use App\Models\Correos_Firmas;
use App\Models\Modulos_Contrato;
use App\Models\Venta_Detalle;
use App\Models\Contrato;
use App\Models\Doc_Tipo;
use App\Models\Inter_Mod_DocTipo;
use App\Models\Inter_Mod_Contrat;
//use App\Models\Rechazo_Contrato;
use App\Models\Detalle_Mod_Contrato;
use App\Models\Inter_Perf_Perm;
use App\Models\Inter_User_Perf;
use App\Models\Inter_User_Perm;
use App\Models\Inter_Perm_Doc_Tipos;
use App\Models\Inter_Perm_Contratos;
use App\Models\Inter_Mod_Perm;
use App\Models\Tipos_Contrato;
use App\Models\Permisos;
use App\Models\Perfiles;
use App\Models\PdfStorage;
//use Validator;

use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use PHPUnit\Framework\Constraint\Count;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

// use PDF;

trait Cruds
{
    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_venta(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {

            $ventas = null;
            //Si es obtener con filtro
            if ($request->has("id") && $request->filled("id")) {
                $ventas = Venta::where([['id', $request->input("id")]])->get();
            } else {
                //Si es obtener normal y sin filtro
                if ($use_for == 0) {
                    $ventas = DB::select('select * from DocuSwift.ventas LIMIT ' . $offset . ',' . $ammount);
                } else if ($use_for == 1) {
                    $ventas = DB::select('select * from DocuSwift.ventas WHERE Is_Vigente = 1 LIMIT ' . $offset . ',' . $ammount);
                } else if ($use_for == 2) {
                    $ventas = DB::select('select * from DocuSwift.ventas WHERE Is_Vigente != 1 LIMIT ' . $offset . ',' . $ammount);
                } else {
                    //Si es obtener TODO
                    $ventas = Venta::get();
                }
            }

            return $ventas;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener la venta, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_venta(Request $request)
    {
        try {
            $venta = null;
            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                $venta = new Venta();
                $venta->id_users = $request->has("id_users") ? $request->input("id_users") : $venta->id_users;
                $venta->Is_Factura_O_Boleta =  $request->has("Is_Factura_O_Boleta") ? $request->input("Is_Factura_O_Boleta") : $venta->Is_Factura_O_Boleta;
                $venta->Is_Vigente =  $request->has("Is_Vigente") ? $request->input("Is_Vigente") : $venta->Is_Vigente;
                $venta->Total =  $request->has("Total") ? $request->input("Total") : $venta->Total;
                $venta->Fecha = $request->has("Fecha") ? $request->input("Fecha") : $venta->Fecha;
                $venta->Giro =  $request->has("Giro") ? $request->input("Giro") : $venta->Giro;
                $venta->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $venta = Venta::where([['id', $request->input("id")]])->first();
                if ($venta != null) {
                    $venta->id_users = $request->has("id_users") ? $request->input("id_users") : $venta->id_users;
                    $venta->Is_Factura_O_Boleta =  $request->has("Is_Factura_O_Boleta") ? $request->input("Is_Factura_O_Boleta") : $venta->Is_Factura_O_Boleta;
                    $venta->Is_Vigente =  $request->has("Is_Vigente") ? $request->input("Is_Vigente") : $venta->Is_Vigente;
                    $venta->Total =  $request->has("Total") ? $request->input("Total") : $venta->Total;
                    $venta->Fecha = $request->has("Fecha") ? $request->input("Fecha") : $venta->Fecha;
                    $venta->Giro =  $request->has("Giro") ? $request->input("Giro") : $venta->Giro;
                    $venta->update();
                }
            }

            if ($request->isMethod('delete')) {
                $venta = Venta::where([['id', $request->input("id")]])->first();
                if ($venta == null) {
                    return false;
                }

                $Venta_Detalle = Venta_Detalle::where('id_Venta', $venta->id);
                if ($Venta_Detalle != null) {
                    $Venta_Detalle->delete();
                }

                $venta = $venta->delete();
            }

            return $venta;
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
    public function Obtener_modulos__contratos(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            $modulo__contrato = null;

            //Si es obtener con filtro
            if ($request->has("id") && $request->filled("id")) {
                $modulo__contrato = Modulos_Contrato::where([['id', $request->input("id")]])->first();
            } else if ($request->has("Rama")) {
                $modulo__contrato = DB::table('modulos__contratos')->where('Rama', 'like', '%' . $request->input("Rama") . '%')->get();
            } else if ($request->has("id_contrato") && $request->filled("id_contrato")) {
                $modulo__contrato = DB::table('inter__mod__contrats')
                    ->Join('modulos__contratos', 'inter__mod__contrats.id_modulos__contratos', '=', 'modulos__contratos.id')
                    ->where([
                        'inter__mod__contrats.id_contratos' => $request->input("id_contrato"),
                    ])
                    ->select('inter__mod__contrats.*', 'modulos__contratos.Nombre', 'modulos__contratos.Descripcion', 'modulos__contratos.Rama', 'modulos__contratos.id_tipos__modulos')
                    ->orderBy('inter__mod__contrats.Posicion', 'ASC')
                    ->get();
            } else if ($request->has("id_doctipo") && $request->filled("id_doctipo")) {
                $modulo__contrato = DB::table('inter__mod__doc_tipos')
                    ->Join('modulos__contratos', 'inter__mod__doc_tipos.id_modulos__contratos', '=', 'modulos__contratos.id')
                    ->where([
                        'inter__mod__doc_tipos.id_doc__tipos' => $request->input("id_doctipo"),
                    ])
                    ->select('inter__mod__doc_tipos.*', 'modulos__contratos.Nombre', 'modulos__contratos.Descripcion', 'modulos__contratos.Rama', 'modulos__contratos.id_tipos__modulos')
                    ->orderBy('inter__mod__doc_tipos.Posicion', 'ASC')
                    ->get();
            } else if ($use_for == 'A') {
                //Si es obtener TODO
                $modulo__contrato = Modulos_Contrato::get();
            }  else if(is_numeric($use_for)) {
                $modulo__contrato = DB::select('SELECT DISTINCT DocuSwift.modulos__contratos.* FROM DocuSwift.modulos__contratos JOIN DocuSwift.inter_mod_perms ON DocuSwift.modulos__contratos.id = DocuSwift.inter_mod_perms.id_modulos__contratos JOIN DocuSwift.inter_user_perms ON DocuSwift.inter_mod_perms.id_permisos = DocuSwift.inter_user_perms.id_permisos WHERE inter_user_perms.id_user = '.$use_for.' LIMIT '. $offset . ',' . $ammount);
            }
            else 
            {
                $modulo__contrato = DB::select('select * from DocuSwift.modulos__contratos LIMIT ' . $offset . ',' . $ammount);
            }

            if ($modulo__contrato != null) {
                // if (!$modulo__contrato instanceof Modulos_Contrato) {
                //dd($modulo__contrato);
                $varTemp = "";
                if (!is_object($modulo__contrato)) {
                    //dd(is_object($modulo__contrato));
                    foreach ($modulo__contrato as $key => $value) {
                        if (!$modulo__contrato[0] instanceof Modulos_Contrato) {   //dd(gettype($value));
                            $varTemp = Detalle_Mod_Contrato::where('id_modulos__contratos', $value->id)->first();
                        }
                        if ($varTemp != null) {
                            $modulo__contrato[$key]->Detalle = $varTemp->Detalle;
                        }
                    }
                } else {
                    //dd(gettype($modulo__contrato));
                    //dd("entro 3");
                    if ($modulo__contrato instanceof Collection) {
                        //dd("entro collection");
                        foreach ($modulo__contrato as $key => $value) {
                            if($value instanceof Modulos_Contrato){
                                //dd("entro mc");
                                $varTemp = Detalle_Mod_Contrato::where('id_modulos__contratos', $value->id)->first();
                            } else {
                                //dd($value);
                                $varTemp = Detalle_Mod_Contrato::where('id_modulos__contratos', $value->id_modulos__contratos)->first();
                            }
                            if ($varTemp != null) {
                                $modulo__contrato[$key]->Detalle = $varTemp->Detalle;
                            }
                        }
                    } else {
                        $varTemp = Detalle_Mod_Contrato::where('id_modulos__contratos', $modulo__contrato->id)->first();
                        if ($varTemp != null) {
                            $modulo__contrato->Detalle = $varTemp->Detalle;
                        }
                    }
                }
            }
            return $modulo__contrato;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el Modulo__contrato, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_modulos__contratos(Request $request)
    {
        try {
            $modulos__contratos = null;

            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                $modulos__contratos = new Modulos_Contrato();
                $modulos__contratos->id_tipos__modulos = $request->has("id_tipos__modulos") ? $request->input("id_tipos__modulos") : $modulos__contratos->id_tipos__modulos;
                $modulos__contratos->Nombre =  $request->has("Nombre") ? $request->input("Nombre") : $modulos__contratos->Nombre;
                //$modulos__contratos->Detalle =  $request->has("Detalle") ? $request->input("Detalle") : $modulos__contratos->Detalle;
                $modulos__contratos->Rama =  $request->has("Rama") ? $request->input("Rama") : $modulos__contratos->Rama;
                $modulos__contratos->save();

                $permiso = new Permisos();
                $permiso->Nombre = "Perm_".$modulos__contratos->Nombre."_".$modulos__contratos->getKey();
                $permiso->Type = 1;
                $permiso->save();

                $imp = new Inter_Mod_Perm();
                $imp->id_modulos__contratos = $modulos__contratos->getKey();
                $imp->id_permisos = $permiso->id;
                $imp->save();

                $user = Auth::user();
                //$user = 1;

                $iup = new Inter_User_Perm();
                $iup->id_user = $user->id;
                //$iup->id_user = $user;
                $iup->id_permisos = $permiso->getKey();
                $iup->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $modulos__contratos = Modulos_Contrato::where([['id', $request->input("id")]])->first();
                if ($modulos__contratos != null) {
                    $modulos__contratos->id_tipos__modulos = $request->has("id_tipos__modulos") ? $request->input("id_tipos__modulos") : $modulos__contratos->id_tipos__modulos;
                    $modulos__contratos->Nombre =  $request->has("Nombre") ? $request->input("Nombre") : $modulos__contratos->Nombre;
                    //$modulos__contratos->Detalle =  $request->has("Detalle") ? $request->input("Detalle") : $modulos__contratos->Detalle;
                    $modulos__contratos->Rama =  $request->has("Rama") ? $request->input("Rama") : $modulos__contratos->Rama;
                    $modulos__contratos->update();
                }
            }

            //Si incluye "Detalle"
            if ($request->has("Detalle")) {
                $isNew = false;
                $detalle_mod_contrato = Detalle_Mod_Contrato::where('id_modulos__contratos', $modulos__contratos->id)->first();

                if ($detalle_mod_contrato == null) {
                    $detalle_mod_contrato = new Detalle_Mod_Contrato();
                    $isNew = true;
                }

                $detalle_mod_contrato->Detalle = $request->has("Detalle") ? $request->input("Detalle") : $modulos__contratos->Detalle;
                $detalle_mod_contrato->id_modulos__contratos = $modulos__contratos->id;
                if ($isNew) {
                    $detalle_mod_contrato->save();
                } else {
                    $detalle_mod_contrato->update();
                }
                $modulos__contratos->Detalle = $detalle_mod_contrato->Detalle;
            }

            if ($request->isMethod('delete')) {
                $modulos__contratos = Modulos_Contrato::where([['id', $request->input("id")]])->first();
                if ($modulos__contratos == null) {
                    return false;
                }

                $detalle_mod_contrato = Detalle_Mod_Contrato::where('id_modulos__contratos', $modulos__contratos->id)->first();
                if ($detalle_mod_contrato != null) {
                    $detalle_mod_contrato->delete();
                }

                $modulos__contratos = $modulos__contratos->delete();
            }

            return $modulos__contratos;
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
    public function Obtener_venta__detalles(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            $venta__detalles = null;

            if ($request->has("id") && $request->filled("id")) {
                $venta__detalles = Venta_Detalle::where([['id', $request->input("id")]])->first();
            } else if ($request->has("id_Producto") && $request->filled("id_Producto")) {
                $venta__detalles = Venta_Detalle::where([['id_Producto', $request->input("id_Producto")]])->get();
            } else if ($request->has("id_Venta") && $request->filled("id_Venta")) {
                $venta__detalles = Venta_Detalle::where([['id_Venta', $request->input("id_Venta")]])->get();
            } else if ($use_for == 0) {
                $venta__detalles = DB::select('select * from DocuSwift.venta__detalles LIMIT ' . $offset . ',' . $ammount);
            }
            //Si es obtener a partir de la id de un contrato
            /*else if($use_for == 1)
            {
                if($request->has("id_ventas") && $request->filled("id_ventas"))
                {
                    $venta__detalles = DB::table('venta__detalles')->where([
                        'venta__detalles.id_Venta' => $request->input("id_ventas"),
                    ])->get();
                }
            }*/ else {
                $venta__detalles = Venta_Detalle::get();
            }

            return $venta__detalles;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el venta__detalle, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_venta__detalles(Request $request)
    {
        try {
            $venta__detalles = null;
            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                $venta__detalles = new Venta_Detalle();
                $venta__detalles->id_Producto = $request->has("id_Producto") ? $request->input("id_Producto") : $venta__detalles->id_Producto;
                $venta__detalles->id_Venta =  $request->has("id_Venta") ? $request->input("id_Venta") : $venta__detalles->id_Venta;
                $venta__detalles->Subtotal =  $request->has("Subtotal") ? $request->input("Subtotal") : $venta__detalles->Subtotal;
                $venta__detalles->Valor_Individual =  $request->has("Valor_Individual") ? $request->input("Valor_Individual") : $venta__detalles->Valor_Individual;
                $venta__detalles->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $venta__detalles = Venta_Detalle::where([['id', $request->input("id")]])->first();
                if ($venta__detalles != null) {
                    $venta__detalles->id_Producto = $request->has("id_Producto") ? $request->input("id_Producto") : $venta__detalles->id_Producto;
                    $venta__detalles->id_Venta =  $request->has("id_Venta") ? $request->input("id_Venta") : $venta__detalles->id_Venta;
                    $venta__detalles->Subtotal =  $request->has("Subtotal") ? $request->input("Subtotal") : $venta__detalles->Subtotal;
                    $venta__detalles->Valor_Individual =  $request->has("Valor_Individual") ? $request->input("Valor_Individual") : $venta__detalles->Valor_Individual;
                    $venta__detalles->update();
                }
            }

            if ($request->isMethod('delete')) {
                $venta__detalles = Venta_Detalle::where([['id', $request->input("id")]])->first();
                if ($venta__detalles == null) {
                    return false;
                }
                $venta__detalles = $venta__detalles->delete();
            }

            return $venta__detalles;
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
    public function Obtener_contrato(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            $Contrato = null;

            if ($request->has("id") && $request->filled("id")) {
                $Contrato = Contrato::where([['id', $request->input("id")]])->first();
            } else if ($request->has("Validator") && $request->filled("Validator")) {
                if ($use_for == 0) {
                    $Contrato = Contrato::where([['Validator', $request->input("Validator")]])->exists();
                } else {
                    $Contrato = Contrato::where([['Validator', $request->input("Validator")]])->first();
                }
            } else if ($request->has("Nombre") && $request->filled("Nombre")) {
                $Contrato = Contrato::where('Nombre', 'like', '%' . $request->input("Nombre") . '%')->get();
            } else if ($use_for == 'A') {
                $Contrato = Contrato::get();
            } else if ($use_for == 'B') {
                $Contrato = DB::select('select * from DocuSwift.contratos LIMIT ' . $offset . ',' . $ammount);
            } else if ($use_for == 'C') {
                $Contrato = DB::select('select * from DocuSwift.contratos Where Estado = 1 LIMIT ' . $offset . ',' . $ammount);
            } else if(is_numeric($use_for)) {
                $Contrato = DB::select('SELECT DISTINCT DocuSwift.contratos.* FROM DocuSwift.contratos JOIN DocuSwift.inter_perm_contratos ON DocuSwift.contratos.id = DocuSwift.inter_perm_contratos.id_contratos JOIN DocuSwift.inter_user_perms ON DocuSwift.inter_perm_contratos.id_permisos = DocuSwift.inter_user_perms.id_permisos WHERE DocuSwift.contratos.Estado = 1 AND DocuSwift.inter_user_perms.id_user = '.$use_for.' LIMIT '. $offset . ',' . $ammount);
            }

            // if ($Contrato != null) {
            //     if (!$Contrato instanceof Contrato) {
            //         $varTemp = "";
            //         foreach ($Contrato as $key => $value) {
            //             $varTemp = DB::table('correos__firmas')->where(
            //                 'correos__firmas.id_contratos',
            //                 $value->id
            //             )->get();
            //             if ($varTemp != null) {
            //                 $Contrato[$key]->FirmasFirmadas = $varTemp->where('Estado', 1)->count();
            //                 $Contrato[$key]->FirmasTotal = $varTemp->count();
            //             }
            //         }
            //     } else {
            //         $varTemp = DB::table('correos__firmas')->where(
            //             'correos__firmas.id_contratos',
            //             $Contrato->id
            //         )->get();
            //         if ($varTemp != null) {
            //             $Contrato->FirmasFirmadas = $varTemp->where('Estado', 1)->count();
            //             $Contrato->FirmasTotal = $varTemp->count();
            //         }
            //     }
            // }

            return $Contrato;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el Contrato, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    public function Obtener_contrato_Arr(array $array, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            $Contrato = null;

            if (array_key_exists('id', $array)) {
                $Contrato = Contrato::where('id', $array['id'])->first();
            } else if (array_key_exists("Validator", $array)) {
                if ($use_for == 0) {
                    $Contrato = Contrato::where([['Validator', $array("Validator")]])->exists();
                } else {
                    $Contrato = Contrato::where([['Validator', $array("Validator")]])->first();
                }
            } else if (array_key_exists('Nombre', $array)) {
                $Contrato = Contrato::where('Nombre', 'like', '%' . $array["Nombre"] . '%')->get();
            } else if ($use_for == 'A') {
                $Contrato = Contrato::get();
            } else if ($use_for == 'B') {
                $Contrato = DB::select('select * from DocuSwift.contratos LIMIT ' . $offset . ',' . $ammount);
            } else if ($use_for == 'C') {
                $Contrato = DB::select('select * from DocuSwift.contratos Where Estado = 1 LIMIT ' . $offset . ',' . $ammount);
            } else if(is_numeric($use_for)) {
                $Contrato = DB::select('SELECT DISTINCT DocuSwift.contratos.* FROM DocuSwift.contratos JOIN DocuSwift.inter_perm_contratos ON DocuSwift.contratos.id = inter_perm_contratos.id_contratos JOIN DocuSwift.inter_user_perms ON inter_perm_contratos.id_permisos = inter_user_perms.id_permisos WHERE DocuSwift.contratos.Estado = 1 AND DocuSwift.inter_user_perms.id_user = '.$use_for.' LIMIT '. $offset . ',' . $ammount);
            }

            // if ($Contrato != null) {
            //     if (!$Contrato instanceof Contrato) {
            //         $varTemp = "";
            //         foreach ($Contrato as $key => $value) {
            //             $varTemp = DB::table('correos__firmas')->where(
            //                 'correos__firmas.id_contratos',
            //                 $value->id
            //             )->get();
            //             if ($varTemp != null) {
            //                 $Contrato[$key]->FirmasFirmadas = $varTemp::where('Estado', 1)->count();
            //                 $Contrato[$key]->FirmasTotal = $varTemp->count();
            //             }
            //         }
            //     } else {
            //         $varTemp = DB::table('correos__firmas')->where(
            //             'correos__firmas.id_contratos',
            //             $Contrato->id
            //         )->get();
            //         if ($varTemp != null) {
            //             $Contrato->FirmasFirmadas = $varTemp->where('Estado', 1)->count();
            //             $Contrato->FirmasTotal = $varTemp->count();
            //         }
            //     }
            // }

            return $Contrato;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el Contrato, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_contrato(Request $request, $hard_delete = true)
    {
        try {
            $contrato = null;
            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                $question = Contrato::where([['Validator', $request->input("Validator")]])->count();
                if ($question > 0) {
                    session()->flash('failure', "Validator ya en uso, por favor intente con otro");
                    return;
                }
                $contrato = new Contrato();
                $contrato->Nombre = $request->has("Nombre") ? $request->input("Nombre") : $contrato->Nombre;
                $contrato->Descripcion = $request->has("Descripcion") ? $request->input("Descripcion") : $contrato->Descripcion;
                $contrato->Validator = $request->has("Validator") ? $request->input("Validator") : $contrato->Validator;
                $contrato->Estado = 1;
                $contrato->id_tipos__contratos =  $request->has("id_tipos__contratos") ? $request->input("id_tipos__contratos") : $contrato->id_tipos__contratos;
                $contrato->save();

                if ($request->has('id_doc_tipo')) {
                    $doctipo = Doc_Tipo::where([['id', $request->input("id_doc_tipo")]])->first();

                    $inter_mod_doctipo = Inter_Mod_DocTipo::where([['id_doc__tipos', $request->input("id_doc_tipo")]])->get();
                    foreach ($inter_mod_doctipo as $key => $value) {
                        $inter_mod_contrato = new Inter_Mod_Contrat();
                        $inter_mod_contrato->id_contratos =  $contrato->id;
                        $inter_mod_contrato->JsonModulo = $request->has("JsonModulo") ? $request->input("JsonModulo") : $inter_mod_contrato->JsonModulo;
                        $inter_mod_contrato->id_modulos__contratos =  $request->has("id_modulos__contratos") ? $request->input("id_modulos__contratos") : $inter_mod_contrato->id_modulos__contratos;
                        $inter_mod_contrato->Posicion =  $request->has("Posicion") ? $request->input("Posicion") : $inter_mod_contrato->Posicion;
                        $inter_mod_contrato->save();
                    }
                }

                
                $permiso = new Permisos();
                $permiso->Nombre = "Perm_".$contrato->Nombre."_".$contrato->getKey();
                $permiso->Type = 1;
                $permiso->save();

                $imp = new Inter_Perm_Contratos();
                $imp->id_contratos = $contrato->getKey();
                $imp->id_permisos = $permiso->id;
                $imp->save();

                $user = Auth::user();
                //$user = 1;

                $iup = new Inter_User_Perm();
                $iup->id_user = $user->id;
                //$iup->id_user = $user;
                $iup->id_permisos = $permiso->getKey();
                $iup->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $contrato = Contrato::where([['id', $request->input("id")]])->first();
                if ($request->has('Validator')) {
                    $question = Contrato::where([['Validator', $request->input("Validator")]])->count();
                    if ($question > 1 && $contrato->Validator != $request->input("Validator")) {
                        session()->flash('failure', "Validator ya en uso, por favor intente con otro");
                        return;
                    }
                }
                if ($contrato != null) {
                    $contrato->Nombre = $request->has("Nombre") ? $request->input("Nombre") : $contrato->Nombre;
                    $contrato->Descripcion = $request->has("Descripcion") ? $request->input("Descripcion") : $contrato->Descripcion;
                    $contrato->Validator = $request->has("Validator") ? $request->input("Validator") : $contrato->Validator;
                    $contrato->Estado = $request->has("Estado") ? $request->input("Estado") : $contrato->Estado;
                    $contrato->id_tipos__contratos =  $request->has("id_tipos__contratos") ? $request->input("id_tipos__contratos") : $contrato->id_tipos__contratos;
                    $contrato->update();
                }
            }

            if ($request->isMethod('delete')) {
                $contrato = Contrato::where([['id', $request->input("id")]])->first();

                if ($contrato == null) {
                    return false;
                }

                if ($hard_delete) {
                    $Inter_Mod_Contrat = Inter_Mod_Contrat::where('id_contratos', $contrato->id);
                    if ($Inter_Mod_Contrat != null) {
                        $Inter_Mod_Contrat->delete();
                    }

                    $Inter_Perm_Contratos = Inter_Perm_Contratos::where('id_contratos', $contrato->id);
                    if ($Inter_Perm_Contratos != null) {
                        $Inter_Perm_Contratos->delete();
                    }

                    return $contrato = $contrato->delete();
                }
                //Si es soft delete
                if ($contrato->Estado != 0) {
                    $contrato->Estado = 0;
                    $contrato->update();
                }
                return ($contrato->Estado == 0 ? true : false);
            }

            return $contrato;
        } catch (Exception  $e) {
            DB::rollback();
            session()->flash('failure', "Error al procesar la solicitud: por favor intente de nuevo");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este copia los contratos, retorna la id del contrato clon, si retorna 0 es que la clonación no se llevo a cabo
    public function Copiar_Contrato($id_contrato)
    {
        try {
            //dd($request);
            $contrato = null;
            $idNuevoContrato = 0;

            //Crear contrato nuevo
            $contrato = Contrato::where([['id', $id_contrato]])->first();
            if ($contrato != null) {
                $contratoNuevo = new Contrato();
                $contratoNuevo->Nombre = $contrato->Nombre;
                $contratoNuevo->Descripcion = $contrato->Descripcion;
                $contratoNuevo->Validator = $contrato->Validator;
                $contratoNuevo->id_tipos__contratos = $contrato->id_tipos__contratos;
                $contratoNuevo->Estado = 1;
                $contratoNuevo->save();
                $idNuevoContrato = $contratoNuevo->id;
            }

            return $idNuevoContrato;
        } catch (Exception $e) {
            session()->flash('failure', "Error al procesar la solicitud: por favor intente de nuevo");
            //print_r("Error ---: " . $e->getMessage());
            return 0;
        }
    }











    //De necesitar otro filtro, favor avisar
    //-Jose-
    //Este obtiene un dato, varios o todos
    public function Obtener_doc_tipo(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            $Contrato = null;
            if ($request->has("id") && $request->filled("id")) {
                $Contrato = Doc_Tipo::where([['id', $request->input("id")]])->first();
            } else if ($request->has("Nombre_Tipo") && $request->filled("Nombre_Tipo")) {
                $Contrato = Doc_Tipo::where('Nombre_Tipo', 'like', '%' . $request->input("Nombre_Tipo") . '%')->get();
            } else if ($use_for == 'A') {
                $Contrato = Doc_Tipo::get();
            } else if ($use_for == 'B'){
                $Contrato = DB::select('select * from DocuSwift.doc__tipos LIMIT ' . $offset . ',' . $ammount);
            } else if(is_numeric($use_for)) {
                $Contrato = DB::select('SELECT DISTINCT DocuSwift.doc__tipos.* FROM DocuSwift.doc__tipos JOIN DocuSwift.inter_perm_doc_tipos ON DocuSwift.doc__tipos.id = DocuSwift.inter_perm_doc_tipos.id_doc__tipos JOIN DocuSwift.inter_user_perms ON DocuSwift.inter_perm_doc_tipos.id_permisos = DocuSwift.inter_user_perms.id_permisos WHERE DocuSwift.inter_user_perms.id_user = '.$use_for.' LIMIT ' . $offset . ',' . $ammount);
            }
            
            return $Contrato;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el Doc_Tipo, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_doc_tipo(Request $request)
    {
        try {

            //dd($request);
            $contrato = null;
            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                $contrato = new Doc_Tipo();
                $contrato->Nombre_Tipo = $request->has("Nombre_Tipo") ? $request->input("Nombre_Tipo") : $contrato->Nombre;
                $contrato->id_tipos__contratos =  $request->has("id_tipos__contratos") ? $request->input("id_tipos__contratos") : $contrato->id_tipos__contratos;
                $contrato->save();

                
                $permiso = new Permisos();
                $permiso->Nombre = "Perm_".$contrato->Nombre_Tipo."_".$contrato->getKey();
                $permiso->Type = 1;
                $permiso->save();

                $imp = new Inter_Perm_Doc_Tipos();
                $imp->id_doc__tipos = $contrato->getKey();
                $imp->id_permisos = $permiso->id;
                $imp->save();

                $user = Auth::user();
                //$user = 1;

                $iup = new Inter_User_Perm();
                $iup->id_user = $user->id;
                //$iup->id_user = $user;
                $iup->id_permisos = $permiso->getKey();
                $iup->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $contrato = Doc_Tipo::where([['id', $request->input("id")]])->first();
                if ($contrato != null) {
                    $contrato->Nombre_Tipo = $request->has("Nombre_Tipo") ? $request->input("Nombre_Tipo") : $contrato->Nombre;
                    $contrato->id_tipos__contratos =  $request->has("id_tipos__contratos") ? $request->input("id_tipos__contratos") : $contrato->id_tipos__contratos;
                    $contrato->update();
                }
            }

            if ($request->isMethod('delete')) {
                $contrato = Doc_Tipo::where([['id', $request->input("id")]])->first();
                if ($contrato == null) {
                    return false;
                }

                $Inter_Mod_DocTipo = Inter_Mod_DocTipo::where('id_doc__tipos', $contrato->id);
                if ($Inter_Mod_DocTipo != null) {
                    $Inter_Mod_DocTipo->delete();
                }

                $Inter_Perm_Doc_Tipos = Inter_Perm_Doc_Tipos::where('id_permisos', $contrato->id);
                if ($Inter_Perm_Doc_Tipos != null) {
                    $Inter_Perm_Doc_Tipos->delete();
                }

                return $contrato = $contrato->delete();
            }

            return $contrato;
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
    public function Obtener_inter__mod__contrats(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            $inter_mod_contracs = null;

            if ($request->has("id") && $request->filled("id")) {
                $inter_mod_contracs = Inter_Mod_Contrat::where([['id', $request->input("id")]])->first();
            } else if ($request->has("id_contrato") && $request->filled("id_contrato")) {
                //$inter_mod_contracs = Inter_Mod_Contrat::where([['id_contratos', $request->input("id_contrato")]])->orderBy('Posicion')->get();
                //dd($ammount);
                $inter_mod_contracs = DB::select('select * from DocuSwift.inter__mod__contrats WHERE id_contratos = '.$request->input("id_contrato").' ORDER BY Posicion LIMIT ' . $offset . ',' . $ammount);
            } else if ($use_for == 1) {
                $inter_mod_contracs = Inter_Mod_Contrat::orderBy('Posicion')->get();
            } else {
                $inter_mod_contracs = DB::select('select * from DocuSwift.inter__mod__contrats ORDER BY Posicion LIMIT ' . $offset . ',' . $ammount);
            }

            if ($inter_mod_contracs != null) {
                if (!$inter_mod_contracs instanceof Inter_Mod_Contrat) {
                    $varTemp = "";
                    foreach ($inter_mod_contracs as $key => $value) {
                        $varTemp = Detalle_Mod_Contrato::where('id_modulos__contratos', $value->id_modulos__contratos)->first();
                        if ($varTemp != null) {
                            $inter_mod_contracs[$key]->Detalle = $varTemp->Detalle;
                        }
                    }
                } else {
                    $varTemp = Detalle_Mod_Contrato::where('id_modulos__contratos', $inter_mod_contracs->id_modulos__contratos)->first();
                    if ($varTemp != null) {
                        $inter_mod_contracs->Detalle = $varTemp->Detalle;
                    }
                }
            }

            return $inter_mod_contracs;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el Inter_Mod_Contrat, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_inter__mod__contrats(Request $request)
    {
        try {
            $inter_mod_contracts = null;
            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                //To avoid create it twice
                $ifAlreadyExist = Inter_Mod_Contrat::where([['id_contratos', $request->input("id_contratos")], ['id_modulos__contratos', $request->input("id_modulos__contratos")]])->get();
                if ($ifAlreadyExist->count() > 0) {
                    session()->flash('failure', "Dato intermedio ya existe, ignorando la solicitud");
                    return "{}";
                }

                $inter_mod_contracts = new Inter_Mod_Contrat();
                $inter_mod_contracts->id_contratos =  $request->has("id_contratos") ? $request->input("id_contratos") : $inter_mod_contracts->id_contratos;
                $inter_mod_contracts->JsonModulo = $request->has("JsonModulo") ? $request->input("JsonModulo") : $inter_mod_contracts->JsonModulo;
                $inter_mod_contracts->id_modulos__contratos =  $request->has("id_modulos__contratos") ? $request->input("id_modulos__contratos") : $inter_mod_contracts->id_modulos__contratos;
                $inter_mod_contracts->Posicion = $request->has("Posicion") ? $request->input("Posicion") : $inter_mod_contracts->Posicion;
                $inter_mod_contracts->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $inter_mod_contracts = Inter_Mod_Contrat::where([['id', $request->input("id")]])->first();
                if ($inter_mod_contracts != null) {
                    $inter_mod_contracts->id_contratos =  $request->has("id_contratos") ? $request->input("id_contratos") : $inter_mod_contracts->id_contratos;
                    $inter_mod_contracts->JsonModulo = $request->has("JsonModulo") ? $request->input("JsonModulo") : $inter_mod_contracts->JsonModulo;
                    $inter_mod_contracts->id_modulos__contratos =  $request->has("id_modulos__contratos") ? $request->input("id_modulos__contratos") : $inter_mod_contracts->id_modulos__contratos;
                    $inter_mod_contracts->Posicion = $request->has("Posicion") ? $request->input("Posicion") : $inter_mod_contracts->Posicion;
                    $inter_mod_contracts->update();
                }
            }

            if ($request->isMethod('delete')) {
                $inter_mod_contracts = Inter_Mod_Contrat::where([['id', $request->input("id")]])->first();
                if ($inter_mod_contracts == null) {
                    return false;
                }
                return $inter_mod_contracts = $inter_mod_contracts->delete();
            }

            return $inter_mod_contracts;
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
    public function Obtener_inter__mod__doc_tipos(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            $inter_mod_doctipo = null;

            if ($request->has("id") && $request->filled("id")) {
                $inter_mod_doctipo = Inter_Mod_DocTipo::where([['id', $request->input("id")]])->first();
            } else if ($request->has("id_doc_tipo") && $request->filled("id_doc_tipo")) {
                //$inter_mod_doctipo = Inter_Mod_DocTipo::where([['id_doc__tipos', $request->input("id_doc_tipo")]])->get();
                $inter_mod_doctipo = DB::select('select * from DocuSwift.inter__mod__doc_tipos WHERE id_doc__tipos = '.$request->input("id_doc_tipo").' LIMIT ' . $offset . ',' . $ammount);
            } else if ($use_for == 1) {
                $inter_mod_doctipo = Inter_Mod_DocTipo::get();
            } else {
                $inter_mod_doctipo = DB::select('select * from DocuSwift.inter__mod__doc_tipos LIMIT ' . $offset . ',' . $ammount);
            }


            return $inter_mod_doctipo;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el Inter_Mod_DocTipo, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_inter__mod__doc_tipos(Request $request)
    {
        try {

            //dd($request);
            $inter_mod_doc_tipo = null;
            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                //To avoid create it twice
                $ifAlreadyExist = Inter_Mod_DocTipo::where([['id_doc__tipos', $request->input("id_doc__tipos")], ['id_modulos__contratos', $request->input("id_modulos__contratos")]])->get();
                if ($ifAlreadyExist->count() > 0) {
                    session()->flash('failure', "Dato intermedio ya existe, ignorando la solicitud");
                    return "{}";
                }

                $inter_mod_doc_tipo = new Inter_Mod_DocTipo();
                $inter_mod_doc_tipo->id_doc__tipos =  $request->has("id_doc__tipos") ? $request->input("id_doc__tipos") : $inter_mod_doc_tipo->id_doc__tipos;
                $inter_mod_doc_tipo->id_modulos__contratos =  $request->has("id_modulos__contratos") ? $request->input("id_modulos__contratos") : $inter_mod_doc_tipo->id_modulos__contratos;
                $inter_mod_doc_tipo->Posicion = $request->has("Posicion") ? $request->input("Posicion") : $inter_mod_doc_tipo->Posicion;
                $inter_mod_doc_tipo->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $inter_mod_doc_tipo = Inter_Mod_DocTipo::where([['id', $request->input("id")]])->first();
                if ($inter_mod_doc_tipo != null) {
                    $inter_mod_doc_tipo->id_doc__tipos =  $request->has("id_doc__tipos") ? $request->input("id_doc__tipos") : $inter_mod_doc_tipo->id_doc__tipos;
                    $inter_mod_doc_tipo->id_modulos__contratos =  $request->has("id_modulos__contratos") ? $request->input("id_modulos__contratos") : $inter_mod_doc_tipo->id_modulos__contratos;
                    $inter_mod_doc_tipo->Posicion = $request->has("Posicion") ? $request->input("Posicion") : $inter_mod_doc_tipo->Posicion;
                    $inter_mod_doc_tipo->update();
                }
            }

            if ($request->isMethod('delete')) {
                $inter_mod_doc_tipo = Inter_Mod_DocTipo::where([['id', $request->input("id")]])->first();
                if ($inter_mod_doc_tipo == null) {
                    return false;
                }
                return $inter_mod_doc_tipo = $inter_mod_doc_tipo->delete();
            }

            return $inter_mod_doc_tipo;
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
    //************** Dario: desde aca las pruebas en api.*************
    //revisado ok *******Dario
    public function Obtener_inter_user_perms(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            $inter_mod_doctipo = null;

            if ($request->has("id") && $request->filled("id")) {
                $inter_mod_doctipo = Inter_User_Perm::where([['id', $request->input("id")]])->first();
            } else if ($request->has("id_user") && $request->filled("id_user")) {
                $inter_mod_doctipo = Inter_User_Perm::where([['id_user', $request->input("id_user")]])->get();
            } else if ($request->has("id_permisos") && $request->filled("id_permisos")) {
                $inter_mod_doctipo = Inter_User_Perm::where([['id_permisos', $request->input("id_permisos")]])->get();
            } else if ($use_for == 1) {
                $inter_mod_doctipo = Inter_User_Perm::get();
            } else {
                $inter_mod_doctipo = DB::select('select * from DocuSwift.inter_user_perms LIMIT ' . $offset . ',' . $ammount);
            }

            return $inter_mod_doctipo;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el inter_user_perms, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    //no funciona el post, no probe el put ni delete**********Dario
    public function Editar_inter_user_perms(Request $request)
    {
        try {
            //dd($request);
            $Inter_User_Perm = null;
            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                //To avoid create it twice
                $ifAlreadyExist = Inter_User_Perm::where([['id_permisos', $request->input("id_permisos")], ['id_user', $request->input("id_user")]])->get();
                if ($ifAlreadyExist->count() > 0) {
                    session()->flash('failure', "Dato intermedio ya existe, ignorando la solicitud");
                    return "{}";
                }

                $Inter_User_Perm = new Inter_User_Perm();
                $Inter_User_Perm->id_user =  $request->has("id_user") ? $request->input("id_user") : $Inter_User_Perm->id_user;
                $Inter_User_Perm->id_permisos =  $request->has("id_permisos") ? $request->input("id_permisos") : $Inter_User_Perm->id_permisos;
                $Inter_User_Perm->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $Inter_User_Perm = Inter_User_Perm::where([['id', $request->input("id")]])->first();
                if ($Inter_User_Perm != null) {
                    $Inter_User_Perm->id_user =  $request->has("id_user") ? $request->input("id_user") : $Inter_User_Perm->id_user;
                    $Inter_User_Perm->id_permisos =  $request->has("id_permisos") ? $request->input("id_permisos") : $Inter_User_Perm->id_permisos;
                    $Inter_User_Perm->update();
                }
            }

            if ($request->isMethod('delete')) {
                $Inter_User_Perm = Inter_User_Perm::where([['id', $request->input("id")]])->first();
                if ($Inter_User_Perm == null) {
                    return false;
                }
                return $Inter_User_Perm = $Inter_User_Perm->delete();
            }

            return $Inter_User_Perm;
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
    public function Obtener_inter_user_perfiles(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            $inter_mod_doctipo = null;

            if ($request->has("id") && $request->filled("id")) {
                $inter_mod_doctipo = Inter_User_Perf::where([['id', $request->input("id")]])->first();
            } else if ($request->has("id_user") && $request->filled("id_user")) {
                $inter_mod_doctipo = Inter_User_Perf::where([['id_user', $request->input("id_user")]])->get();
            } else if ($request->has("id_permisos") && $request->filled("id_permisos")) {
                $inter_mod_doctipo = Inter_User_Perf::where([['id_permisos', $request->input("id_permisos")]])->get();
            } else if ($use_for == 1) {
                $inter_mod_doctipo = Inter_User_Perf::get();
            } else {
                $inter_mod_doctipo = DB::select('select * from DocuSwift.inter_user_perfiles LIMIT ' . $offset . ',' . $ammount);
            }

            return $inter_mod_doctipo;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el inter_user_perfiles, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_inter_user_perfiles(Request $request)
    {
        
        try {
            //dd($request);
            $Inter_User_Perf = null;

            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                //To avoid create it twice
                $ifAlreadyExist = Inter_User_Perf::where([['id_perfiles', $request->input("id_perfiles")], ['id_user', $request->input("id_user")]])->get();
                if ($ifAlreadyExist->count() > 0) {
                    session()->flash('failure', "Dato intermedio ya existe, ignorando la solicitud");
                    return "{}";
                }

                $Inter_User_Perf = new Inter_User_Perf();
                $Inter_User_Perf->id_user =  $request->has("id_user") ? $request->input("id_user") : $Inter_User_Perf->id_user;
                $Inter_User_Perf->id_perfiles =  $request->has("id_perfiles") ? $request->input("id_perfiles") : $Inter_User_Perf->id_perfiles;
                $Inter_User_Perf->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $Inter_User_Perf = Inter_User_Perf::where([['id', $request->input("id")]])->first();
                if ($Inter_User_Perf != null) {
                    $Inter_User_Perf->id_user =  $request->has("id_user") ? $request->input("id_user") : $Inter_User_Perf->id_user;
                    $Inter_User_Perf->id_perfiles =  $request->has("id_perfiles") ? $request->input("id_perfiles") : $Inter_User_Perf->id_perfiles;
                    $Inter_User_Perf->update();
                }
            }

            if ($request->isMethod('delete')) {
                $Inter_User_Perf = Inter_User_Perf::where([['id', $request->input("id")]])->first();
                if ($Inter_User_Perf == null) {
                    return false;
                }
                return $Inter_User_Perf = $Inter_User_Perf->delete();
            }

            return $Inter_User_Perf;
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
    //revisado ok *******Dario
    public function Obtener_inter_perf_perms(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            $Inter_Perf_Perm = null;

            if ($request->has("id") && $request->filled("id")) {
                $Inter_Perf_Perm = Inter_Perf_Perm::where([['id', $request->input("id")]])->first();
            } else if ($request->has("id_perfiles") && $request->filled("id_perfiles")) {
                $Inter_Perf_Perm = Inter_Perf_Perm::where([['id_perfiles', $request->input("id_perfiles")]])->get();
            } else if ($request->has("id_permisos") && $request->filled("id_permisos")) {
                $Inter_Perf_Perm = Inter_Perf_Perm::where([['id_permisos', $request->input("id_permisos")]])->get();
            } else if ($use_for == 1) {
                $Inter_Perf_Perm = Inter_Perf_Perm::get();
            } else {
                $Inter_Perf_Perm = DB::select('select * from DocuSwift.inter_perf_perms LIMIT ' . $offset . ',' . $ammount);
            }

            return $Inter_Perf_Perm;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el inter_perf_perms, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    //no funciona el post, no probe el put ni delete**********Dario
    public function Editar_inter_perf_perms(Request $request)
    {
        try {
            //dd($request);
            $Inter_Perf_Perm = null;
            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                //To avoid create it twice
                $ifAlreadyExist = Inter_Perf_Perm::where([['id_perfiles', $request->input("id_perfiles")], ['id_permisos', $request->input("id_permisos")]])->get();
                if ($ifAlreadyExist->count() > 0) {
                    session()->flash('failure', "Dato intermedio ya existe, ignorando la solicitud");
                    return "{}";
                }

                $Inter_Perf_Perm = new Inter_Perf_Perm();
                $Inter_Perf_Perm->id_perfiles =  $request->has("id_perfiles") ? $request->input("id_perfiles") : $Inter_Perf_Perm->id_perfiles;
                $Inter_Perf_Perm->id_permisos =  $request->has("id_permisos") ? $request->input("id_permisos") : $Inter_Perf_Perm->id_permisos;
                $Inter_Perf_Perm->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $Inter_Perf_Perm = Inter_Perf_Perm::where([['id', $request->input("id")]])->first();
                if ($Inter_Perf_Perm != null) {
                    $Inter_Perf_Perm->id_perfiles =  $request->has("id_perfiles") ? $request->input("id_perfiles") : $Inter_Perf_Perm->id_perfiles;
                    $Inter_Perf_Perm->id_permisos =  $request->has("id_permisos") ? $request->input("id_permisos") : $Inter_Perf_Perm->id_permisos;
                    $Inter_Perf_Perm->update();
                }
            }

            if ($request->isMethod('delete')) {
                $Inter_Perf_Perm = Inter_Perf_Perm::where([['id', $request->input("id")]])->first();
                if ($Inter_Perf_Perm == null) {
                    return false;
                }
                return $Inter_Perf_Perm = $Inter_Perf_Perm->delete();
            }

            return $Inter_Perf_Perm;
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
    public function Obtener_permisos(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            $permisos = null;

            if ($request->has("id") && $request->filled("id")) {
                $permisos = Permisos::where([['id', $request->input("id")]])->first();
            } else if ($request->has("Nombre") && $request->filled("Nombre")) {
                $permisos = Permisos::where('Nombre', 'like', '%' . $request->input("Nombre") . '%')->get();
            } else if ($request->has("id_perfiles") && $request->filled("id_perfiles")) {
                $permisos = DB::select('SELECT DocuSwift.permisos.* FROM DocuSwift.permisos JOIN DocuSwift.inter_perf_perms ON DocuSwift.permisos.id = DocuSwift.inter_perf_perms.id_permisos WHERE DocuSwift.inter_perf_perms.id_perfiles = '.$request->input('id_perfiles'));
            } else if ($use_for == 'A') {
                $permisos = Permisos::get();
            } else if(is_numeric($use_for)) {
                $permisos = DB::select('SELECT DISTINCT DocuSwift.permisos.* FROM DocuSwift.permisos JOIN DocuSwift.inter_user_perms ON DocuSwift.permisos.id = DocuSwift.inter_user_perms.id_permisos JOIN DocuSwift.users ON DocuSwift.inter_user_perms.id_user = DocuSwift.users.id WHERE DocuSwift.users.id = '.$use_for.' LIMIT '. $offset . ',' . $ammount);
            } else if(str_contains($use_for, "_")) {
                $numberToUse =  substr($use_for, stripos($use_for, "_")+1);
                $permisos = DB::select('SELECT DocuSwift.permisos.*, DocuSwift.users.id "id_user" FROM DocuSwift.permisos JOIN DocuSwift.inter_user_perms ON DocuSwift.permisos.id = DocuSwift.inter_user_perms.id_permisos JOIN DocuSwift.users ON DocuSwift.inter_user_perms.id_user = DocuSwift.users.id WHERE DocuSwift.users.id_usuario_upper LIKE (SELECT CONCAT(\'%_\', '.$numberToUse.')) LIMIT '. $offset . ',' . $ammount);
            } else {
                $permisos = DB::select('select * from DocuSwift.permisos LIMIT ' . $offset . ',' . $ammount);
            }

            return $permisos;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el permisos, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    //revisado ok *******Dario
    public function Editar_permisos(Request $request)
    {
        try {
            //dd($request);
            $Permisos = null;

            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                $Permisos = new Permisos();
                $Permisos->Nombre =  $request->has("Nombre") ? $request->input("Nombre") : $Permisos->Nombre;
                $Permisos->Type =  $request->has("Type") ? $request->input("Type") : $Permisos->Type;
                $Permisos->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $Permisos = Permisos::where([['id', $request->input("id")]])->first();
                if ($Permisos != null) {
                    $Permisos->Nombre =  $request->has("Nombre") ? $request->input("Nombre") : $Permisos->Nombre;
                    $Permisos->Type =  $request->has("Type") ? $request->input("Type") : $Permisos->Type;
                    $Permisos->update();
                }
            }

            if ($request->isMethod('delete')) {
                $Permisos = Permisos::where([['id', $request->input("id")]])->first();
                if ($Permisos == null) {
                    return false;
                }

                $Inter_Perm_Doc_Tipos = Inter_Perm_Doc_Tipos::where('id_permisos', $Permisos->id);
                if ($Inter_Perm_Doc_Tipos != null) {
                    $Inter_Perm_Doc_Tipos->delete();
                }

                $Inter_User_Perm = Inter_User_Perm::where('id_permisos', $Permisos->id);
                if ($Inter_User_Perm != null) {
                    $Inter_User_Perm->delete();
                }

                $Inter_Perm_Contratos = Inter_Perm_Contratos::where('id_permisos', $Permisos->id);
                if ($Inter_Perm_Contratos != null) {
                    $Inter_Perm_Contratos->delete();
                }

                $Inter_Perf_Perm = Inter_Perf_Perm::where('id_permisos', $Permisos->id);
                if ($Inter_Perf_Perm != null) {
                    $Inter_Perf_Perm->delete();
                }

                return $Permisos = $Permisos->delete();
            }

            return $Permisos;
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
    //revisado ok
    public function Obtener_perfiles(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            $perfiles = null;

            if ($request->has("id") && $request->filled("id")) {
                $perfiles = Perfiles::where([['id', $request->input("id")]])->first();
            } else if ($request->has("Nombre") && $request->filled("Nombre")) {
                $perfiles = Perfiles::where('Nombre', 'like', '%' . $request->input("Nombre") . '%')->get();
            } else if ($use_for == 'A') {
                $perfiles = Perfiles::get();
            } else if(is_numeric($use_for)) {
                $perfiles = DB::select('SELECT DISTINCT DocuSwift.perfiles.* FROM DocuSwift.perfiles JOIN DocuSwift.inter_user_perfiles ON DocuSwift.perfiles.id = DocuSwift.inter_user_perfiles.id_perfiles JOIN DocuSwift.users ON DocuSwift.inter_user_perfiles.id_user = DocuSwift.users.id WHERE DocuSwift.users.id = '.$use_for.' LIMIT '. $offset . ',' . $ammount);
            } else if(str_contains($use_for, "_")) {
                $numberToUse =  substr($use_for, stripos($use_for, "_")+1);
                $perfiles = DB::select('SELECT DocuSwift.perfiles.*, DocuSwift.users.id "id_user" FROM DocuSwift.perfiles JOIN DocuSwift.inter_user_perfiles ON DocuSwift.perfiles.id = DocuSwift.inter_user_perfiles.id_perfiles JOIN DocuSwift.users ON DocuSwift.inter_user_perfiles.id_user = DocuSwift.users.id WHERE DocuSwift.users.id_usuario_upper LIKE (SELECT CONCAT(\'%_\', '.$numberToUse.')) LIMIT '. $offset . ',' . $ammount);
            }  else {
                $perfiles = DB::select('select * from DocuSwift.perfiles LIMIT ' . $offset . ',' . $ammount);
            }

            return $perfiles;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el perfiles, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    //revisado ok *******Dario
    public function Editar_perfiles(Request $request, $special_case = 0)
    {
        try {
            //dd($request);
            $Perfiles = null;

            //Special Case, Borrar todos los inter mod permisos vinculados al perfil correspondiente y reestablece los permisos vinculados por los perfiles al usuario
            if($special_case > 0)
            {
                $userid = Auth::user()->id;

                //Eliminar todos los permisos vinculados al usuario al que le fue removido dicho especifico perfil
                // $interPermUserLinkedToPerfilABorrar = DB::table('DocuSwift.inter_user_perms')
                // ->where('DocuSwift.inter_user_perms.id_user', '=', $userid)
                // ->whereIn('id_permisos', DB::table('DocuSwift.inter_perf_perms')
                //     ->where('DocuSwift.inter_perf_perms.id_perfiles', '=', $special_case)
                //     ->select('id_permisos')
                //     ->get());
                
                // $interPermUserLinkedToPerfilABorrar->delete();

                // //Removemos el perfil del usuario
                // $Inter_User_Perf = Inter_User_Perf::where([['id_perfiles', $special_case],['id_user', $userid]]);
                // $Inter_User_Perf->delete();

                //Reinstauramos cualquier permiso que pudiera haberse perdido de los perfiles que aún persisten

                /*->where('DocuSwift.inter_user_perms.id_user', '=', Auth::user()->id)*/
                /*SELECT id_permisos 
                FROM DocuSwift.inter_perf_perms 
                WHERE id_perfiles IN 
                (SELECT id_perfiles FROM DocuSwift.inter_user_perfiles iup where id_user = 1)
                AND id_permisos NOT IN 
                (SELECT id_permisos FROM DocuSwift.inter_user_perms iup where id_user = 1);*/

                // $reinstatePermits = DB::table('DocuSwift.inter_perf_perms')
                // ->whereIn('id_perfiles', DB::table('DocuSwift.inter_user_perfiles')
                //     ->where('DocuSwift.inter_user_perfiles.id_user', '=', $userid)
                //     ->select('id_perfiles')
                //     ->get())
                // ->whereNotIn('id_permisos', DB::table('DocuSwift.inter_user_perms')
                //     ->where('DocuSwift.inter_user_perms.id_user', '=', $userid)
                //     ->select('id_permisos')
                //     ->get())
                // ->select('id_permisos');

                // $Inter_User_Perm = null;
                // foreach ($reinstatePermits as $key => $value) {
                //     $Inter_User_Perm = new Inter_User_Perm();
                //     $Inter_User_Perm->id_user = $userid;
                //     $Inter_User_Perm->id_permisos = $value;
                //     $Inter_User_Perm->save();
                // }

                DB::select('CALL DocuSwift.Anadir_Permisos_Segun_Perfil_A_User('.$special_case.','.$userid.');');
                return 1;
            }

            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                $Perfiles = new Perfiles();
                $Perfiles->Nombre =  $request->has("Nombre") ? $request->input("Nombre") : $Perfiles->Nombre;
                $Perfiles->Type =  $request->has("Type") ? $request->input("Type") : $Perfiles->Type;
                $Perfiles->save();

                $user = Auth::user();
                //$user = 1;
                
                $Inter_User_Perf = new Inter_User_Perf();
                $Inter_User_Perf->id_user =  $user->id;
                $Inter_User_Perf->id_perfiles =  $Perfiles->getKey();
                $Inter_User_Perf->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $Perfiles = Perfiles::where([['id', $request->input("id")]])->first();
                if ($Perfiles != null) {
                    $Perfiles->Nombre =  $request->has("Nombre") ? $request->input("Nombre") : $Perfiles->Nombre;
                    $Perfiles->Type =  $request->has("Type") ? $request->input("Type") : $Perfiles->Type;
                    $Perfiles->update();
                }
            }

            if ($request->isMethod('delete')) {
                $Perfiles = Perfiles::where([['id', $request->input("id")]])->first();
                if ($Perfiles == null) {
                    return false;
                }

                $Inter_Perf_Perm = Inter_Perf_Perm::where('id_perfiles', $Perfiles->id);
                if ($Inter_Perf_Perm != null) {
                    $Inter_Perf_Perm->delete();
                }

                $Inter_User_Perf = Inter_User_Perf::where('id_perfiles', $Perfiles->id);
                if ($Inter_User_Perf != null) {
                    $Inter_User_Perf->delete();
                }

                return $Perfiles = $Perfiles->delete();
            }

            return $Perfiles;
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
    public function Obtener_inter_perm_contratos(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            $inter_perm_contratos = null;

            if ($request->has("id") && $request->filled("id")) {
                $inter_perm_contratos = Inter_Perm_Contratos::where([['id', $request->input("id")]])->first();
            } else if ($request->has("id_permisos") && $request->filled("id_permisos")) {
                $inter_perm_contratos = Inter_Perm_Contratos::where([['id_permisos', $request->input("id_permisos")]])->get();
            } else if ($request->has("id_contratos") && $request->filled("id_contratos")) {
                $inter_perm_contratos = Inter_Perm_Contratos::where([['id_contratos', $request->input("id_contratos")]])->get();
            } else if ($use_for == 1) {
                $inter_perm_contratos = Inter_Perm_Contratos::get();
            } else {
                $inter_perm_contratos = DB::select('select * from DocuSwift.inter_perm_contratos LIMIT ' . $offset . ',' . $ammount);
            }

            return $inter_perm_contratos;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el inter_perm_contratos, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_inter_perm_contratos(Request $request)
    {
        try {
            //dd($request);
            $Inter_Perm_Contratos = null;
            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                //To avoid create it twice
                $ifAlreadyExist = Inter_Perm_Contratos::where([['id_contratos', $request->input("id_contratos")], ['id_permisos', $request->input("id_permisos")]])->get();
                if ($ifAlreadyExist->count() > 0) {
                    session()->flash('failure', "Dato intermedio ya existe, ignorando la solicitud");
                    return "{}";
                }

                $Inter_Perm_Contratos = new Inter_Perm_Contratos();
                $Inter_Perm_Contratos->id_permisos =  $request->has("id_permisos") ? $request->input("id_permisos") : $Inter_Perm_Contratos->id_permisos;
                $Inter_Perm_Contratos->id_contratos =  $request->has("id_contratos") ? $request->input("id_contratos") : $Inter_Perm_Contratos->id_contratos;
                $Inter_Perm_Contratos->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $Inter_Perm_Contratos = Inter_Perm_Contratos::where([['id', $request->input("id")]])->first();
                if ($Inter_Perm_Contratos != null) {
                    $Inter_Perm_Contratos->id_permisos =  $request->has("id_permisos") ? $request->input("id_permisos") : $Inter_Perm_Contratos->id_permisos;
                    $Inter_Perm_Contratos->id_contratos =  $request->has("id_contratos") ? $request->input("id_contratos") : $Inter_Perm_Contratos->id_contratos;
                    $Inter_Perm_Contratos->update();
                }
            }

            if ($request->isMethod('delete')) {
                $Inter_Perm_Contratos = Inter_Perm_Contratos::where([['id', $request->input("id")]])->first();
                if ($Inter_Perm_Contratos == null) {
                    return false;
                }

                return $Inter_Perm_Contratos = $Inter_Perm_Contratos->delete();
            }

            return $Inter_Perm_Contratos;
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
    public function Obtener_inter_perm_doc_tipos(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            $Inter_Perm_Doc_Tipos = null;

            if ($request->has("id") && $request->filled("id")) {
                $Inter_Perm_Doc_Tipos = Inter_Perm_Doc_Tipos::where([['id', $request->input("id")]])->first();
            } else if ($request->has("id_permisos") && $request->filled("id_permisos")) {
                $Inter_Perm_Doc_Tipos = Inter_Perm_Doc_Tipos::where([['id_permisos', $request->input("id_permisos")]])->get();
            } else if ($request->has("id_doc__tipos") && $request->filled("id_doc__tipos")) {
                $Inter_Perm_Doc_Tipos = Inter_Perm_Doc_Tipos::where([['id_doc__tipos', $request->input("id_doc__tipos")]])->get();
            } else if ($use_for == 1) {
                $Inter_Perm_Doc_Tipos = Inter_Perm_Doc_Tipos::get();
            } else {
                $Inter_Perm_Doc_Tipos = DB::select('select * from DocuSwift.inter_perm_doc_tipos LIMIT ' . $offset . ',' . $ammount);
            }

            return $Inter_Perm_Doc_Tipos;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el inter_perm_doc_tipos, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_inter_perm_doc_tipos(Request $request)
    {
        try {
            //dd($request);
            $Inter_Perm_Doc_Tipos = null;
            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                //To avoid create it twice
                $ifAlreadyExist = Inter_Perm_Doc_Tipos::where([['id_doc__tipos', $request->input("id_doc__tipos")], ['id_permisos', $request->input("id_permisos")]])->get();
                if ($ifAlreadyExist->count() > 0) {
                    session()->flash('failure', "Dato intermedio ya existe, ignorando la solicitud");
                    return "{}";
                }

                $Inter_Perm_Doc_Tipos = new Inter_Perm_Doc_Tipos();
                $Inter_Perm_Doc_Tipos->id_permisos =  $request->has("id_permisos") ? $request->input("id_permisos") : $Inter_Perm_Doc_Tipos->id_permisos;
                $Inter_Perm_Doc_Tipos->id_doc__tipos =  $request->has("id_doc__tipos") ? $request->input("id_doc__tipos") : $Inter_Perm_Doc_Tipos->id_doc__tipos;
                $Inter_Perm_Doc_Tipos->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $Inter_Perm_Doc_Tipos = Inter_Perm_Doc_Tipos::where([['id', $request->input("id")]])->first();
                if ($Inter_Perm_Doc_Tipos != null) {
                    $Inter_Perm_Doc_Tipos->id_permisos =  $request->has("id_permisos") ? $request->input("id_permisos") : $Inter_Perm_Doc_Tipos->id_permisos;
                    $Inter_Perm_Doc_Tipos->id_doc__tipos =  $request->has("id_doc__tipos") ? $request->input("id_doc__tipos") : $Inter_Perm_Doc_Tipos->id_doc__tipos;
                    $Inter_Perm_Doc_Tipos->update();
                }
            }

            if ($request->isMethod('delete')) {
                $Inter_Perm_Doc_Tipos = Inter_Perm_Doc_Tipos::where([['id', $request->input("id")]])->first();
                if ($Inter_Perm_Doc_Tipos == null) {
                    return false;
                }
                return $Inter_Perm_Doc_Tipos = $Inter_Perm_Doc_Tipos->delete();
            }

            return $Inter_Perm_Doc_Tipos;
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
    public function Obtener_inter_mod_perms(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            $Inter_Mod_Perm = null;

            if ($request->has("id") && $request->filled("id")) {
                $Inter_Mod_Perm = Inter_Mod_Perm::where([['id', $request->input("id")]])->first();
            } else if ($request->has("id_permisos") && $request->filled("id_permisos")) {
                $Inter_Mod_Perm = Inter_Mod_Perm::where([['id_permisos', $request->input("id_permisos")]])->get();
            } else if ($request->has("id_modulos__contratos") && $request->filled("id_modulos__contratos")) {
                $Inter_Mod_Perm = Inter_Mod_Perm::where([['id_modulos__contratos', $request->input("id_modulos__contratos")]])->get();
            } else if ($use_for == 1) {
                $Inter_Mod_Perm = Inter_Mod_Perm::get();
            } else {
                $Inter_Mod_Perm = DB::select('select * from DocuSwift.inter_mod_perms LIMIT ' . $offset . ',' . $ammount);
            }

            return $Inter_Mod_Perm;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el inter_mod_perms, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_inter_mod_perms(Request $request)
    {
        try {
            //dd($request);
            $Inter_Mod_Perm = null;
            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                //To avoid create it twice
                $ifAlreadyExist = Inter_Mod_Perm::where([['id_modulos__contratos', $request->input("id_modulos__contratos")], ['id_permisos', $request->input("id_permisos")]])->get();
                if ($ifAlreadyExist->count() > 0) {
                    session()->flash('failure', "Dato intermedio ya existe, ignorando la solicitud");
                    return "{}";
                }

                $Inter_Mod_Perm = new Inter_Mod_Perm();
                $Inter_Mod_Perm->id_permisos =  $request->has("id_permisos") ? $request->input("id_permisos") : $Inter_Mod_Perm->id_permisos;
                $Inter_Mod_Perm->id_modulos__contratos =  $request->has("id_modulos__contratos") ? $request->input("id_modulos__contratos") : $Inter_Mod_Perm->id_modulos__contratos;
                $Inter_Mod_Perm->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $Inter_Mod_Perm = Inter_Mod_Perm::where([['id', $request->input("id")]])->first();
                if ($Inter_Mod_Perm != null) {
                    $Inter_Mod_Perm->id_permisos =  $request->has("id_permisos") ? $request->input("id_permisos") : $Inter_Mod_Perm->id_permisos;
                    $Inter_Mod_Perm->id_modulos__contratos =  $request->has("id_modulos__contratos") ? $request->input("id_modulos__contratos") : $Inter_Mod_Perm->id_modulos__contratos;
                    $Inter_Mod_Perm->update();
                }
            }

            if ($request->isMethod('delete')) {
                $Inter_Mod_Perm = Inter_Mod_Perm::where([['id', $request->input("id")]])->first();
                if ($Inter_Mod_Perm == null) {
                    return false;
                }
                return $Inter_Mod_Perm = $Inter_Mod_Perm->delete();
            }

            return $Inter_Mod_Perm;
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
    public function Obtener_tipos__contratos(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {
            $Tipos_Contrato = null;

            if ($request->has("id") && $request->filled("id")) {
                $Tipos_Contrato = Tipos_Contrato::where([['id', $request->input("id")]])->first();
            } else if ($request->has("id_permisos") && $request->filled("id_permisos")) {
                $Tipos_Contrato = Tipos_Contrato::where([['id_permisos', $request->input("id_permisos")]])->get();
            } else if ($request->has("Nombre_Tipo") && $request->filled("Nombre_Tipo")) {
                $Tipos_Contrato = Tipos_Contrato::where([['Nombre_Tipo', $request->input("Nombre_Tipo")]])->get();
            } else if ($use_for == 1) {
                $Tipos_Contrato = Tipos_Contrato::get();
            } else {
                $Tipos_Contrato = DB::select('select * from DocuSwift.tipos__contratos LIMIT ' . $offset . ',' . $ammount);
            }

            return $Tipos_Contrato;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el tipos__contratos, por favor intente nuevamente");
            //print_r("Error ---: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_tipos__contratos(Request $request)
    {
        try {
            //dd($request);
            $Tipos_Contrato = null;
            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                //To avoid create it twice
                $ifAlreadyExist = Tipos_Contrato::where([['Nombre_Tipo', $request->input("Nombre_Tipo")], ['id_permisos', $request->input("id_permisos")]])->get();
                if ($ifAlreadyExist->count() > 0) {
                    session()->flash('failure', "Dato intermedio ya existe, ignorando la solicitud");
                    return "{}";
                }

                $Tipos_Contrato = new Tipos_Contrato();
                $Tipos_Contrato->id_permisos =  $request->has("id_permisos") ? $request->input("id_permisos") : $Tipos_Contrato->id_permisos;
                $Tipos_Contrato->Nombre_Tipo =  $request->has("Nombre_Tipo") ? $request->input("Nombre_Tipo") : $Tipos_Contrato->Nombre_Tipo;
                $Tipos_Contrato->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $Tipos_Contrato = Tipos_Contrato::where([['id', $request->input("id")]])->first();
                if ($Tipos_Contrato != null) {
                    $Tipos_Contrato->id_permisos =  $request->has("id_permisos") ? $request->input("id_permisos") : $Tipos_Contrato->id_permisos;
                    $Tipos_Contrato->Nombre_Tipo =  $request->has("Nombre_Tipo") ? $request->input("Nombre_Tipo") : $Tipos_Contrato->Nombre_Tipo;
                    $Tipos_Contrato->update();
                }
            }

            if ($request->isMethod('delete')) {
                $Tipos_Contrato = Tipos_Contrato::where([['id', $request->input("id")]])->first();
                if ($Tipos_Contrato == null) {
                    return false;
                }
                return $Tipos_Contrato = $Tipos_Contrato->delete();
            }

            return $Tipos_Contrato;
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

    public function Obtener_usuarios(Request $request, $use_for = 0, $offset = 0, $ammount = 20)
    {
        try {

            $usuarios = null;
            //Valida que use_for no sea nulo, dado que el valor viene generalmente de un parámetro opcional
            if(!isset($use_for) || $use_for == null)
            {
                $use_for = 'A';
            }
            //Si es obtener con filtro
            if ($request->has("id") && $request->filled("id")) {
                $usuarios = User::where([['id', $request->input("id")]])->get();
            } 
            else if ($request->has('id_perfiles') && $request->filled('id_perfiles'))
            {
                $usuarios = DB::select('SELECT users.* FROM DocuSwift.users users JOIN DocuSwift.inter_user_perfiles ON DocuSwift.users.id = DocuSwift.inter_user_perfiles.id_user WHERE id_perfiles = '.$request->input('id_perfiles'));                
            }
            else {
                //Si es obtener normal y sin filtro
                if ($use_for == 'A') {
                    $usuarios = DB::select('select * from DocuSwift.users LIMIT ' . $offset . ',' . $ammount);
                } else if ($use_for == 'B') {
                    $id_tipos__usuario = $request->has("id_tipos__usuario") ?  $request->input("id_tipos__usuario") : 1;
                    $usuarios = DB::select('select * from DocuSwift.users WHERE id_tipos__usuario = ' . $id_tipos__usuario . ' LIMIT ' . $offset . ',' . $ammount);
                } else if(str_contains($use_for, "_")) {
                    $strArray = explode("_",$use_for);
                    $numberToUse = 0;
                    if($strArray[0] == '-') //Todos los de la empresa
                    {
                        $numberToUse = $strArray[1];
                        $usuarios = DB::select('SELECT * FROM DocuSwift.users WHERE DocuSwift.users.id_usuario_upper LIKE (SELECT CONCAT(\'%_\', '.$numberToUse.')) LIMIT '. $offset . ',' . $ammount);
                    }
                    else if($strArray[1] == '-') //Todas las empresas donde ha trabajado o trabaja supervisor
                    {
                        $numberToUse = $strArray[0];
                        $usuarios = DB::select('SELECT * FROM DocuSwift.users WHERE DocuSwift.users.id_usuario_upper LIKE (SELECT CONCAT('.$numberToUse.',\'_%\')) LIMIT '. $offset . ',' . $ammount);
                    }
                    else //Todos los de supervisor
                    {   
                        $numberToUse = $strArray[0];
                        $numberToUse2 = $strArray[1];
                        $usuarios = DB::select('SELECT * FROM DocuSwift.users WHERE DocuSwift.users.id_usuario_upper LIKE (SELECT CONCAT(\''.$numberToUse.'_\', '.$numberToUse2.')) LIMIT '. $offset . ',' . $ammount);
                    }   
                } else {
                    //Si es obtener TODO
                    $usuarios = User::get();
                }
            }

            return $usuarios;
        } catch (Exception  $e) {
            session()->flash('failure', "Error al obtener el producto, por favor intente nuevamente");
            //print_r("Error InterceptParaSolicitud: " . $e->getMessage());
            return "{}";
        }
    }

    //Este hace de Crear/Editar y Borrar
    public function Editar_usuarios(Request $request, $hard_delete = false)
    {
        try {
            //TODO:
            /*
            * AGREGAR SEGURIDAD DE FUNCIONAMIENTO A ESTE NIVEL Y ADECUADO FILTRO DE EFECTO (i.e. que empresas solo puedan trabajar sobre sus propios trabajadores, Etc...)
            * o al menos recordar que REQUIERE PERMISOS ESPECIALES
            */

            //dd($request);
            $User = null;
            //Crear objeto nuevo
            if ($request->isMethod('post')) {
                $User = new User();
                $User->name =  $request->has("name") ? $request->input("name") : $User->name;
                $User->Nombre_Cliente =  $request->has("Nombre_Cliente") ? $request->input("Nombre_Cliente") : $User->Nombre_Cliente;
                $User->id_usuario_upper =  $request->has("id_usuario_upper") ? $request->input("id_usuario_upper") : $User->id_usuario_upper;

                $User->Run_Cliente =  $request->has("Run_Cliente") ? $request->input("Run_Cliente") : $User->Run_Cliente;
                $User->Fecha_Nacimiento =  $request->has("Fecha_Nacimiento") ? $request->input("Fecha_Nacimiento") : $User->Fecha_Nacimiento;
                $User->Fecha_Incorporacion =  $request->has("Fecha_Incorporacion") ? $request->input("Fecha_Incorporacion") : $User->Fecha_Incorporacion;
                $User->Fecha_Expiración =  $request->has("Fecha_Expiración") ? $request->input("Fecha_Expiración") : $User->Fecha_Expiración;

                $User->Dirección =  $request->has("Dirección") ? $request->input("Dirección") : $User->Dirección;
                $User->Comuna =  $request->has("Comuna") ? $request->input("Comuna") : $User->Comuna;
                $User->Ciudad =  $request->has("Ciudad") ? $request->input("Ciudad") : $User->Ciudad;
                $User->Telefono =  $request->has("Telefono") ? $request->input("Telefono") : $User->Telefono;

                $User->email =  $request->has("email") ? $request->input("email") : $User->email;
                $User->email_verified_at =  $request->has("email_verified_at") ? $request->input("email_verified_at") : $User->email_verified_at;
                $User->password =  $request->has("password") ? $request->input("password") : $User->password;
                $User->id_tipos__usuario =  $request->has("id_tipos__usuario") ? $request->input("id_tipos__usuario") : $User->id_tipos__usuario;

                $User->save();
            }

            //Actualizar objeto
            if ($request->isMethod('put')) {
                $User = User::where([['id', $request->input("id")]])->first();
                if ($User != null) {
                    $User->name =  $request->has("name") ? $request->input("name") : $User->name;
                    $User->Nombre_Cliente =  $request->has("Nombre_Cliente") ? $request->input("Nombre_Cliente") : $User->Nombre_Cliente;
                    $User->id_usuario_upper =  $request->has("id_usuario_upper") ? $request->input("id_usuario_upper") : $User->id_usuario_upper;

                    $User->Run_Cliente =  $request->has("Run_Cliente") ? $request->input("Run_Cliente") : $User->Run_Cliente;
                    $User->Fecha_Nacimiento =  $request->has("Fecha_Nacimiento") ? $request->input("Fecha_Nacimiento") : $User->Fecha_Nacimiento;
                    $User->Fecha_Incorporacion =  $request->has("Fecha_Incorporacion") ? $request->input("Fecha_Incorporacion") : $User->Fecha_Incorporacion;
                    $User->Fecha_Expiración =  $request->has("Fecha_Expiración") ? $request->input("Fecha_Expiración") : $User->Fecha_Expiración;

                    $User->Dirección =  $request->has("Dirección") ? $request->input("Dirección") : $User->Dirección;
                    $User->Comuna =  $request->has("Comuna") ? $request->input("Comuna") : $User->Comuna;
                    $User->Ciudad =  $request->has("Ciudad") ? $request->input("Ciudad") : $User->Ciudad;
                    $User->Telefono =  $request->has("Telefono") ? $request->input("Telefono") : $User->Telefono;

                    $User->email =  $request->has("email") ? $request->input("email") : $User->email;
                    $User->email_verified_at =  $request->has("email_verified_at") ? $request->input("email_verified_at") : $User->email_verified_at;
                    $User->password =  $request->has("password") ? $request->input("password") : $User->password;
                    $User->id_tipos__usuario =  $request->has("id_tipos__usuario") ? $request->input("id_tipos__usuario") : $User->id_tipos__usuario;

                    $User->update();
                }
            }

            if ($request->isMethod('delete')) {
                $User = User::where([['id', $request->input("id")]])->first();
                if ($User == null) {
                    return false;
                }

                if ($hard_delete) {
                    $Inter_Perf_Perm = Inter_User_Perf::where('id_user', $User->id);
                    if ($Inter_Perf_Perm != null) {
                        $Inter_Perf_Perm->delete();
                    }

                    $Inter_User_Perm = Inter_User_Perm::where('id_user', $User->id);
                    if ($Inter_User_Perm != null) {
                        $Inter_User_Perm->delete();
                    }
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


    public function Obtener_Total_Tabla($nombreTabla)
    {
        try {
            $ammountOfRows = 0;
            $ammountOfRows = DB::select('SELECT COUNT(id) "Total" FROM DocuSwift.' . $nombreTabla);
            return $ammountOfRows;
        } catch (\Throwable $th) {
            return 0;
        }
    }

    public function SubirPDF(Request $request)
    {
        //To save the pdf Uploaded
        $PdfPath = "";
        if ($request->hasFile('pdf')) {
            $PdfPath = $request->file('pdf')->storeAs(
                'pdfs',
                uniqid() . "." . $request->file('pdf')->getClientOriginalExtension(),
                'public',
            );
        }

        //To load and show the pdf uploaded
        $pdfStore = new PdfStorage();
        $pdfStore->pdf_path = $PdfPath;
        return $pdfStore->save();
    }

    public function MostrarPDF($nombrePDF)
    {
        //To load and show the pdf uploaded

        $path = storage_path('app\\public\\pdfs\\' . $nombrePDF . ".pdf");
        //  if (!Storage::exists($path)) {
        //      //abort(404);
        //      dd($path);
        //  }

        //Parte mía, pero probablemente en ella persiste el asunto del Storage:: -Jose-
        //$PdfPath = str_replace("/","\\",$nombrePDF);
        //$path = storage_path('app\\public\\pdfs\\' . $PdfPath);

        //if (!Storage::exists('public/pdfs/' . $PdfPath)) {
        //  abort(404);
        //dd($path);
        //}

        $headers = [
            'Content-Type' => 'application/pdf',
        ];

        return response()->file($path, $headers);
    }

    //TODO: Debe testearse el método
    //Parámetros:
    //$ViewBase => La view donde se mostrará la data (En String)
    //$ContenidoParaElCompact => Todos los datos que hay que enviar al método "compact" del View, como en el método View normal de laravel
    //Return: Retorna la view en un pdf con todos los datos asociados
    //Observaciones: Es posible que la view tenga que ser autocontenida (i.e. un html de tomo y lomo, pero en un blade), 
    //además, es posible que deba usar bootstrap 4 llamado en el header especifico del pdf, eso es solo para generarlo 
    //si porque una vez generado el archivo ya conserva la data en sí, no debería llamar hacia afuera, esto último sin 
    //embargo, requiere verificación.
    public function MostratDataEnPDF($ViewBase, array $content)
    {
        $cont = $content;
        // dd($cont);
        $pdf = Pdf::loadView($ViewBase, compact('cont'));
        //$pdf = Pdf::loadHTML($ViewBase, compact('content'));
        //dd($pdf);
        //$pdf->setPaper('a4', 'portrait');
        return $pdf->stream($ViewBase);
        //return $pdf->download($ViewBase);
    }

    //Genera una venta nueva y la retorna para reservar su ID, con el objetivo de poder generar la transacción (Transbank)
    //Requiere una id de usuario
    public function GetNewIdVenta($idUsuario)
    {
        //Elimina cualquier venta que no tenga venta detalle alguna
        $ventas_a_borrar = Venta::doesnthave('mu_ventas')->get();
        foreach ($ventas_a_borrar as $key => $value) {
            $value->delete();
        }
        //dd($ventas_a_borrar);

        //Genera una venta nueva solo con la ID
        $venta = new Venta();
        $venta->id_users = $idUsuario;
        $venta->Is_Factura_O_Boleta = 2;
        $venta->Is_Vigente = 1;
        $venta->Total = 0;
        $venta->Fecha = \Carbon\Carbon::now()->format('Y-m-d'); //Sino usa la fecha del fallido apocalipsis falso '2012-12-21'
        $venta->Giro = "";
        $venta->save();

        //Retorna dicha venta nueva solo con ID
        return $venta;
    }

    //Permite retornar los resultados necesarios para los buscadores por nombre
    public function BuscadorVariable($tabla, $columnTableName, $nombre, $offset = 0, $ammount = 20)
    {
        $resultados = DB::table($tabla)->where($columnTableName, 'like', '%' . $nombre . '%')->skip($offset)->take($ammount)->get();
        return $resultados;
    }

    public function Obtener_FirmasFirmadas_FirmasTotales($str)
    {
        try {
            $ids_Contrato = explode(",", $str);
            $NumerosFirmasContrato = array();
            foreach ($ids_Contrato as $key => $value) {
                $Contrato = new \stdClass();
                $varTemp = DB::table('correos__firmas')->where(
                    'correos__firmas.id_contratos',
                    $value
                )->get();

                if ($varTemp != null) {
                    $Contrato->FirmasFirmadas = $varTemp->where('Estado', 1)->count();
                    $Contrato->FirmasTotal = $varTemp->count();
                    $NumerosFirmasContrato[] = $Contrato;
                }
            }

            return $NumerosFirmasContrato;
        } catch (\Throwable $th) {
            return 0;
        }
    }




    public function Revisar_Acceso($userid, $eval)
    {
        try {
            $permisos = DB::select('SELECT DocuSwift.permisos.* FROM DocuSwift.permisos JOIN DocuSwift.inter_user_perms ON DocuSwift.inter_user_perms.id_permisos = DocuSwift.permisos.id WHERE  DocuSwift.permisos.Type = 2 AND DocuSwift.inter_user_perms.id_user = '.$userid);
            foreach ($permisos as $key => $value) {
                if (str_contains($value->Nombre,$eval)) {
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
