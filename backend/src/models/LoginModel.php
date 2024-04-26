<?php
namespace Jp\Backendmbe\models;

use Jp\Backendmbe\core\Model;
// model de login 
class LoginModel extends Model {

    private $currentDate; 
    
    public function __construct() {
        parent::__construct();
        $this->currentDate = date("Y-m-d H:i:s");
    }

    public function login($user) {
        $sql = "SELECT id,usuario,pws,intentos,nombre FROM usuarios WHERE usuario = '{$user}'";
        parent::set_consultaRegistro($sql);
        $res=parent::get_consultaRegistro();
        return $res;
    }

    public function register($user,$pws,$nombre) {

        // validar si el usuario existe
        $sql = "SELECT id FROM usuarios WHERE usuario = '{$user}'";
        parent::set_consultaRegistro($sql);
        $res=parent::get_consultaRegistro();
        if ($res["mysqli"]["num_rows"] > 0) {
            return array("status" => false, "message" => "Usuario ya existe");
        }

        $pws = password_hash($pws, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (usuario,pws,nombre,fechaRegistro) VALUES ('{$user}','{$pws}','{$nombre}','{$this->currentDate}')";
        $res = parent::ejecutar($sql);

        return $res;
    }

    public function updateIntentos($user, $intentos) {
        $sql = "UPDATE usuarios SET intentos = '{$intentos}' WHERE usuario = '{$user}'";
        $res = parent::ejecutar($sql);
        return $res;
    }

    public function registerToken($user, $token, $timeMaxToken, $refreshToken,$expirationTimeRefreshToken) {
        try {
            $sql = "UPDATE usuarios SET 
                token = '{$token}',
                timeMaxToken = '{$timeMaxToken}',
                refreshToken = '{$refreshToken}',
                expirationTimeRefreshToken = '{$expirationTimeRefreshToken}'
            WHERE usuario = '{$user}'";
            $res = parent::ejecutar($sql);
            return $res;
        } catch (\Exception $e) {
            print_r($e->getMessage());
            return $e;
        }
    }

    public function updateToken($token,$newToken,$timeMaxToken) {
        $sql = "UPDATE usuarios SET token = '{$newToken}',timeMaxToken = '{$timeMaxToken}' WHERE token = '{$token}'";
        $res = parent::ejecutar($sql);
        return $res;
    }

    public function deleteToken($dato,$campo = "token") {
        $sql = "UPDATE usuarios 
        SET token = '',timeMaxToken = '',refreshToken = '',expirationTimeRefreshToken='' 
        WHERE ".$campo." = '{$dato}'";
        $res = parent::ejecutar($sql);
        return $res;
    }

}