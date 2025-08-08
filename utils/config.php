<?php
// Configuraci�n para PRODUCCI�N - No usar valores locales

/**
 * DATABASE_CONFIG
 * IMPORTANTE: Usar los valores correctos del servidor de producci�n
 */
define("HOST_SS", "localhost");
define("DATABASE_SS", "magusqao_arequipa");      // ? Confirmado
define("USER_SS", "magusqao_maguado");           // ? Usuario creado
define("PASSWORD_SS", "magus72nK3XqL");          // ? Contrase�a definida

/**
 * EMAILS_CONFIG
 * Configuraci�n de correo para producci�n
 */
define("HOST_SMTP", "matrixsistem.com");
define("USER_SMTP", "informes@matrixsistem.com");
define("CLAVE_SMTP", "s(^&2_b5$2lp");
define("PUERTO_SMTP", "465");

/**
 * SERVER GEN XML SUNAT
 */
//define("ENDPOINT", "production"); // Cambiar a production para servidor real
//define("URL_GEN_XML_SUNAT", "http://genxml.production"); // URL de producci�n

define("KEY_ENCRYPT", "matrixsistem_key");

/**
 * SESSION_CONFIG
 */
define("INACTIVITY_TIMEOUT_SECONDS", 7200); // 2 horas para producci�n

// Funci�n para probar la conexi�n (opcional - remover despu�s de probar)
function testDatabaseConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . HOST_SS . ";dbname=" . DATABASE_SS,
            USER_SS,
            PASSWORD_SS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        echo "? Conexi�n exitosa a la base de datos\n";
        return true;
    } catch (PDOException $e) {
        echo "? Error de conexi�n: " . $e->getMessage() . "\n";
        return false;
    }
}

// Descomentar la siguiente l�nea para probar la conexi�n
// testDatabaseConnection();
?>