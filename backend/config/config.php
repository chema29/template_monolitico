<?php
// definir constantes de la aplicación db
define("DB_HOST", "localhost");
define("DB_NAME", "test");
define("DB_USER", "root");
define("DB_PASS", "");

// definir urls de la aplicación
define("URL_BACKEND", "http://localhost/template/template_monolitico/backend/");
// definir un array de ip permitidas para que se conecten al backend
define("allowedOrigin", ["http://localhost/" /* la ip de tu frontend */]);

// definir confirguraciones del cors 
define("CORS", false);

// definir en que ambiente se encuentra la aplicación
define("PRODUCTION", false);

// este es el codigo secreto para generar el tokens
define("SECRET_KEY","tu_secret");