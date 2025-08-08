<?php
// Importar las clases de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Ruta a los archivos de PHPMailer

require_once __DIR__ . '/../utils/lib/mailer/vendor/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/../utils/lib/mailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../utils/lib/mailer/vendor/phpmailer/phpmailer/src/SMTP.php';



    
    // Crear instancia de PHPMailer
    $mail = new PHPMailer(true);
    
    try {
        // Configuración SMTP
      //$mail->SMTPDebug = 2; // Nivel de depuración: 2 o 3 para más detalles
      //$mail->Debugoutput = 'html'; // Formato de salida de la depuración
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'magustechnologuies@gmail.com';
        $mail->Password   = 'almtvfbipiamglrv';
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;
        
        // AÑADIDO: Opciones adicionales para solucionar problemas de autenticación
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Remitente
        $mail->setFrom('magustechnologuies@gmail.com', 'Formulario de Contacto');
        
        // Destinatario principal
        $mail->addAddress('linsalo19b@gmail.com', 'Backup Recipient');
        
        // MODIFICADO: Asunto del correo para indicar envío de backup
        $mail->isHTML(true);
        $mail->Subject = 'Envío de Backup de Base de Datos';
        
        // MODIFICADO: Cuerpo del mensaje simplificado
        $mail->Body    = 'Se adjunta el archivo de respaldo de base de datos.';
        $mail->AltBody = 'Se adjunta el archivo de respaldo de base de datos.';

        // MODIFICADO: Ruta al archivo bdbackup.sql en la raíz del proyecto
        $archivo = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bdbackup.sql';

        if (file_exists($archivo)) {
            $mail->addAttachment($archivo, 'bdbackup.sql'); // nombre con que llegará el archivo
        } else {
            echo 'El archivo no existe en la ruta: ' . $archivo;
            exit; // MODIFICADO: Detenemos si no existe el archivo
        }
    
        // Enviar el correo
        $mail->send();

        // Respuesta exitosa
        echo 'Correo enviado correctamente con el backup adjunto.';
    } catch (Exception $e) {
        echo 'Error al enviar el correo: ' . $mail->ErrorInfo;
    }
