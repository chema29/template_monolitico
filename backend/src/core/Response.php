<?php
namespace Jp\Backend\core;
// class Response se encarga de manejar los errores de la aplicación
class Response {

    public $status;
    public $codeError;
    public $messageError;

    public function __construct() {
        $this->status = true;
        $this->codeError = 200;
        $this->messageError = "OK";
    }

    public function send($data) {
        // header utf-8
        header('Content-Type: text/html; charset=utf-8');
        // header codigo de respuesta
        http_response_code($this->codeError);   
        header('Content-Type: application/json');
        echo json_encode($data);
        
    }

    public function error($code, $message, $data = [], $status = false) {
        $this->codeError = $code;
        $this->messageError = $message;
        $this->status = $status;
        $this->send([
                "code" => $this->codeError,
                "message" => $this->messageError,
                "status" => $this->status,
                "data" => $data
        ]);
    }

    public function success($code, $message, $data = []) {
        $this->codeError = $code;
        $this->messageError = $message;
        $this->send([
                "code" => $this->codeError,
                "message" => $this->messageError,
                "status" => $this->status,
                "data" => $data
        ]);
    }

    public function status($status) {
        $this->status = $status;
    }

    public function getCodeError() {
        return $this->codeError;
    }

    public function getMessageError() {
        return $this->messageError;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getCodeMessageStatus() {
        return [
            "code" => $this->codeError,
            "message" => $this->messageError,
            "status" => $this->status
        ];
    }
}