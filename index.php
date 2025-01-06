<?php
use app\Core;
// Configuraciones de seguridad para las cookies de sesión.
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
// Iniciar y regenerar la sesión.
session_start();
session_regenerate_id(true);
// Incluir el archivo principal del núcleo de la aplicación.
require_once 'app/Core.php';
// Inicializar la aplicación.
Core::init();
