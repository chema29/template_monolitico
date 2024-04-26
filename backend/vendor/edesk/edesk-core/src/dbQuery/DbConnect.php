<?php

namespace Edesk\dbQuery;

use mysqli;


class DbConnect
{
    private $username;
    private $passwd;
    private $host;
    private $dbname;
    protected $resultDB;

    public function __construct()
    {
        $this->username = DB_USER;
        $this->passwd = DB_PASS;
        $this->host = DB_HOST;
        $this->dbname = DB_NAME;
    }

    public function connectClose($connect)
    {
        $connect->close();
    }

    protected function getConnection()
    {
        if (empty($this->dbname) || empty($this->host)  || empty($this->username)) {
            $this->resultDB = array("status" => false, "result" => "No tiene ingresado los datos de conexion");
        }

        $connect = new mysqli($this->host, $this->username, $this->passwd, $this->dbname);
        if ($connect->connect_error) {
            $this->resultDB =  array("status" => false, "result" => 'Error de conexion (' . $connect->connect_errno . ') ' . $connect->connect_error);;
        }
        return $connect;
    }
}
