<?php
/**
 * Probar diferentes variaciones del endpoint
 */

header('Content-Type: text/html; charset=utf-8');

$base = 'https://magusemail.com/arequipago-api';

// Posibles endpoints
$endpoints = [
    '/upload-profile-picture',
    '/upload-profile-picture/',
    '/upload-photo',
    '/upload-photo/',
    '/profile-picture',
    '/profile-picture/',
    '/foto-perfil',
    '/foto-perfil/',
    '/upload',
    '/upload/',
    '/cliente/foto',
    '/cliente/foto/',
    '/api/upload-profile-picture',
    '/api/upload-photo',
    '/', // Raíz del API
];

echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscar Endpoints</title>
    <style>
        body { font-family: Arial; max-width: 1000px; margin: 30px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #4CAF50; color: white; padding: 12px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        .code-200 { color: #28a745; font-weight: bold; }
        .code-301, .code-302 { color: #ffc107; font-weight: bold; }
        .code-404 { color: #dc3545; }
        .code-405 { color: #17a2b8; font-weight: bold; }
        .code-401, .code-403 { color: #fd7e14; font-weight: bold; }
        .found { background: #d4edda; }
        .redirect { background: #fff3cd; }
        .method-error { background: #d1ecf1; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Buscar Endpoints Disponibles</h1>
        <p><strong>Base URL:</strong> ' . $base . '</p>
        
        <table>
            <thead>
                <tr>
                    <th>Endpoint</th>
                    <th>Código HTTP</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>';

foreach ($endpoints as $endpoint) {
    $url = $base . $endpoint;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    $errno = curl_errno($ch);
    curl_close($ch);
    
    // Pequeña pausa para no saturar el servidor
    usleep(200000); // 0.2 segundos
    
    $class = '';
    $status = '';
    
    if ($errno) {
        $status = '⚠️ Error: ' . $error;
    } elseif ($http_code == 200) {
        $class = 'found';
        $status = '✅ Encontrado';
    } elseif ($http_code == 301 || $http_code == 302) {
        $class = 'redirect';
        $status = '↪️ Redirect';
    } elseif ($http_code == 405) {
        $class = 'method-error';
        $status = '⚠️ Método no permitido (existe pero no acepta GET)';
    } elseif ($http_code == 401 || $http_code == 403) {
        $class = 'found';
        $status = '🔐 Requiere autenticación (existe)';
    } elseif ($http_code == 404) {
        $status = '❌ No encontrado';
    } elseif ($http_code == 0) {
        $status = '⚠️ Sin respuesta (timeout o bloqueado)';
    } else {
        $status = '❓ Código ' . $http_code;
    }
    
    $code_class = 'code-' . $http_code;
    
    echo '<tr class="' . $class . '">';
    echo '<td><code>' . htmlspecialchars($endpoint) . '</code></td>';
    echo '<td class="' . $code_class . '">' . $http_code . '</td>';
    echo '<td>' . $status . '</td>';
    echo '</tr>';
}

echo '</tbody>
        </table>
        
        <div style="margin-top: 30px; padding: 20px; background: #e7f3ff; border-radius: 5px; border-left: 4px solid #2196F3;">
            <h3>💡 Interpretación de Códigos</h3>
            <ul>
                <li><strong class="code-200">200:</strong> Endpoint existe y responde</li>
                <li><strong class="code-405">405:</strong> Endpoint existe pero no acepta GET (probablemente necesita POST)</li>
                <li><strong class="code-401">401/403:</strong> Endpoint existe pero requiere autenticación</li>
                <li><strong class="code-301">301/302:</strong> Redirect a otra URL</li>
                <li><strong class="code-404">404:</strong> No existe</li>
            </ul>
        </div>
        
        <div style="margin-top: 20px; padding: 20px; background: #fff3cd; border-radius: 5px; border-left: 4px solid #ffc107;">
            <h3>📞 Qué hacer ahora</h3>
            <p>Si encuentras un endpoint con código <strong>405</strong>, ese es el correcto (existe pero necesita POST en lugar de GET).</p>
            <p>Si todos dan <strong>404</strong>, el desarrollador móvil necesita:</p>
            <ol>
                <li>Desplegar el API en el servidor</li>
                <li>Darte la URL correcta</li>
                <li>Proporcionarte documentación del endpoint</li>
            </ol>
        </div>
    </div>
</body>
</html>';
?>
