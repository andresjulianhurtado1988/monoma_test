<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\Category;
use App\Models\Post;
use App\Helpers\JwtAuth;

class PostController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('api.auth', ['except' => ['index', 
                                                    'show', 
                                                    'getImage', 
                                                    'getPostByCategory', 
                                                    'getPostByUser'
                                                    ]]);
    }

    public function index()
    {
     
            $posts = Post::all()->load('category');

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'posts' => $posts
            ]);
    }

    public function show($id)
    {
        $posts = Post::find($id)->load('category');

        if (is_object($posts)) {
           $data = [
                'code' => 200,
                'status' => 'success',
                'posts' => $posts
            ];
        }else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'No se ha encontrado ninguna entrada'
            ];
        }
        return response()->json($data, $data['code']);
       
    }

    public function store(Request $request)
    {
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {


            $user = $this->getIdentity($request);

            // validar los datos

            $validate = \Validator::make($params_array, [
                    'title' => 'required',
                    'content' => 'required',
                    'category_id' => 'required',
                    'image' => 'required'

            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Faltan datos'
                ];
            }else {

                $post = new Post();
                $post->user_id =  $user->sub;
                $post->category_id = $params->category_id;
                $post->title = $params->title;
                $post->content = $params->content;
                $post->image = $params->image;
                $post->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Se han enviado los datos correctamente',
                    'post' => $post
                ];
            }


        }else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Se han enviado los datos correctamente'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request)
    {
            // recoger los datos por post

            $json = $request->input('json', null);
            $params_array = json_decode($json, true);
         

            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'datos enviados incorrectamente'
            ];

        // validar los datos
            if (!empty($params_array)) {
                
            $validate = \Validator::make($params_array, [
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required'
            ]);

            if ($validate->fails()) {
                $data['errors'] = $validate->errors();
               return response()->json($data, $data['code']);
            }
            
            // eliminar lo que no queremos actualizar

            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['created_at']);
            unset($params_array['user']);


            

            $user = $this->getIdentity($request);
          
            // buscar el registro
            $post = Post::where('id', $id)
            ->where('user_id', $user->sub)
            ->first();

            if (is_object($post) && !empty($post)) {
               
            // actualizar el registro
          
            $post->update($params_array);

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post,
                    'changes' => $params_array
                ];
            }

            // en caso de tener más de una condición en el updateOrCreate, se debe enviar como un array

            /*
            $where = [
                'id'=> $id, 
                'user_id' => $user->sub
            ];
          
            $post = Post::updateOrCreate($where, $params_array)->toSql();
            */
            // devolver respuesta



        }
            return response()->json($data, $data['code']);
    }

    public function destroy($id, Request $request)
    {
            // conseguir el post

           $user = $this->getIdentity($request);

        $post = Post::where('id', $id)
                    ->where('user_id', $user->sub)
                    ->first();

            if (!empty($post)) {

            // borrarlo
                $post->delete();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post,
                    'message' => 'Entrada eliminada con éxito'
                ];

            }else {
                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'El post no existe'
                ];
            }
            // devolver algo

        
            return response()->json($data, $data['code']);
    }

        private function getIdentity($request)
        {
            $jwtAuth = new JwtAuth();
            $token = $request->header('Authorization', null);
            $user = $jwtAuth->checkToken($token, true);

            return $user;
        }


        public function upload(Request $request)
        {
                    // recoger imagen de peticion

                    $image = $request->file('file0');

                     // validar la imagen
    
                    $validate = \Validator::make($request->all(), 
                    [
                        'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
                    ]);
    
                    // subir y guardar imagen
                    if (!$image || $validate->fails()) {
    
                        $data = array('status' => 'error',
                        'code' => 400,
                        'message' => 'Error al subir imagen'
                    );

                 }else {
                    $image_name = time().$image->getClientOriginalName();
                    \Storage::disk('img_post')->put($image_name, \File::get($image));

                    $data = array('status' => 'success',
                    'code' => 200,
                    'image' => $image_name
                );
            }
                 
                    // guardar imagen en disco
                    // devolver datos

                    return response()->json($data, $data['code']);
        }

        public function getImage($filename)
        {
    
                    $isset = \Storage::disk('img_post')->exists($filename);
    
                    if ($isset) {
                        $file = \Storage::disk('img_post')->get($filename);
                        return new Response($file, 200);
                    }else{
                        
                        $data = array('status' => 'error',
                        'code' => 404,
                        'message' => 'La imagen no existe'
                    );
    
                    return response()->json($data, $data['code']);
                    }
    
                
        }

        public function getPostByCategory($id)
        {
            $post = Post::where('category_id', $id)->get();

            return response()->json([
                'status' => 'success',
                'post' => $post
            ], 200);
        }

        public function getPostByUser($id)
        {
            $post = Post::where('user_id', $id)->get();

            return response()->json([
                'status' => 'success',
                'post' => $post
            ], 200);
        }
}
