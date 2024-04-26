<?php
// middleware/AuthMiddleware.php
namespace Jp\Backend\middleware;

use Jp\Backend\core\Middleware;

class AuthMiddleware extends Middleware{
    public function handle($request) {

        // crear excepciones para las rutas que no necesitan autenticación

        // validar en que ambiente se encuentra la aplicación
        if (PRODUCTION) {
            // si esta en producción
            $noAuth = [
                "/login/authentication",
                "/login/register",
                "/login/refreshToken",
            ];
        } else {
            // si esta en desarrollo
            $noAuth = [
                "/template/template_monolitico/backend/login/authentication",
                "/template/template_monolitico/backend/login/register",
                "/template/template_monolitico/backend/login/refreshToken",
            ];
        }
        // validar si la ruta no necesita autenticación
        if (in_array($request["REQUEST_URI"], $noAuth)) {
            
            // si quiere actualizar el token
            if ($request["REQUEST_URI"] == $noAuth[2]) {
                // Valida el token
                echo $this->refreshToken($request);
                return  false;
            }
            return true;
        }

        // validar que HTTP_AUTHORIZATION exista
        if (empty($request["HTTP_AUTHORIZATION"])) {
            echo $this->response()->error(401, "Negación de acceso. Token no proporcionado.");
            return false;
            //die("Acceso no autorizado");
        }
        // Obtén el token del encabezado bearer
        $token = $request["HTTP_AUTHORIZATION"];
        $token = explode("Bearer ", $token);
        $token = $token[1];
        // Valida el token
        return $this->validarToken($token);
    }

    public function refreshToken($request) {
        // validar que HTTP_AUTHORIZATION exista
        if (empty($request["HTTP_AUTHORIZATION"])) {
            echo $this->response()->error(401, "Negación de acceso. Token no proporcionado HTTP_AUTHORIZATION.");
            return false;
        }
        // Obtén el token del encabezado bearer
        $token = $request["HTTP_AUTHORIZATION"];
        $token = explode("Bearer ", $token);
        $refreshToken = $token[1];
        // validar si es el token de refresco
        $sql = "SELECT id,usuario,pws,token,timeMaxToken,intentos,nombre FROM usuarios WHERE refreshToken = '{$refreshToken}'";
        parent::set_consultaRegistro($sql);
        $res=parent::get_consultaRegistro();

        if ($res === false) {
            echo $this->response()->error(404, "No existe el modelo Login");
            return false;
        }

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

        // colocar datos del usuario en el modelo
        $this->user()->setId($res["result"][0]);
        $this->user()->setUsuario($res["result"][1]);
        $this->user()->setNombre($res["result"][6]);

        // generar nuevo token
        $token = $this->user()->generateToken();
        // que dure 10 minutos
        $timeMax = time() + (60 * 10);
        // actualizar token
        $sql = "UPDATE usuarios SET token = '{$token}',timeMaxToken = '{$timeMax}' WHERE refreshToken = '{$refreshToken}'";
        $res=parent::ejecutar($sql);

        if($res["mysqli"]["affected_rows"] == 0){
            echo $this->response()->error(500, "Error al actualizar el token");
            return false;
        }

        // retornar el nuevo token
        return $this->response()->success(200, "OK", ["token" => $token]);
    }

    private function validarToken($token) {
        // Aquí realizas la validación del token, por ejemplo, utilizando JWT
        // Retorna true si el token es válido, false en caso contrario
        // ... Lógica de validación del token ...
        $sql = "SELECT id,usuario,pws,token,timeMaxToken,intentos,nombre FROM usuarios WHERE token = '{$token}'";
        parent::set_consultaRegistro($sql);
        $res=parent::get_consultaRegistro();
        if ($res === false) {
            echo $this->response()->error(404, "No existe el modelo Login");
            return false;
        }

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

        //$this->response()->success(200, "OK");
        return true;
    }
}
