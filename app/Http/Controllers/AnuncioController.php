<?php

namespace App\Http\Controllers;


use App\Models\Anuncio;
use App\Models\Carta;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;



class AnuncioController extends Controller
{
    /*
    public function plantilla(Request $req) {
        $response = ['status'=> 1, 'msg'=>''];
        //recoger la info del request (viene del json)
        $JsonData = $req->getContent();
        //pasar el Json al objeto
        $Data = json_decode($JsonData);


        try {
            $validator = Validator::make(json_decode($JsonData, true), [
                'nombre' => 'required|unique:usuarios| string',
                'email' => 'required|unique:usuarios| string',
                'password' => 'required',
                'rol' => 'required|in:particular,profesional,administrador',
                'biografia' => 'required',
            ]);

            if($validator->fails()){
                $response = ['status'=>0, 'msg'=>$validator->errors()->first()];
            } else {

            }

        } catch (\Exception $error){
            $response['msg'] = "Ha ocurrido un error al añadir : ".$error->getMessage();
            $response['status'] = 0;
        }
        return response()->json($response);
    }*/
    public function buscar(Request $req){ //Pide: api_token y name
        $jdata = $req->getContent();
        $data = json_decode($jdata);

        $response["status"]=1;
        try{
            if(isset($data->carta_nombre)){
                $cartas = Carta::where('nombre', 'like', '%'.$data->carta_nombre.'%')->get();
                if(count($cartas)>0){
                    foreach ($cartas as $key => $carta) {
                        $response["msg"]="Cartas encontradas";
                        $response[$key]["id"] = $carta->id;
                        $response[$key]["nombre"] = $carta->nombre;
                    }
                }else{
                    $response["msg"] = "No hay ninguna coincidencia";
                }
            }else{
                throw new Exception("Error: Introduce un nombre de una carta");
            }
        }catch(\Exception $e){
            $response["status"]=0;
            $response["msg"]=$e->getMessage();
        }
        return response()->json($response);

    }
    public function vender(Request $req) {
        $response = ['status'=> 1, 'msg'=>''];
        //recoger la info del request (viene del json)
        $JsonData = $req->getContent();
        //pasar el Json al objeto
        $data = json_decode($JsonData);

        $validator = Validator::make(json_decode($JsonData, true), [
            'carta_id' => 'required|integer',
            'cantidad' => 'required|integer',
            'precio' => 'required|integer',
        ]);
        if ($validator->fails()) {
            $response = ['status'=>0, 'msg'=>$validator->errors()->first()];
        } else {
            $response = ['status'=>1, 'msg'=>''];
            try {
                $carta = carta::find($data->carta_id);
                if(isset($carta)) {
                    $usuario = User::where('api_token', $data->token)->first();
                    $anuncio = new anuncio();
                    $anuncio->usuario_id = $usuario->id;
                    $anuncio->carta_id = $data->carta_id;
                    $anuncio->cantidad = $data->cantidad;
                    $anuncio->precio= $data->precio;
                    $anuncio->nombre= $carta->nombre;
                    $anuncio->save();

                    $response['msg'] = "Anuncio creado correctamente.";
                    $response['status'] = 1;
                } else {
                    $response['msg'] = "No existe ninguna carta con ese id";
                    $response['status'] = 0;
                }
            } catch (\Exception $error) {
                $response['msg'] = "Se ha producido un error:".$error->getMessage();
                $response['status'] = 0;
            }
        }
        return response()->json($response);
    }

    public function BuscarAnuncio(Request $req) {
        $response = ['status'=> 1, 'msg'=>''];
        $jdata = $req->getContent();
        $data = json_decode($jdata);

        $response["status"]=1;
        try{
            if(isset($data->anuncio_nombre)){
                $anuncios = anuncio::where('nombre', 'like', '%'.$data->anuncio_nombre.'%')->get();
                if(count($anuncios)>0){
                    foreach ($anuncios as $key => $anuncio) {
                        $response["msg"]="Anuncios encontrados";
                        $response[$key]["id"] = $anuncio->id;
                        $response[$key]["nombre"] = $anuncio->nombre;
                        $response[$key]["precio"] = $anuncio->precio;
                        $response[$key]["cantidad"] = $anuncio->cantidad;
                        $vendedor = User::find($anuncio->usuario_id);
                        $response[$key]["vendedor"] = $vendedor->nombre;

                    }
                }else{
                    $response["msg"] = "No hay ninguna coincidencia";
                }
            }else{
                throw new Exception("Error: Introduce un nombre de un anuncio");
            }
        }catch(\Exception $e){
            $response["status"]=0;
            $response["msg"]=$e->getMessage();
        }
        return response()->json($response);
    }
}
