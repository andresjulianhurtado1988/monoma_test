<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Helpers\JwtAuth;
use App\Models\Candidate;

class CandidateRegisterTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

     $json = '{"name" : "candidato 3",
                "source" : "Fotocasa",
                "owner" : 1
                }';

        $params = json_decode($json); // tengo un objeto
        $params_array = json_decode($json, true); // obtengo un array

        if (!empty($params_array) && !empty($params)) {

                $params_array = array_map('trim', $params_array);
          
                $validate = \Validator::make($params_array, [
                    'name' => 'required|string',
                    'source' => 'required|string',
                    'owner' => 'numeric|min:1'
                ]);
            
              
                $owner = (int)$params_array['owner'];

                $user = User::find($owner);
    
                if (!is_object($user)) {
                
                    $data = array('status' => 'error',
                            'code' => 404,
                            'message' => 'El owner no existe'
                );
        
                return response()->json($data, $data['code']);
                }

                    if ($validate->fails()) {
                        $data = array('status' => 'error',
                                    'code' => 404,
                                    'message' => 'El candidato no se ha creado',
                                    'error' => $validate->errors()
                    );
                    
                    
                    }else{
    
                        // si validación pasa correctamente
                      
                        $candidate = new Candidate();
                        $candidate->name = $params_array['name'];
                        $candidate->source = $params_array['source'];
                        $candidate->owner = $params_array['owner'];
                        $candidate->created_by = 1;
                        //guardar el usuario
                        $candidate->save();
                           
                            // crear el usuario
                        $data = array('status' => 'success',
                                    'code' => 200,
                                    'message' => 'El candidato se ha creado correctamente'
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
