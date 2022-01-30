<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Carta;
use App\Models\Coleccion;
use App\Models\Pertenece;

class CartasController extends Controller
{
    public function CrearCarta(Request $req) {
        $response = ['status'=> 1, 'msg'=>''];
        //recoger la info del request (viene del json)
        $JsonData = $req->getContent();
        //pasar el Json al objeto
        $Data = json_decode($JsonData);
        $carta = new Carta();
        $pertenece = new Pertenece();

        try {
            $validator = Validator::make(json_decode($JsonData, true), [ //creo el validator y le indico las caracteristicas de los datos necesarios
                'nombre' => 'required| string',
                'descripcion' => 'required| string',
                'coleccion_id' => 'required| integer',
            ]);

            if($validator->fails()){
                $response = ['status'=>0, 'msg'=>$validator->errors()->first()]; //si los datos introducidos son erroneos salta un error
            } else {
                $carta->nombre = $Data->nombre; //asocio los datos a cada apartado de la tabla para crear una nueva carta
                $carta->descripcion = $Data->descripcion;
                $carta -> save();
                $pertenece->coleccion_id = $Data->coleccion_id;//asocio la carta a una coleccion
                $pertenece->carta_id = $carta->id;
                $pertenece->save();
                $response['msg'] = " la carta ha sido creado correctamente";
                $response['status'] = 1;
            }

        } catch (\Exception $error){
            $response['msg'] = "Ha ocurrido un error al añadir : ".$error->getMessage();
            $response['status'] = 0;
        }
        return response()->json($response);

    }

    public function CrearColeccion(Request $req) {
        $response = ['status'=> 1, 'msg'=>''];
        //recoger la info del request (viene del json)
        $JsonData = $req->getContent();
        //pasar el Json al objeto
        $Data = json_decode($JsonData);
        $coleccion = new Coleccion();
        $pertenece = new Pertenece();
        $carta = new Carta();

        $validator = Validator::make(json_decode($JsonData, true), [ //creo el validator y le indico las caracteristicas de los datos necesarios
            'nombre' => 'required|unique:coleccions|string',
            'simbolo' => 'required|string',
            'fecha' => 'required',//en este validator le indico el formato en que debe ser introcida fecha
            'carta_id' => 'integer',
            'carta_nombre' => 'string',
            'carta_descripcion' => 'string',

        ]);

        if ($validator->fails()) {
            $response = ['status'=>0, 'msg'=>$validator->errors()->first()];
        } else {
            $response = ['status'=>1, 'msg'=>''];
            try {
                $coleccion = new coleccion();
                $coleccion->nombre = $Data->nombre;//asocio los datos a cada apartado de la tabla para crear una nueva coleccion
                $coleccion->simbolo = $Data->simbolo;
                $coleccion->fecha = $Data->fecha;
                $coleccion->save();
                if(isset($Data->carta_id)){
                    $carta = carta::where('id', $Data->carta_id)->first();
                    if(!isset($carta)){
                        $response['msg'] = "No existe ninguna carta con ese nombre";
                        $response['status'] = 0;
                        return response()->json($response);
                    }
                    $pertenece = new pertenece();
                    $pertenece->carta_id = $carta->id;//asocio los ids de carta y de coleccion para indicar la pertenecia de la carta a esta
                    $pertenece->coleccion_id = $coleccion->id;
                    $pertenece->save();
                }else{
                    $carta = new carta();
                    $carta->nombre = $Data->carta_nombre;//asocio los datos a cada apartado de la tabla para crear una nueva carta
                    $carta->descripcion = $Data->carta_descripcion;
                    $carta->save();
                    $pertenece = new pertenece();
                    $pertenece->carta_id = $carta->id;//asocio los ids de carta y de coleccion para indicar la pertenecia de la carta a esta
                    $pertenece->coleccion_id = $coleccion->id;
                    $pertenece->save();
                }
                $response['msg'] = "La coleccion ".$coleccion->id." se ha creado con exito"; // se envia un mensaje indicando que todo ha ido bien
            } catch (\Exception $error) {
                $response['msg'] = "Se ha producido un error:".$error->getMessage();
                $response['status'] = 0;
            }
        }
        return response()->json($response);
    }
    public function AñadirCarta(Request $req) {
        $response = ['status'=> 1, 'msg'=>''];
        //recoger la info del request (viene del json)
        $JsonData = $req->getContent();
        //pasar el Json al objeto
        $Data = json_decode($JsonData);




        try {
            $validator = Validator::make(json_decode($JsonData, true), [
                'carta_id' => 'required:cartas| integer',
                'coleccion_id' => 'required:coleccions| integer',

            ]);

            if($validator->fails()){
                $response = ['status'=>0, 'msg'=>$validator->errors()->first()];
            } else {
                $coleccion = Coleccion::find($Data->coleccion_id);
                $carta = Carta::find($Data->carta_id);
                if(isset($coleccion)&&isset($carta)){
                    $pertenece = new Pertenece();
                    $pertenece->coleccion_id = $Data->coleccion_id;//asocio la carta a una coleccion
                    $pertenece->carta_id = $carta->id;
                    $pertenece->save();
                }else{
                    $response['msg'] = "Ha ocurrido un error al añadir la carta a la coleccion";
                    $response['status'] = 0;
                }
            }

        } catch (\Exception $error){
            $response['msg'] = "Ha ocurrido un error al añadir la carta a la coleccion : ".$error->getMessage();
            $response['status'] = 0;
        }
        return response()->json($response);

    }
}
