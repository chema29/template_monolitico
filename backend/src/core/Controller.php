<?php
namespace Jp\Backendmbe\core;

// class controller base
class Controller{

    function __construct() {
        //echo "<p>Controlador base</p>";
    }

    public function view($view, $data = []) {
        require_once "views/{$view}.php";
    }
    // model
    public function model($model) {
        // namespace
        $model = "Jp\\Backendmbe\\models\\{$model}Model";
        // validamos si existe
        if (!class_exists($model)) {
            return false;
        }

        return new $model();
    }
    // response 
    public function response() {
        return new Response();
    }
}