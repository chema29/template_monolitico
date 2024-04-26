<?php
// descripcion de la libreria backend
/* esta es la base para comenzar a desarrollar el backend */

// descripcion de la libreria backend

// namespace
namespace Jp\Backend\controllers;

// class backend
class HomeController
{
    // version
    public static $version = "1.0.0";
    // descripcion
    public static $description = "Backend para el desarrollo de aplicaciones web";
    // autor
    public static $author = "Jp";
    // email
    public static $email = "depots@e-pagos.services";

    // constructor
    public function __construct()
    {
        echo "<p>Backend</p>";

        // mostrar la version
        echo "<p>Version: " . self::$version . "</p>";
        // mostrar la descripcion
        echo "<p>Descripcion: " . self::$description . "</p>";
        // mostrar el autor
        echo "<p>Autor: " . self::$author . "</p>";
        // mostrar el email
        echo "<p>Email: " . self::$email . "</p>";

    }

    // index
    public function index()
    {
        echo "<p>Index</p>";
    }

    // metodo para obtener la version
    public static function getVersion()
    {
        return self::$version;
    }

    // metodo para obtener la descripcion
    public static function getDescription()
    {
        return self::$description;
    }

    // metodo para obtener el autor
    public static function getAuthor()
    {
        return self::$author;
    }

    // metodo para obtener el email
    public static function getEmail()
    {
        return self::$email;
    }
}

// fin del archivo
