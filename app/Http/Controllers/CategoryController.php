<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\Category;



class CategoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

   public function index()
   {
    $categories = Category::all();


    return response()->json([
            'code' => 200,
            'status' => 'success',
            'categories' => $categories
        ]);
   }

   public function show($id)
   {


    $categories = Category::find($id);

    if (is_object($categories)) {

        $data = array('status' => 'success',
                        'code' => 200,
                        'categories' => $categories
                    );
    
    }else {
        $data = array('status' => 'error',
                        'code' => 404,
                        'message' => 'La categoría no existe'
                    );
    }


        return response()->json($data, $data['code']);
        
   }

   public function store(Request $request)
   {
            // recoger datos por post


            $json = $request->input('json', null);
            $params_array = json_decode($json, true);

            if (!empty($params_array)) {

              // validar los datos
            $validate = \Validator::make($params_array, [
                'name' => 'required',
            ]);

            if ($validate->fails()) {
                $data = array('status' => 'error',
                            'code' => 400,
                            'message' => 'La categoría no se ha creado',
                        );
                        
            }else{

                // guardar la categoría

                $category = new Category();
                $category->name = $params_array['name'];
                $category->save();
                   
                $data = array('status' => 'success',
                            'code' => 200,
                            'category' => $category
                        );
            }
            
        }else {
            $data = array('status' => 'error',
            'code' => 400,
            'message' => 'No se ha enviado ninguna categoría',
        );
        }
            // devolver resultado
            return response()->json($data, $data['code']);

   }

   public function update($id, Request $request)
   {
            // recoger los datos

            $json = $request->input('json', null);

            $params_array = json_decode($json, true); // obtengo un array 

            if (!empty($params_array)) {
              
                $validate = \Validator::make($params_array, [
                    'name' => 'required',
                ]);
    
          
                    unset($params_array['id']);
                    unset($params_array['created_at']);
                    // actualizar usuario en DB
  
                    $categoty = Category::where('id', $id)->update(
                      $params_array
                    );

                    $data = array('status' => 'success',
                    'code' => 200,
                    'message' => 'Se ha actualizado la categoría',
                    'category' => $params_array
                );

              



            }else {
                $data = array('status' => 'error',
                'code' => 400,
                'message' => 'No se ha enviado ninguna categoría',
            );
            }

            return response()->json($data, $data['code']);

   }
}
