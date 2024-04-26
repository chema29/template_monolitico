<?php
use Jp\Backendmbe\core\App;
use Jp\Backendmbe\middleware\CorsMiddleware;


// mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// index se sentará en la raíz del proyecto
require_once "config/config.php";
require_once "vendor/autoload.php";

// validar cros origen
(new CorsMiddleware())->handle();

// instacia de la clase App
$app = new App();