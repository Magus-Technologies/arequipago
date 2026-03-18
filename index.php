<?php
// Configuración de errores
// Reportar todos los errores EXCEPTO deprecated y strict
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

// Definir ruta del log
$logFile = __DIR__ . '/error_log.log';

// Para DESARROLLO (local): Mostrar errores en pantalla Y guardar en log
if ($_SERVER['SERVER_NAME'] === 'localhost' || strpos($_SERVER['SERVER_NAME'], '127.0.0.1') !== false || strpos($_SERVER['SERVER_NAME'], 'laragon') !== false) {
    ini_set('display_errors', '1');  // Mostrar en pantalla (desarrollo)
    ini_set('log_errors', '1');      // También guardar en log
    ini_set('error_log', $logFile);
} else {
    // Para PRODUCCIÓN (servidor): Solo guardar en log, NO mostrar en pantalla
    ini_set('display_errors', '0');  // NO mostrar en pantalla
    ini_set('log_errors', '1');      // SÍ guardar en log
    ini_set('error_log', $logFile);
}

require './src/launcher.php';
