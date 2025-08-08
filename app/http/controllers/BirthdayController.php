<?php
require_once __DIR__ . "/../config/Conexion.php"; // Cambio: Se requiere la conexión directamente
require_once __DIR__ . "/../utils/config.php"; //
require_once 'C:/xampp/htdocs/arequipago/utils/lib/mailer/vendor/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/../utils/lib/mailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../utils/lib/mailer/vendor/phpmailer/phpmailer/src/SMTP.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class BirthdayController 
{

    private $conexion;


    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }

    public function enviarFelicitaciones()
    {
        $cumpleaneros = $this->obtenerCumpleaniosHoy();

        if (empty($cumpleaneros)) {
            echo "No hay conductores que cumplan años hoy.";
            return;
        }

        foreach ($cumpleaneros as $conductor) {
            $this->enviarCorreo($conductor);
        }
    }

    private function obtenerCumpleaniosHoy()
    {
        date_default_timezone_set('America/Lima'); // Cambio: Se establece zona horaria
        $hoy = date('m-d'); // Obtiene el mes y día actual en formato MM-DD

        $sql = "SELECT id_conductor, nombres, apellido_paterno, apellido_materno, correo 
                FROM conductores 
                WHERE DATE_FORMAT(fech_nac, '%m-%d') = ? 
                AND correo IS NOT NULL AND correo != ''";

        $stmt = $this->conexion->prepare($sql); // Cambio: Se usa conexión directa
        $stmt->bind_param("s", $hoy);
        $stmt->execute();
        
        $resultado = $stmt->get_result();
        $cumpleaneros = [];
        
        while ($fila = $resultado->fetch_assoc()) {
            $cumpleaneros[] = $fila;
        }
        
        $stmt->close();
        return $cumpleaneros;
    }

    private function enviarCorreo($conductor)
    {
        $mail = new PHPMailer(true);

        try {
            // Configuración SMTP
            $mail->isSMTP();
            $mail->Host       = HOST_SMTP;
            $mail->SMTPAuth   = true;
            $mail->Username   = USER_SMTP;
            $mail->Password   = CLAVE_SMTP;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = PUERTO_SMTP;

            // Remitente
            $mail->setFrom(USER_SMTP, 'Feliz Cumpleaños');

            // Destinatario
            $mail->addAddress($conductor['correo'], $conductor['nombres']);

            // Adjuntar imagen de banner de cumpleaños
            $rutaImagen = 'public' . DIRECTORY_SEPARATOR . 'fotos' . DIRECTORY_SEPARATOR . 'Cumpleaños_Mesa de trabajo 1.png'; // Se usa DIRECTORY_SEPARATOR para evitar problemas de compatibilidad con las barras
            $mail->addAttachment($rutaImagen); // Adjuntar la imagen al correo

             // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = '🎉 ¡Feliz Cumpleaños, ' . $conductor['nombres'] . '! 🎂';
            
            // Cuerpo del correo con imagen y mensaje personalizado
            $mail->Body = '<div style="text-align: center;">
                            <img src="cid:CumpleBanner" alt="Feliz Cumpleaños" style="max-width: 100%; height: auto;"> <!-- Mostrar imagen en el correo -->
                            <h2>Hola, ' . $conductor['nombres'] . '!</h2>
                            <p>Hoy es un día muy especial y queremos celebrarlo contigo. 🎉</p>
                            <p>Desde <strong>ArequipaGo</strong>, te enviamos nuestros mejores deseos en tu cumpleaños. Que este nuevo año de vida esté lleno de alegría, éxito y muchas bendiciones.</p>
                            <p>🎂 ¡Feliz cumpleaños! 🥳</p>
                            <p>Saludos cordiales,<br><strong>El equipo de ArequipaGo</strong></p>
                        </div>';

            // Enviar el correo
            $mail->send();
            echo "Correo enviado a: " . $conductor['correo'] . "<br>";
        } catch (Exception $e) {
            echo "Error al enviar a " . $conductor['correo'] . ": {$mail->ErrorInfo}<br>";
        }
    }
    
}

if (php_sapi_name() === 'cli') { // Solo ejecuta si está en la terminal
    $controller = new BirthdayController();
    $controller->enviarFelicitaciones();
}
?>


