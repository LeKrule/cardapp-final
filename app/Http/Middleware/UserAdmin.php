<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Exception;
use Illuminate\Http\Request;

class UserAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)

    {
        $JsonData = $request->getContent();
        //pasar el Json al objeto
        $Data = json_decode($JsonData);
        try{
            $user = User::where('api_token', $Data->token)->first() ;
            $response['aaaaaaaaaa'] = "cagaste";
            if(!isset($user)){
                throw new Exception("Error: Ese token no existe");
            }
            if($user->rol == 'administrador'){

                return $next($request);

            }else{
                $response['rrrrr'] = 'El usuario no tiene los permisos necesarios';

            }
        }catch (\Exception $error){
            $response['msg'] = "Ha ocurrido un error ".$error->getMessage();
            $response['status'] = 69;
    }
    return response()->json($response);
}}
