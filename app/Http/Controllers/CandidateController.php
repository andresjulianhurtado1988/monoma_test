<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\Candidate;
use App\Helpers\JwtAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CandidateController extends Controller
{
    public function candidateRegister(Request $request)
    {

          // recibir los datos
        $json = $request->input('json', null);
        $params = json_decode($json); // tengo un objeto
        $params_array = json_decode($json, true); // obtengo un array

        $user = $this->getIdentity($request);

        if ($user->role == "agent") {

            $data = array('status' => 'error',
                    'code' => 404,
                    'message' => 'El rol del usuario no es manager'
        );

        return response()->json($data, $data['code']);
        }

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
                        $candidate->created_by = $user->id;
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
        return response()->json($data, $data['code']);

    }

    public function showAllCandidates(Request $request)
    {

        $user = $this->getIdentity($request);

        if ($user->role == "agent") {

            if (Cache::has('candidates')) {
                $candidates = Cache::get('candidates');
            }else {
                $candidates = Candidate::where('owner', $user->id)->get();
                Cache::put('candidates', $candidates);
            }
        
        }else {

            if (Cache::has('candidates')) {
                $candidates = Cache::get('candidates');
            }else {
                $candidates = Candidate::all();
                Cache::put('candidates', $candidates);
            }
           
        }
        return response()->json([
            'code' => 200,
            'status' => 'success',
            'candidates' => $candidates
        ]);
       
    }

    public function showCandidate(Request $request, $id)
    {

        $user = $this->getIdentity($request);

        $id_usuario = $user->id;

        if ($user->role == "agent") {
            $candidates = DB::table('candidate AS c')
            ->select('c.name', 'c.source', 'u.username')
            ->leftjoin('users AS u', 'u.id','=','c.owner')
            ->where('c.id', $id)
            ->first();

        }else {
            $candidates = DB::table('candidate AS c')
            ->select('c.name', 'c.source', 'u.username')
            ->leftjoin('users AS u', 'u.id','=','c.owner')
            ->where([['c.id', $id],['c.owner', $id_usuario]])
            ->first();
        }

       if (is_object($candidates)) {

        $data = array('status' => 'success',
        'code' => 200,
        'candidates' =>  $candidates
            );
            }else {
                $data = array('status' => 'error',
                'code' => 400,
                'message' =>  'El candidato no está asignado a este usuario'
            );
    }

    return response()->json($data, $data['code']);
       
    }

    private function getIdentity($request)
    {
        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        $user = $jwtAuth->checkToken($token, true);

        return $user;
    }
}
