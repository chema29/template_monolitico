<?php
use Jp\Backend\core\App;
use Jp\Backend\middleware\CorsMiddleware;


// mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// index se sentará en la raíz del proyecto
require_once "config/config.php";
require_once "vendor/autoload.php";

// validar si esta activo el uso de cors
if (constant("CORS")) {
    // validar cros origen
    (new CorsMiddleware())->handle();
}

// instacia de la clase App
$app = new App();