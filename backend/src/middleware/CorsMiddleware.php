<?php
namespace Jp\Backend\middleware;
use Jp\Backend\core\Middleware;
// una class que valida el origen de la petición
// Configuración CORS
// Permitir solicitudes desde frontend en la IP específica produccion
class CorsMiddleware extends Middleware {
    // propiedad para almacenar la ip permitida
    private $allowedOrigin;
    private $ip;

    public function __construct() {
        $this->allowedOrigin = constant("allowedOrigin");
    }

    public function handle(){
        $res=$this->handleCors();
        if ($res) {
            header("Access-Control-Allow-Origin: ".$this->ip);
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            header("Access-Control-Allow-Headers: Content-Type, Authorization");
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Max-Age: 86400");
            return true;
        }
        // responder con un error
        echo $this->response()->error(403, "No tiene permiso para acceder a este recurso.");
        exit;
    }

    // funcion para validar el origen de la petición
    protected function handleCors() {
        // obterner el la ip del servidor real
        if(empty($_SERVER["HTTP_X_REAL_IP"])){
            if(empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
                if(empty($_SERVER["SERVER_ADDR"])){
                    return false;
                }else{
                    $this->ip = $_SERVER["SERVER_ADDR"];
                }
            }else{
                $this->ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            }
        }else{
            $this->ip = $_SERVER["HTTP_X_REAL_IP"];
        }

        // validar si la realIp esta en el array de ip permitidas
        if (in_array($this->ip, $this->allowedOrigin)) {
            // si es permitida retornar true
            return true;
        }

        // si no es permitida retornar false
        return false;

    }
}