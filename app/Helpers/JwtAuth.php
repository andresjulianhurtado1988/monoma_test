<?php
namespace App\Helpers;

//use Firebase\JWT;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class JwtAuth{

    public $key;

    public function __construct(){
        $this->key = "clave_secreta_1408";
    }

    public function signup($username, $password, $role, $getToken = NULL)
    {

    // si existe el usuario con esas credenciales
        $user = User::where([
            'username' => $username,
            'password' => $password,
            'role' => $role
        ])->first();

    // comprobar si son correctas

    $signup = false;

    if (is_object($user)) {
        $signup = true;
    }

    // generar el token

    if ($signup) {
        $token = array(
            'id' => $user->id,
            'username' => $user->username,
            'role' => $user->role,
            'iat' => time(),
            'exp' => time() + (7*24*60*60)

        );

        $jwt = JWT::encode($token, $this->key, 'HS256');
        $decode = JWT::decode($jwt, $this->key, ['HS256']);

        if (is_null($getToken)) {
           $data = $jwt;
        }else {
            $data = $decode;
        }
       /*   $data = array('status' => 'success',
                    'message' => 'Login correcto',
                    'token' => $data
                    );
                    
            */
    }else {

        $data = array('status' => 'error',
                'message' => 'Login incorrecto' );

    }
    // devolver los datos decodificados o el token en función de un paramentro
    return $data;
    }

    public function checkToken($jwt, $getIdentity = false)
    {
            $auth = false;
            try {
                $jwt = str_replace('"', '', $jwt);
                $decoded = JWT::decode($jwt, $this->key, ['HS256']);
            } catch (\UnexpectedValueException $e) {
                $auth = false;
              
            } catch (\DomainException $e){
                $auth = false;
            }
           
            if (!empty($decoded) && is_object($decoded) && isset($decoded->id)) 
            {
                $auth = true;
            }else {
                $auth = false;
            }

            if ($getIdentity) {
                return $decoded;
            }

            return $auth;
    }
   
}