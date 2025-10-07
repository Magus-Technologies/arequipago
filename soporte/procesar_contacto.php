<?php
header('Content-Type: application/json');

// Cambiar al directorio raíz del proyecto para que las rutas relativas funcionen
chdir(__DIR__ . '/..');

// Incluir la clase de envío de email y configuración
require_once __DIR__ . '/../utils/config.php';
require_once __DIR__ . '/../app/clases/EnvioEmail.php';

// Correo de destino
$email_destino = 'soporte@arequipago-ventas.pe';

// Validar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Capturar datos del formulario
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$apellido = isset($_POST['apellido']) ? trim($_POST['apellido']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
$tipo_consulta = isset($_POST['tipo_consulta']) ? trim($_POST['tipo_consulta']) : '';
$asunto = isset($_POST['asunto']) ? trim($_POST['asunto']) : '';
$mensaje = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : '';

// Validar campos requeridos
if (empty($nombre) || empty($apellido) || empty($email) || empty($tipo_consulta) || empty($asunto) || empty($mensaje)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos requeridos deben estar llenos']);
    exit;
}

// Validar formato de email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'El correo electrónico no es válido']);
    exit;
}

// Mapear tipo de consulta a texto legible
$tipos_consulta = [
    'soporte_tecnico' => '🔧 Soporte Técnico',
    'consulta_general' => '💬 Consulta General',
    'problema_app' => '📱 Problema con la App',
    'sugerencia' => '💡 Sugerencia',
    'facturacion' => '💰 Facturación',
    'otro' => '📋 Otro'
];

$tipo_consulta_texto = isset($tipos_consulta[$tipo_consulta]) ? $tipos_consulta[$tipo_consulta] : $tipo_consulta;

// Preparar el asunto del correo
$email_subject = "🆕 Nueva Consulta de Soporte: " . $asunto;

// Preparar el cuerpo del correo en HTML
$email_body = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f7fa;
            padding: 20px;
            line-height: 1.6;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .header {
            background: linear-gradient(135deg, #2E217A 0%, #1a0e4d 100%);
            padding: 35px 30px;
            text-align: center;
        }
        .header-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .header h1 {
            color: #f4f750;
            font-size: 26px;
            font-weight: 700;
            margin: 0 0 8px 0;
        }
        .header p {
            color: rgba(255,255,255,0.9);
            font-size: 14px;
            margin: 0;
        }
        .content {
            padding: 35px 30px;
        }
        .info-row {
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid #e8e8e8;
        }
        .info-row:last-of-type {
            border-bottom: none;
            padding-bottom: 0;
        }
        .info-label {
            font-size: 11px;
            font-weight: 600;
            color: #2E217A;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .info-value {
            font-size: 15px;
            color: #333333;
            font-weight: 500;
        }
        .info-value a {
            color: #2E217A;
            text-decoration: none;
        }
        .badge {
            display: inline-block;
            background: #f4f750;
            color: #2E217A;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
        }
        .message-box {
            background: #f8f9fa;
            border-left: 4px solid #2E217A;
            padding: 20px;
            border-radius: 8px;
            margin-top: 10px;
        }
        .message-text {
            color: #333;
            font-size: 14px;
            line-height: 1.7;
            white-space: pre-wrap;
        }
        .date-box {
            background: #e8f5e9;
            border-left: 4px solid #4CAF50;
            padding: 16px;
            border-radius: 8px;
            margin-top: 25px;
        }
        .date-box strong {
            color: #2E8B57;
            font-size: 13px;
        }
        .footer {
            background: #f8f9fa;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e8e8e8;
        }
        .footer-title {
            color: #2E217A;
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .footer-text {
            color: #666666;
            font-size: 12px;
            margin: 4px 0;
        }
        .footer-email {
            color: #2E217A;
            font-weight: 600;
        }
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            .content {
                padding: 25px 20px;
            }
            .header {
                padding: 25px 20px;
            }
            .footer {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <div class='email-container'>
        <div class='header'>
            <div class='header-icon'>📧</div>
            <h1>Nueva Consulta de Soporte</h1>
            <p>ArequipaGo - Sistema de Soporte</p>
        </div>

        <div class='content'>
            <div class='info-row'>
                <div class='info-label'>👤 NOMBRE COMPLETO</div>
                <div class='info-value'>" . htmlspecialchars($nombre . ' ' . $apellido) . "</div>
            </div>

            <div class='info-row'>
                <div class='info-label'>📧 CORREO ELECTRÓNICO</div>
                <div class='info-value'>
                    <a href='mailto:" . htmlspecialchars($email) . "'>" . htmlspecialchars($email) . "</a>
                </div>
            </div>

            " . (!empty($telefono) ? "
            <div class='info-row'>
                <div class='info-label'>📞 TELÉFONO</div>
                <div class='info-value'>" . htmlspecialchars($telefono) . "</div>
            </div>
            " : "") . "

            <div class='info-row'>
                <div class='info-label'>📋 TIPO DE CONSULTA</div>
                <div class='info-value'>
                    <span class='badge'>{$tipo_consulta_texto}</span>
                </div>
            </div>

            <div class='info-row'>
                <div class='info-label'>📌 ASUNTO</div>
                <div class='info-value'>" . htmlspecialchars($asunto) . "</div>
            </div>

            <div class='info-row' style='border-bottom: none;'>
                <div class='info-label'>💬 MENSAJE</div>
                <div class='message-box'>
                    <div class='message-text'>" . nl2br(htmlspecialchars($mensaje)) . "</div>
                </div>
            </div>

            <div class='date-box'>
                <strong>⏰ Fecha y Hora:</strong> " . date('d/m/Y H:i:s') . "
            </div>
        </div>

        <div class='footer'>
            <div class='footer-title'>ArequipaGo</div>
            <p class='footer-text'>Sistema de Gestión y Soporte</p>
            <p class='footer-text'>Este correo fue generado automáticamente desde el formulario de contacto.</p>
            <p class='footer-text'>Para responder, envía un correo a: <span class='footer-email'>" . htmlspecialchars($email) . "</span></p>
        </div>
    </div>
</body>
</html>
";

// Intentar enviar el correo usando la clase EnvioEmail
try {
    $envioEmail = new EnvioEmail();

    $resultado = $envioEmail
        ->de(USER_SMTP, 'Soporte ArequipaGo')
        ->addEmail($email_destino)
        ->addReplicarA($email, $nombre . ' ' . $apellido)
        ->setasunto($email_subject)
        ->esHTML(true)
        ->cuerpo($email_body)
        ->enviar();

    if ($resultado) {
        echo json_encode([
            'success' => true,
            'message' => 'Mensaje enviado exitosamente. Te responderemos pronto.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al enviar el correo: ' . $envioEmail->error()
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
    ]);
}
?>
