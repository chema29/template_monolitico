<?php

namespace Edesk\dbQuery;

use Throwable;
use Edesk\dbQuery\DbConnect;

class MyQuery extends DbConnect
{
    protected $getConsultarRegistros;
    protected $getConsultaRegistro;
    protected $dbProcedureData;
    protected $call_nemaProcedute;
    private $mysqli;
    private $status;
    private $error;
    private $sql;
    private $warning;
    private $info;
    private $Affected_rows;
    private $num_rows;
    public $dbConnect = null;

    public function __construct()
    {
        parent::__construct();
        (string) $this->sql = null;
        (string) $this->error = null;
        (string) $this->warning = null;
        (string) $this->info = null;
        (int) $this->Affected_rows = 0;
        (array) $this->getConsultaRegistro = [];
        (array) $this->getConsultarRegistros = [];
        (int) $this->num_rows = 0;
        (boolean)$this->status = false;
        (string) $this->call_nemaProcedute = null;
        $this->mysqli = parent::getConnection();
        $this->dbConnect = $this->resultDB;
    }

    public function Reset_dbProcedureData()
    {
        $this->dbProcedureData = "";
    }

    public function append_dbProcedureData(string $data)
    {

        if (!empty($this->dbProcedureData)) {
            $this->dbProcedureData .= ",";
        }
        if (is_numeric($data) && !($data[0] == 0)) {
            $this->dbProcedureData .= $data;
        } else {
            $this->dbProcedureData .= "'" . $data . "'";
        }
    }

    public function append_dbProcedureResult($data) // cantura el resultado out de procedure
    {

        if (!empty($this->dbProcedureData)) {
            $this->dbProcedureData .= ",";
        }
        if (is_numeric($data)) {
            $this->dbProcedureData .= $data;
        } else {
            $this->dbProcedureData .= "  $data ";
        }
    }

    private function mysqli_warning_show($conect)
    {
        $resultwarning = "";
        if ($conect->warning_count) {
            if ($result = $conect->query("SHOW WARNINGS")) {
                $row = $result->fetch_row();
                $resultwarning = $row[0] . ': ' . $row[1] . ' ' . $row[2];
                $result->free();
            }
        }
        return $resultwarning;
    }

    public function set_consultaRegistro(string $sql = "")
    {
        try {
            $this->status = false;
            $this->getConsultaRegistro = [];
            $this->Affected_rows = 0;
            $this->sql = $sql;
            $mysqli = $this->mysqli;

            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 
            try {
                $result = $mysqli->query($sql); //ejecutamos la query
                $this->getConsultaRegistro = $result;
            } catch (\mysqli_sql_exception $e) {
                $this->error = 'No se pudo consultar:' . $e->getMessage();
                $this->info = $mysqli->info; // recupera informacion adicional sobre la última consulta ejecutrada OJO xD no funciona con CALL X'D seria chevere
                return false;
            }
            if (!$result) {
                $this->error = 'No se pudo consultar:' . $mysqli->error;
                $this->info = $mysqli->info; // recupera informacion adicional sobre la última consulta ejecutrada OJO xD no funciona con CALL X'D seria chevere
                return false;
            }
            $this->num_rows = $result->num_rows;
            $this->info = $mysqli->info; // recupera informacion adicional sobre la última consulta ejecutrada OJO xD no funciona con CALL X'D seria chevere
            $this->warning = $this->mysqli_warning_show($mysqli); // devuelve informacion sobre los errores de la query ejectuda

            if (empty($this->warning)) { // validamos que no tengamos warning
                $this->Affected_rows = $mysqli->affected_rows;
                if ($this->Affected_rows >= 0) { // validamos que no tengamos errores
                    $this->status = true;
                    $this->getConsultaRegistro = $result->fetch_row();
                }
            }
            $result->free();
        } catch (\PDOException $e) {
            $this->error = 'No se pudo consultar:' . $e->getMessage();
            $this->getConsultaRegistro = [];
        }
    }

    public function get_consultaRegistro(): array
    {
        $res = array(
            'status' => $this->status,
            'mysqli' => array(
                'error' => $this->error,
                'warning' => $this->warning,
                'info' => $this->info,
                'Affected_rows' => $this->Affected_rows,
                'num_rows' => $this->num_rows,
                'info' => $this->info
            ),
            'sql' => $this->sql,
            'result' => $this->getConsultaRegistro
        );

        $this->clearResult();
        return $res;
    }

