<?php

namespace Jp\Backend\controllers;

use Jp\Backend\core\Controller;
// controller de clientes

class CustomerController extends Controller{
    
    public function __construct() {
        parent::__construct();

        //echo "<p>Controlador de clientes</p>";
    }

    public function index() {
        $this->response()->success(200, "OK", ["customers" => "Listado de clientes"]);
    }

}