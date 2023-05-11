<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Helpers\JwtAuth;

class UserController extends Controller
{

   public function register(Request $request)
   {

    // recibir los datos
    $json = $request->input('json', null);
    $params = json_decode($json); // tengo un objeto
    $params_array = json_decode($json, true); // obtengo un array

  
    // validar los datos
    if (!empty($params_array) && !empty($params)) {

        $params_array = array_map('trim', $params_array);
      
            $validate = \Validator::make($params_array, [
                'username' => 'required|unique:users',
                'password' => 'required',
            ]);

                if ($validate->fails()) {
                    $data = array('status' => 'error',
                                'code' => 404,
                                'message' => 'El usuario no se ha creado',
                                'error' => $validate->errors()
                );
                
                
                }else{

                    // si validación pasa correctamente

                     // cifrar contraseña
                 $pwd =   hash('sha256', $params->password);

                    $user = new User();
                    $user->username = $params_array['username'];
                    $user->is_active = 1;
                    $user->password = $pwd;
                    $user->role = 'agent';

                    //guardar el usuario

                    $user->save();
                       
                        // crear el usuario
        $data = array('status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario se ha creado correctamente'
                );
                }

            }else {
                $data = array('status' => 'error',
                'code' => 404,
                'message' => 'Los datos enviados no son correctos'
            );

    }
    return response()->json($data, $data['code']);


   }

   
}