    public function set_consultarRegistros(string $sql = "")
    {
        try {
            $this->status = false;
            $this->getConsultaRegistro = [];            
            $this->Affected_rows = 0;
            $this->sql = $sql;
            $mysqli = $this->mysqli;
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            try {
                $result = $mysqli->query($sql); //ejecutamos la query
            } catch (\mysqli_sql_exception $e) {
                $this->error = 'No se pudo consultar:' . $e->getMessage();
                $this->info = $mysqli->info; // recupera informacion adicional sobre la última consulta ejecutrada OJO xD no funciona con CALL X'D seria chevere
                return false;
            }

            if (!$result || empty($result)) {
                $this->error = 'No se pudo consultar:' . $mysqli->error;
                $this->info = $mysqli->info; // recupera informacion adicional sobre la última consulta ejecutrada OJO xD no funciona con CALL X'D seria chevere
                return false;
            }

            $this->info = $mysqli->info; // recupera informacion adicional sobre la última consulta ejecutada, OJO xD no funciona con CALL X'D seria chevere
            $this->warning = $this->mysqli_warning_show($mysqli); // devuelve informacion sobre los errores de la query ejectuda
            $this->num_rows = $result->num_rows;
            if (empty($this->warning)) { // validamos que no tengamos warning
                $this->Affected_rows = $mysqli->affected_rows;
                if ($this->Affected_rows >= 0) { // validamos que no tengamos errores
                    $this->status = true;

                    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
                        $this->getConsultaRegistro[] = $row;
                    }

                    mysqli_free_result($result);
                }
            }

        } catch (\PDOException  $e) {
            $this->error = 'No se pudo consultar:' . $e->getMessage();
            $this->getConsultaRegistro = [];
        }
    }

    public function get_consultarRegistros(): array
    {
        $res = array(
            'status' => $this->status,
            'mysqli' => array(
                'error' => $this->error,
                'warning' => $this->warning,
                'info' => $this->info,
                'Affected_rows' => $this->Affected_rows,
                'num_rows' => $this->num_rows,
                'info' => $this->info
            ),
            'sql' => $this->sql,
            'result' => $this->getConsultaRegistro
        );

        $this->clearResult();
        return $res;
    }

    public function ejecutar(string $sql = ""): array
    {
        try {
            $status = false;
            $error = "";
            $data = "";
            $sql = $sql;
            $Affected_rows = 0;
            $mysqli = $this->mysqli;

            if (!$mysqli) {
                $array = array('status' => false, 'result' => "conexion cerrada");
                return $array;
            }
            $result = $mysqli->query($sql); //ejecutamos la query
            if (!$result) {
                //printf("Error - SQLSTATE %s.\n", $mysqli->sqlstate);
                $error = 'No se pudo consultar:' . $mysqli->error . ' mysqli:' . $mysqli->info;
                $array = array('status' => false, 'result' => $error, 'sql' => $sql, 'mysqli_info' => $mysqli->info, "sqlstate" => $mysqli->sqlstate);
                return $array;
            }
            $info = $mysqli->info; // recupera informacion adicional sobre la última consulta ejecutrada OJO xD no funciona con CALL X'D seria chevere
            $warning = $this->mysqli_warning_show($mysqli); // devuelve informacion sobre los errores de la query ejectuda


            $Affected_rows = $mysqli->affected_rows;
            $insert_id = 0;

            if ($Affected_rows >= 0) { // validamos que no tengamos errores
                $status = true;
                $insert_id = $mysqli->insert_id;
            }


            $array = array(
                'status' => $status,
                'result' => $data,
                'mysqli' => array(
                    'error' => $error,
                    'warning' => $warning,
                    'info' => $info,
                    'Affected_rows' => $Affected_rows,
                    'num_rows' => $this->num_rows,
                    'insert_id' => $insert_id //secuencial generado en la tabla por el registro insertado
                ), 'Sql' => $sql
            );

            return $array;
        } catch (\PDOException $e) {
            $array = array('status' => false, 'result' => $e, 'sql' => $sql);
            return $array;
        }
    }

    public function get_call_nameProcedute(string $name = '')
    {
        $this->call_nemaProcedute = $name;
    }

    public function call_runProcedure(): array
    {
        try {
            $status = 0;
            $error = "";
            $resultProcedure = "";
            $Affected_rows = 0;
            /* prepare la consulta */
            $sql = "CALL " . $this->call_nemaProcedute  . "(" . $this->dbProcedureData . ");";
            //$call_store_procedure = ; //ejecutamos la query
            $mysqli = $this->mysqli;
            if (!$mysqli->query($sql)) {
                $error = 'No se pudo consultar:' . $mysqli->error;
                $array = array('status' => false, 'result' => $error, 'call' => $sql);
                return $array;
            }

            $info = $mysqli->info; // recupera informacion adicional sobre la última consulta ejecutrada OJO xD no funciona con CALL X'D seria chevere
            $warning = $this->mysqli_warning_show($mysqli); // devuelve informacion sobre los errores de la query ejectuda

            if (empty($warning)) { // validamos que no tengamos warning
                $Affected_rows = $mysqli->affected_rows;
                $result = $mysqli->query("SELECT @_result AS `_result`;"); //ejecutamos la query
                $data = $result->fetch_row();
                if (empty($data[0])) {
                    if ($Affected_rows >= 0) { // validamos que no tengamos errores
                        $status = 1;
                    }
                } else {
                    $resultProcedure = $data[0];
                }
            }

            $array = array(
                'status' => $status, 
                'result' => $resultProcedure,
                'mysqli' => array(
                    'error' => $error,
                    'warning' => $warning,
                    'info' => $info,
                    'Affected_rows' => $Affected_rows,
                    'num_rows' => $this->num_rows,
                ), 'Sql' => $sql
            );
            return $array;
        } catch (Throwable  $e) {
            $array = array('status' => false, 'result' => $e, 'call' => $sql);
            return $array;
        }
    }

    public function call_storeProcedure(): array
    {

        try {
            $status = 0;
            $valores = NULL;
            $error = "";
            $Affected_rows = 0;
            $sql = "CALL " . $this->call_nemaProcedute . "(" . $this->dbProcedureData . ");";

            $mysqli = $this->mysqli;
            $call_store_procedure = $mysqli->query($sql); //ejecutamos la query
            $this->num_rows = $call_store_procedure->num_rows;
            $Affected_rows = $mysqli->affected_rows; // recueper el numero de filas afectada, si -1 = Error
            $info = $mysqli->info; // recupera informacion adicional sobre la última consulta ejecutrada OJO xD no funciona con CALL X'D seria chevere
            $warning = $this->mysqli_warning_show($mysqli); // devuelve informacion sobre los errores de la query ejectuda

            if (!$call_store_procedure) { // validamos si la consulta se ejecuto sin errores
                $error = 'No se pudo consultar:' . $mysqli->error;
                $array = array('status' => false, 'result' => $error, 'call' => $sql);
                return $array;
            } else {
                $status = 1;
                $valores = $call_store_procedure->fetch_all(MYSQLI_NUM);
            }
            //parent::connectClose($mysqli);
            $array = array(
                'status' => $status, 'result' => $valores, 'mysqli' => array(
                    'error' => $error,
                    'warning' => $warning,
                    'info' => $info,
                    'Affected_rows' => $Affected_rows,
                    'num_rows' => $this->num_rows
                ), 'Sql' => $sql
            );

            return $array;
        } catch (Throwable  $e) {
            $array = array('status' => false, 'result' => $e);
            return $array;
        }
    }


    function call_storeProcedure_old($name = '')
    {
        $link = $this->mysqli;
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }

        $sql = "CALL " . $name . "(" . $this->dbProcedureData . ");";
        $call_store_procedure = mysqli_query($link, $sql);
        $valores = NULL;
        $valores = mysqli_fetch_array($call_store_procedure, MYSQLI_ASSOC);
        mysqli_close($link);
        return $valores;
    }

    public function clearResult()
    {
        $this->getConsultaRegistro = [];
        $this->getConsultarRegistros = [];
    }

    public function closeConnection(): void
    {
        parent::connectClose($this->mysqli);
    }
}
