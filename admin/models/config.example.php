<?php
    session_start();
    define('DB_DRIVER', 'mysql');
    define('DB_HOST', getenv('DB_HOST') ?: 'mariadb');
    define('DB_USER', getenv('DB_USER') ?: 'user');
    define('DB_PASS', getenv('DB_PASS') ?: 'CAMBIAR_ESTA_CONTRASENA');
    define('DB_NAME', getenv('DB_NAME') ?: 'xtreme');
    define('DB_PORT', getenv('DB_PORT') ?: '3307');
    define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
    define('SMTP_PORT', getenv('SMTP_PORT') ?: 465);
    define('SMTP_USER', getenv('SMTP_USER') ?: '');
    define('SMTP_PASS', getenv('SMTP_PASS') ?: '');
?>
