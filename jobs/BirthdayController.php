<?php
require_once __DIR__ . "/../config/Conexion.php"; // Cambio: Se requiere la conexión directamente
require_once __DIR__ . "/../utils/config.php"; //
require_once __DIR__ . '/../utils/lib/mailer/vendor/phpmailer/phpmailer/src/Exception.php';
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

        echo "<h1>Iniciando el proceso de felicitaciones...</h1><br>";

        $cumpleaneros = $this->obtenerCumpleaniosHoy();

        if (empty($cumpleaneros)) {
            echo "No hay conductores que cumplan años hoy.";
            return;
        }

        echo "<p>Se encontraron " . count($cumpleaneros) . " conductores con cumpleaños hoy.</p><br>";

        foreach ($cumpleaneros as $conductor) {
            echo "<p>Enviando correo a " . $conductor['nombres'] . "...</p><br>";
            $this->enviarCorreo($conductor);

            echo "<p>Proceso de envío completado.</p><br>";

        }
    }

    private function obtenerCumpleaniosHoy()
    {
        date_default_timezone_set('America/Lima'); // Cambio: Se establece zona horaria
        $hoy = date('m-d'); // Obtiene el mes y día actual en formato MM-DD

        echo "<p>Buscando cumpleaños para la fecha: " . $hoy . "</p><br>";

        $sql = "SELECT id_conductor, nombres, apellido_paterno, apellido_materno, correo, foto 
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
            $mail->SMTPDebug = 3; // Nivel de depuración: 2 o 3 para más detalles
            $mail->Debugoutput = 'html'; // Formato de salida de la depuración
            $mail->isSMTP();
           $mail->Host       = 'smtp.zoho.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'envios@magustechnologies.com';
            $mail->Password   = 'C4p1cu4$$211088';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;
            
            echo "<p>Configuración SMTP establecida correctamente.</p><br>";

            // Remitente
            $mail->setFrom('envios@magustechnologies.com', 'Feliz Cumpleaños');

            echo "<p>Configuración SMTP establecida correctamente.</p><br>";

            // Destinatario
            $mail->addAddress($conductor['correo'], $conductor['nombres']);
            echo "<p>Destinatario añadido: " . $conductor['correo'] . "</p><br>";
            
            $mail->addBCC('arequipaago@gmail.com');

            $mail->isHTML(true);  // Asegura que el contenido sea HTML
            $mail->CharSet = 'UTF-8';
            
            // Ruta de las imágenes
            $rutaImagen = __DIR__ . '/../public/img/Cumpleaños_Mesa de trabajo 1.png'; // Banner por defecto
            $rutaFoto = (isset($conductor['foto']) && !empty($conductor['foto'])) ? __DIR__ . '/../public/' . $conductor['foto'] : null; // Si existe, usarla; si no, null

            if (!file_exists($rutaFoto)) {
                // Si no existe la foto del conductor
                $mail->Subject = '🎉 ¡Feliz Cumpleaños, ' . $conductor['nombres'] . '! 🎂';
                $mail->Body = '<div style="text-align: center;">
                                <img src="cid:CumpleBanner" alt="Feliz Cumpleaños" style="max-width: 100%; height: auto;">
                                <h2>Hola, ' . $conductor['nombres'] . '!</h2>
                                <p>Hoy es un día muy especial y queremos celebrarlo contigo. 🎉</p>
                                <p>Desde <strong>ArequipaGo</strong>, te enviamos nuestros mejores deseos en tu cumpleaños. Que este nuevo año de vida esté lleno de alegría, éxito y muchas bendiciones.</p>
                                <p>🎂 ¡Feliz cumpleaños! 🥳</p>
                                <p>Saludos cordiales,<br><strong>El equipo de ArequipaGo</strong></p>
                            </div>';
                $mail->addEmbeddedImage($rutaImagen, 'CumpleBanner'); // Agregar banner de cumpleaños
            }
            else
            {
                $rutaImagen = __DIR__ . '/../public/img/Cumpleaños 1x1_Mesa de trabajo 1.png'; // Ruta del banner
                $rutaFoto = __DIR__ . '/../public/' . $conductor['foto']; // Ruta de la foto del conductor

                if (!isset($conductor) || !isset($rutaFoto)) {
                    // Verificación de error si no se encuentra el conductor o la foto
                    error_log("Error: \$conductor o \$rutaFoto no está definido.");
                    $mail->Subject = 'Error enviando el correo de cumpleaños';
                    $mail->Body = 'Hubo un error procesando tu solicitud.';
                    return; // O lanzar una excepción
                }

                // Configuración del asunto del correo
                $mail->Subject = '🎉 ¡Feliz Cumpleaños, ' . $conductor['nombres'] . '! 🎂';
                $mail->isHTML(true); // Asegura que el contenido sea en formato HTML

                // Adjuntar el banner de cumpleaños
                $mail->AddEmbeddedImage($rutaImagen, 'CumpleBanner', 'banner.jpg'); // Agregar el banner

                // Adjuntar la foto del conductor
                $mail->AddEmbeddedImage($rutaFoto, 'ConductorFoto', 'conductor.jpg'); // Agregar la foto del conductor

                // Configuración del asunto del correo
                $mail->Subject = '🎉 ¡Feliz Cumpleaños, ' . $conductor['nombres'] . '! 🎂';
                $mail->isHTML(true); // Asegura que el contenido sea en formato HTML
            
                // Adjuntar el banner de cumpleaños
                $mail->AddEmbeddedImage($rutaImagen, 'CumpleBanner', 'banner.jpg'); // Agregar el banner
            
                // Adjuntar la foto del conductor
                $mail->AddEmbeddedImage($rutaFoto, 'ConductorFoto', 'conductor.jpg'); // Agregar la foto del conductor
            
                // Cuerpo del correo
                $mail->Body = '
                <div style="text-align: center; padding: 20px; position: relative; background-image: url(cid:ConductorFoto); background-size: 42%; background-position: 50% 10%; background-repeat: no-repeat; padding-bottom: 100px;"> <!-- Foto del conductor como fondo más grande y más arriba -->
            
                    <!-- Banner en la parte superior del correo -->
                    <img src="cid:CumpleBanner" alt="Feliz Cumpleaños" style="width: 100%; height: auto;"> <!-- Banner arriba del texto -->
            
                    <!-- Texto del correo -->
                    <h2>Hola, ' . $conductor['nombres'] . '!</h2>
                    <p>Hoy es un día muy especial y queremos celebrarlo contigo. 🎉</p>
                    <p>Desde <strong>ArequipaGo</strong>, te enviamos nuestros mejores deseos en tu cumpleaños. Que este nuevo año de vida esté lleno de alegría, éxito y muchas bendiciones.</p>
                    <p>🎂 ¡Feliz cumpleaños! 🥳</p>
                    <p>Saludos cordiales,<br><strong>El equipo de ArequipaGo</strong></p>
                </div>';

                // Enviar el correo
                $mail->send();
               
            }
          

            // Enviar el correo
            $mail->send();
          
        } catch (Exception $e) {
           echo "<p style='color:red;'>Excepción atrapada al enviar correo a " . $conductor['correo'] . ": " . $e->getMessage() . "</p><br>";
        
        }
    }
   
}

// Ejecutar directamente desde el navegador
$controller = new BirthdayController();
$controller->enviarFelicitaciones();
?>
