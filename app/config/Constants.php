<?php
namespace app\config;

final class Constants {
    // General.
    public const SITE_NAME = 'GamesPortal';
    public const SITE_ENCODING = 'utf-8';
    public const SITE_LANG = 'es_ES';
    public const SITE_TEMPLATE = 'retro';
    public const SITE_ADMIN_TEMPLATE = 'admin';
    public const SITE_EMAIL = 'email@localhost';
    public const SITE_ENGINE = 'GamesPortal';
    public const SITE_COPYRIGHT_HTML = 'Copyright © 2025&nbsp;<b>' . self::SITE_NAME . '</b>.&nbsp;Powered by&nbsp;<a class="link-blue" href="#" target="_blank"><b>' . self::SITE_ENGINE . '</b></a>';
    
    // Base de datos.
    public const DB_HOST = 'localhost';
    public const DB_NAME = 'juegos404';
    public const DB_USER = 'root';
    public const DB_PASS = '';

    // Envío de correos electrónicos.
    public const SMTP = 'smtp.yourserver.com';
    public const SMTP_PORT = 25;
    
    // Usuarios.
    public const DEFAULT_INBOX_SIZE = 20;
    
    // Paginación.
    public const ADMIN_ELEMENTS_PER_PAGE = 50;
    public const ELEMENTS_PER_PAGE = 50;
    
    // Seguridad.
    public const ATTEMPT_LIMIT = 3;
    public const ATTEMPT_LIMIT_MINUTES = 15;
    public const UNKNOWN_IP = '0.0.0.0';
    public const UNKNOWN_BROWSER = 'Unknown';
    public const ENTRY_MIN_TIME_TO_POST_IN_SECONDS = 60;
    public const FORUM_MIN_TIME_TO_POST_IN_SECONDS = 60;
    public const COOKIE_EXPIRATION_TIME_IN_DAYS = 30;
    
    // Nombre de las cookies.
    public const COOKIE_ACCEPT = '81ghtk7vazdd51uw';
    
    // Nombre de las sesiones.
    public const SESSION_CAPTCHA = 'kubq81fm9e25kvcz';
    public const SESSION_USERNAME = 'b1hj0zeqylm7tau6';
    public const SESSION_ERROR_MESSAGES = 'mfgswi5qan433dpo';
    public const SESSION_SUCCESS_MESSAGES = '8t5hvnn29lbrm68j';
    public const SESSION_WARNING_MESSAGES = 'atgk9vg1qpms26fx';
    public const SESSION_FORMDATA = 'vwk6914k7zbrl8ys';
    
    // Extensiones de los ficheros subidos.
    public const ENTRY_FEATURED_TYPE = 'gif';
    public const ENTRY_THUMBNAIL_TYPE = 'gif';
    
    // Dimensiones de las imágenes.
    public const IMG_MEMBER_AVATAR_HEIGHT = 200;
    public const IMG_MEMBER_AVATAR_WIDTH = 200;

    // Configuración de errores.
    public const DISPLAY_ERRORS = 0;
    public const LOG_ERRORS = 1;
    public const ERROR_REPORTING = \E_ALL;
}
