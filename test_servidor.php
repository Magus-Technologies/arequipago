<?php
/**
 * Verificar si el servidor API está activo
 */

header('Content-Type: text/html; charset=utf-8');

$base_url = 'https://magusemail.com';
$api_path = '/arequipago-api';

echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificar Servidor API</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 30px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
        .test { margin: 20px 0; padding: 15px; border-radius: 5px; border-left: 4px solid #4CAF50; background: #f9f9f9; }
        .success { border-left-color: #4CAF50; background: #d4edda; }
        .error { border-left-color: #dc3545; background: #f8d7da; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Verificar Servidor API</h1>';

// Test 1: Servidor base
echo '<div class="test">';
echo '<h3>Test 1: Servidor Base</h3>';
echo '<strong>URL:</strong> ' . $base_url . '<br><br>';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo '<strong>❌ Error:</strong> ' . $error;
    echo '</div>';
} else {
    echo '<strong>✅ Código HTTP:</strong> ' . $http_code . '<br>';
    echo '<strong>Servidor:</strong> Activo';
    echo '</div>';
}

// Test 2: Ruta del API
echo '<div class="test">';
echo '<h3>Test 2: Ruta del API</h3>';
echo '<strong>URL:</strong> ' . $base_url . $api_path . '<br><br>';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . $api_path);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo '<strong>❌ Error:</strong> ' . $error;
} else {
    echo '<strong>Código HTTP:</strong> ' . $http_code . '<br><br>';
    if ($http_code == 404) {
        echo '<strong>❌ No encontrado</strong> - La ruta /arequipago-api no existe<br>';
    } elseif ($http_code == 200) {
        echo '<strong>✅ Encontrado</strong><br>';
    }
    echo '<br><strong>Respuesta (primeros 500 caracteres):</strong><br>';
    echo '<pre>' . htmlspecialchars(substr($response, 0, 500)) . '</pre>';
}
echo '</div>';

// Test 3: Endpoint específico
echo '<div class="test">';
echo '<h3>Test 3: Endpoint de Subida</h3>';
echo '<strong>URL:</strong> ' . $base_url . $api_path . '/upload-profile-picture<br><br>';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . $api_path . '/upload-profile-picture');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo '<strong>❌ Error:</strong> ' . $error;
} else {
    echo '<strong>Código HTTP:</strong> ' . $http_code . '<br><br>';
    if ($http_code == 404) {
        echo '<strong>❌ No encontrado</strong> - El endpoint no existe<br>';
    } elseif ($http_code == 405) {
        echo '<strong>⚠️ Método no permitido</strong> - El endpoint existe pero no acepta GET<br>';
    } elseif ($http_code == 401 || $http_code == 403) {
        echo '<strong>🔐 Requiere autenticación</strong><br>';
    } elseif ($http_code == 200) {
        echo '<strong>✅ Encontrado</strong><br>';
    }
    echo '<br><strong>Respuesta:</strong><br>';
    echo '<pre>' . htmlspecialchars(substr($response, 0, 500)) . '</pre>';
}
echo '</div>';

echo '<div style="margin-top: 30px; padding: 20px; background: #fff3cd; border-radius: 5px; border-left: 4px solid #ffc107;">';
echo '<h3>📝 Conclusión</h3>';
echo '<p>Contacta al desarrollador móvil y pídele:</p>';
echo '<ul>';
echo '<li>La URL completa y correcta del API</li>';
echo '<li>Confirmación de que el endpoint está desplegado</li>';
echo '<li>Documentación del API (método, parámetros, autenticación)</li>';
echo '<li>Un ejemplo de petición exitosa (con cURL o Postman)</li>';
echo '</ul>';
echo '</div>';

echo '</div></body></html>';
?>
