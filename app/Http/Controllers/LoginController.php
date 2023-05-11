<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Helpers\JwtAuth;
use Illuminate\Support\Facades\Cache;

class LoginController extends Controller
{

    public function login(Request $request)
    {
        Cache::flush();

            $jwtAuth = new \JwtAuth();

            // recibir los datos por post
            
            $json = $request->input('json', null);
            $params = json_decode($json); // tengo un objeto
            $params_array = json_decode($json, true); // obtengo un array
            
            // validar los datos

            $validate = \Validator::make($params_array, [
                'username' => 'required',  // comprobar si el usuario existe
                'password' => 'required',
                'role' => 'required|string'
            ]);

            if ($validate->fails()) {
                $signup = array('status' => 'error',
                            'code' => 404,
                            'message' => 'El usuario no se ha podido loguear',
                            'error' => $validate->errors()
                        );
                    }else {

                        // cifrar contraseña
                    $pwd =   hash('sha256', $params->password);

                    $signup =  $jwtAuth->signup($params->username, $pwd, $params->role);

                    if (!empty($params->gettoken)) {
                        $signup =  $jwtAuth->signup($params->username, $pwd, $params->role, true);
                       
                    }
                }
        
            // devolver token o datos



       //     return response()->json($data, $data['code']);
            return response()->json($signup, 200);
            
    }


    
}
