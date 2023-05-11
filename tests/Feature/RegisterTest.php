<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Helpers\JwtAuth;

class RegisterTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {

        $response = $this->get('/');

        $json = '{"username" : "agente342",
                    "password" : "pass"}';


        //  $json = $request->input('json', null);
        $params = json_decode($json); // tengo un objeto
        $params_array = json_decode($json, true); // obtengo un array

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

    
        $response->assertStatus(200);
    }
}
