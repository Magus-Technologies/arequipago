<?php
/**
 * Script de prueba para el API de subida de fotos
 * Ejecutar desde: http://localhost/arequipago/test_api_foto.php
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Test API Foto - PHP</title>
    <style>
        body { font-family: Arial; max-width: 900px; margin: 30px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { background: #4CAF50; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; width: 100%; }
        button:hover { background: #45a049; }
        .result { margin-top: 20px; padding: 15px; border-radius: 5px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto; white-space: pre-wrap; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test API - Subir Foto (PHP)</h1>
        
        <div class="info">
            <strong>📋 Información:</strong><br>
            Este script hace la petición desde el servidor PHP, evitando problemas de CORS.<br>
            <strong>URL API:</strong> https://magusemail.com/arequipago-api/public/api/upload-profile-picture<br>
            <strong>Validaciones:</strong> nro_documento (string, max 20), foto_perfil (jpeg/png/jpg, max 2MB)
        </div>

        <?php if (!isset($_POST['test'])): ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Número de Documento:</label>
                <input type="text" name="nro_documento" value="44111807" required>
            </div>
            
            <div class="form-group">
                <label>Foto de Perfil (JPG, PNG - Max 2MB):</label>
                <input type="file" name="foto_perfil" accept="image/jpeg,image/png,image/jpg" required>
            </div>
            
            <input type="hidden" name="test" value="1">
            <button type="submit">🚀 Probar API desde PHP</button>
        </form>
        
        <?php else: ?>
        
        <?php
        // Procesar la subida
        $nro_documento = $_POST['nro_documento'];
        $foto = $_FILES['foto_perfil'];
        
        // Validar
        if ($foto['error'] !== UPLOAD_ERR_OK) {
            echo '<div class="result error">';
            echo '<strong>❌ Error al subir archivo:</strong> ' . $foto['error'];
            echo '</div>';
        } elseif ($foto['size'] > 2 * 1024 * 1024) {
            echo '<div class="result error">';
            echo '<strong>❌ Error:</strong> La imagen debe ser menor a 2MB';
            echo '</div>';
        } else {
            // Hacer la petición al API
            $url = 'https://magusemail.com/arequipago-api/public/api/upload-profile-picture';
            
            $ch = curl_init();
            
            // Preparar datos
            $post_data = [
                'nro_documento' => $nro_documento,
                'foto_perfil' => new CURLFile($foto['tmp_name'], $foto['type'], $foto['name'])
            ];
            
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Para desarrollo
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            // Capturar información de la respuesta
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            $curl_errno = curl_errno($ch);
            curl_close($ch);
            
            echo '<div class="result ' . ($http_code == 200 ? 'success' : 'error') . '">';
            
            if ($curl_errno) {
                echo '<strong>❌ Error de cURL:</strong><br>';
                echo '<strong>Código:</strong> ' . $curl_errno . '<br>';
                echo '<strong>Mensaje:</strong> ' . $curl_error . '<br><br>';
                echo '<strong>Posibles causas:</strong><br>';
                echo '• El servidor no está disponible<br>';
                echo '• Problemas de red<br>';
                echo '• URL incorrecta<br>';
            } else {
                echo '<strong>Código HTTP:</strong> ' . $http_code . '<br><br>';
                
                if ($http_code == 200) {
                    echo '<strong>✅ ¡Éxito!</strong><br><br>';
                    echo '<strong>Respuesta del API:</strong><br>';
                    
                    // Intentar decodificar JSON
                    $json_response = json_decode($response, true);
                    if ($json_response) {
                        echo '<pre>' . json_encode($json_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</pre>';
                        
                        // Mostrar la URL de la foto si existe
                        if (isset($json_response['url'])) {
                            echo '<br><strong>URL de la foto:</strong><br>';
                            echo '<a href="' . $json_response['url'] . '" target="_blank">' . $json_response['url'] . '</a><br>';
                            echo '<br><img src="' . $json_response['url'] . '" style="max-width: 200px; border: 2px solid #4CAF50; border-radius: 5px;">';
                        } elseif (isset($json_response['foto_perfil'])) {
                            echo '<br><strong>Ruta de la foto:</strong><br>';
                            echo $json_response['foto_perfil'];
                        }
                    } else {
                        echo '<pre>' . htmlspecialchars($response) . '</pre>';
                    }
                } elseif ($http_code == 401 || $http_code == 403) {
                    echo '<strong>🔐 Requiere Autenticación</strong><br><br>';
                    echo 'El API requiere token o API key.<br><br>';
                    echo '<strong>Respuesta:</strong><br>';
                    echo '<pre>' . htmlspecialchars($response) . '</pre>';
                } elseif ($http_code == 404) {
                    echo '<strong>❌ Endpoint no encontrado</strong><br><br>';
                    echo 'La URL del API no existe o es incorrecta.<br><br>';
                    echo '<strong>Respuesta:</strong><br>';
                    echo '<pre>' . htmlspecialchars($response) . '</pre>';
                } elseif ($http_code == 422) {
                    echo '<strong>⚠️ Error de Validación</strong><br><br>';
                    echo 'Los datos enviados no cumplen con las validaciones.<br><br>';
                    echo '<strong>Respuesta:</strong><br>';
                    $json_response = json_decode($response, true);
                    if ($json_response) {
                        echo '<pre>' . json_encode($json_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</pre>';
                    } else {
                        echo '<pre>' . htmlspecialchars($response) . '</pre>';
                    }
                } else {
                    echo '<strong>❌ Error ' . $http_code . '</strong><br><br>';
                    echo '<strong>Respuesta:</strong><br>';
                    echo '<pre>' . htmlspecialchars($response) . '</pre>';
                }
            }
            
            echo '</div>';
        }
        ?>
        
        <br>
        <a href="test_api_foto.php" style="display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;">🔄 Probar de nuevo</a>
        
        <?php endif; ?>
    </div>
</body>
</html>
