<?php
/* Almacenara los datos del usuario para tenerlos disponibles en el model */
namespace Jp\Backend\core;

class User {
    private $id;
    private $usuario;
    private $nombre;
    private $intentos;
    private $token;
    private $timeMaxToken;
    private $refreshToken;
    private $expirationTimeRefreshToken;

    public function __construct() {
        $this->id = 0;
        $this->usuario = "";
        $this->nombre = "";
        $this->intentos = 0;
        $this->token = "";
        $this->timeMaxToken = "";
        $this->refreshToken = "";
        $this->expirationTimeRefreshToken = "";
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    public function getUsuario() {
        return $this->usuario;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setIntentos($intentos) {
        $this->intentos = $intentos;
    }

    public function getIntentos() {
        return $this->intentos;
    }

    public function setToken($token) {
        $this->token = $token;
    }

    public function getToken() {
        return $this->token;
    }

    public function setTimeMaxToken($timeMaxToken) {
        $this->timeMaxToken = $timeMaxToken;
    }

    public function getTimeMaxToken() {
        return $this->timeMaxToken;
    }

    public function setRefreshToken($refreshToken) {
        $this->refreshToken = $refreshToken;
    }

    public function getRefreshToken() {
        return $this->refreshToken;
    }

    public function setExpirationTimeRefreshToken($expirationTimeRefreshToken) {
        $this->expirationTimeRefreshToken = $expirationTimeRefreshToken;
    }

    public function getExpirationTimeRefreshToken() {
        return $this->expirationTimeRefreshToken;
    }

    public function generateToken() {
        $header = [
            "typ" => "JWT",
            "alg" => "HS256"
        ];
        $header = json_encode($header);
        $header = base64_encode($header);
        $payload = [
            "iat" => time(),
            "exp" => time() + (60 * 60),
            "user" => $this->getUsuario(),
            'iduser' => $this->getId(),
        ];
        $payload = json_encode($payload);
        $payload = base64_encode($payload);
        $signature = hash_hmac("sha256", "$header.$payload", "secret", true);
        $signature = base64_encode($signature);
        $token = "$header.$payload.$signature";
        return $token;
    }
}