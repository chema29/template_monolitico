<?php
namespace Jp\Backendmbe\controllers;

use Jp\Backendmbe\core\Controller;
// login user y password generar token y devolverlo
class LoginController extends Controller{
    
    public function __construct() {
        parent::__construct();
    }

    public function authentication() {
        try{
            $user = $_POST["user"];
            $password = $_POST["password"];
            // obtener el usaurio de la database
            $model = parent::model("Login");
            if ($model === false) {
                echo $this->response()->error(404, "No existe el modelo Login");
                return false;
            } 
            $res = $model->login($user);
            if (empty($res)) {
                echo $this->response()->error(500, "Error de sistema");
                return false;
            }
            if (empty($res["status"])) {
                echo $this->response()->error(409,"No pudo ser procesada debido a un conflicto con el estado actual del recurso que esta identifica.", $res);
                return false;
            }
            // validar si el usuario existe 
            if ($res["mysqli"]["num_rows"] == 0) {
                echo $this->response()->error(401, "Usuario incorrectos");
                return false;
            }
        
            // mapear $res en un array asociativo id,usuario,pws,token,timeMax
            $id = $res["result"][0];
            $usuario = $res["result"][1];
            $pws = $res["result"][2];
            $intentos = $res["result"][3];
            $nombre = $res["result"][4];

            // validar intentos
            if ($intentos >= 3) {
                // elminar token
                $model->deleteToken($usuario,"usuario");
                echo $this->response()->error(401, "Usuario bloqueado");
                return false;
            }

            // decodificar password_hash
            if (!password_verify($password, $pws)) {
                $intentos++;
                $model->updateIntentos($user, $intentos);
                echo $this->response()->error(401, "Clave o usuario incorrectos");
                return false;
            }

            // actualizar intentos 
            $intentos = 0;
            $model->updateIntentos($user, $intentos);
            // generar token
            $token = $this->generateToken();
            // que dure 10 minutos
            $timeMax = time() + (60 * 10);
            // generar token refresh
            $refreshToken = $this->generateToken();
            // que dure un mes
            $expirationTimeRefreshToken = time() + (60 * 60 * 24 * 30);
            // registrar token
            $model->registerToken($user, $token, $timeMax, $refreshToken, $expirationTimeRefreshToken);

            // pasar datos del usaurio a la clase User
            $this->User()->setId($id);
            $this->User()->setUsuario($usuario);
            $this->User()->setNombre($nombre);
            $this->User()->setIntentos($intentos);
            $this->User()->setToken($token);
            $this->User()->setTimeMaxToken($timeMax);
            $this->User()->setRefreshToken($refreshToken);
            $this->User()->setExpirationTimeRefreshToken($expirationTimeRefreshToken);

            $this->response()->success(200, "OK", [
                "token" => $token, 
                "timeMax" => $timeMax,
                "tokenRefresh" => $refreshToken,
                "nombre" => $nombre
            ]);
        }catch(\Exception $e){
            echo $this->response()->error(500, "Error de sistema");
        }
    
    }

    private function generateToken() {
        $header = [
            "typ" => "JWT",
            "alg" => "HS256"
        ];
        $header = json_encode($header);
        $header = base64_encode($header);
        $payload = [
            "iat" => time(),
            "exp" => time() + (60 * 60),
            "user" => $this->User()->getUsuario(),
            'iduser' => $this->User()->getId(),
        ];
        $payload = json_encode($payload);
        $payload = base64_encode($payload);
        $signature = hash_hmac("sha256", "$header.$payload", "secret", true);
        $signature = base64_encode($signature);
        $token = "$header.$payload.$signature";
        return $token;
    }

    public function validateToken() {
        try{
           // obtener el token bearer
            $token = $_SERVER["HTTP_AUTHORIZATION"];
            $token = str_replace("Bearer ", "", $token);
            // validar token
            $model = parent::model("Login");
            if ($model === false) {
                echo $this->response()->error(404, "No existe el modelo Login");
                return false;
            } 

            $res = $model->validateToken($token);
            if (empty($res)) {
                echo $this->response()->error(500, "Error de sistema");
                return false;
            }
            
            if (empty($res["status"])) {
                echo $this->response()->error(409,"No pudo ser procesada debido a un conflicto con el estado actual del recurso que esta identifica.", $res);
                return false;
            }
            // validar si el token existe
            if ($res["mysqli"]["num_rows"] == 0) {
                echo $this->response()->error(401, "Token invalido");
                return false;
            }
            // validar si el token esta expirado
            $timeMaxToken = $res["result"][4];
            if ($timeMaxToken < time()) {
                echo $this->response()->error(401, "Token expirado");
                return false;
            }

            $this->response()->success(200, "OK");
        }catch(\Exception $e){
            echo $this->response()->error(500, "Error de sistema");
        }
    }

    public function updateToken($token){
        try{
            $model = parent::model("Login");
            if ($model === false) {
                echo $this->response()->error(404, "No existe el modelo Login");
                return false;
            }

            $newToken = $this->generateToken();
            // que dure 1 hora
            $timeMax = time() + (60 * 60);
            $res = $model->updateToken($token,$newToken,$timeMax);
            if (empty($res)) {
                echo $this->response()->error(500, "Error de sistema");
                return false;
            }
            if (empty($res["status"])) {
                echo $this->response()->error(409,"No pudo ser procesada debido a un conflicto con el estado actual del recurso que esta identifica.", $res);
                return false;
            }
            $this->response()->success(200, "OK", ["token" => $newToken]);
        }catch(\Exception $e){
            echo $this->response()->error(500, "Error de sistema");
        }
    }

    public function register() {
        try{

            // validar que los campos no esten vacios
            if (empty($_POST["user"]) || empty($_POST["password"]) || empty($_POST["nombre"])) {
                echo $this->response()->error(400, "Campos requeridos");
                return false;
            }

            $user = $_POST["user"];
            $password = $_POST["password"];
            $nombre = $_POST["nombre"];

            $model = parent::model("Login");
            if ($model === false) {
                echo $this->response()->error(404, "No existe el modelo Login");
                return false;
            } 
            $res = $model->register($user, $password, $nombre);
            if (empty($res)) {
                echo $this->response()->error(500, "Error de sistema");
                return false;
            }
            if ($res["status"] === null) {
                echo $this->response()->error(409,"No pudo ser procesada debido a un conflicto con el estado actual del recurso que esta identifica.", $res);
                return false;
            }

            if($res["status"]){
                echo $this->response()->success(200, "enhorabuena usuario registrado");
                return true;
            }else{
                echo $this->response()->error(500,$res["message"]);
                return false;
            }

        }catch(\Exception $e){
            echo $this->response()->error(500, "Error de sistema");
        }
    }

}