<?php
namespace Jp\Backend\core;

use Jp\Backend\middleware\AuthMiddleware;
// App class se encarga de manejar las rutas y cargar los controladores

class App {

    public function __construct() {

        // Crear instancia del AuthMiddleware
        $auth = new AuthMiddleware();
        // Validar el token antes de continuar con la lógica de los controladores
        if(!$auth->handle($_SERVER)){
            return false;
        }

        $url = $this->parseUrl();
        // la primera letra de la url se convierte a mayúscula
        $url[0] = ucfirst($url[0]);
        $controllerName = "{$url[0]}Controller";
        $controllerClass =  "\\Jp\\Backend\\controllers\\" . $controllerName;
        // verifica si existe la clase
        //if (!file_exists("../controllers/{$controllerName}.php")) {
        if (!class_exists($controllerClass)) {
            //$controllerName = "ErrorController";
            //echo "No existe el controlador {$controllerName}";
            echo $this->response()->error(404, "No existe el controlador {$controllerName}");
            return false;
        }

        //require_once "../controllers/{$controllerName}.php";
        $controller = new $controllerClass;
        $methodName = isset($url[1]) ? $url[1] : "index";
        if (!method_exists($controller, $methodName)) {
            //$controllerName = "ErrorController";
            //echo "No existe el método {$methodName}";
            $this->response()->error(404, "No existe el método {$methodName}");
            return false;
        }
        unset($url[0]);
        unset($url[1]);
        $params = $url ? array_values($url) : [];
        call_user_func_array([$controller, $methodName], $params);
    }

    public function parseUrl() {
        if (isset($_GET["url"])) {
            return explode("/", filter_var(rtrim($_GET["url"], "/"), FILTER_SANITIZE_URL));
        }
    }
    // llamar response
    public function response() {
        return new Response();
    }
}
