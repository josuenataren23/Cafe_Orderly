<?php
// Incluye Composer Autoloader
// require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailerService {
    
    // Configuración SMTP de Hostinger (Tus valores confirmados)
    private $host = 'smtp.hostinger.com';
    private $username = 'no-reply@kimikodev.click';
    private $password = '!a0PA3mU';
    private $port = 465;
    private $smtpSecure = PHPMailer::ENCRYPTION_SMTPS;

    public function sendVerificationEmail($recipientEmail, $recipientName, $code) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $this->host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->username;
            $mail->Password   = $this->password;
            $mail->SMTPSecure = $this->smtpSecure;
            $mail->Port       = $this->port;
            $mail->CharSet    = 'UTF-8';

            // Remitentes
            $mail->setFrom($this->username, 'Verificacion de Cuenta Cafe Orderly'); 
            $mail->addAddress($recipientEmail, $recipientName);          
            $mail->addReplyTo('soporte@kimikodev.click', 'Soporte Cafe Orderly'); 

            // Contenido del Correo (Menciona 3 minutos)
            $mail->isHTML(true);                                        
            $mail->Subject = 'Tu Codigo de Verificacion de Cuenta';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px;'>
                    <h2 style='color: #2d8cf0; border-bottom: 2px solid #eee; padding-bottom: 10px;'>Hola $recipientName,</h2>
                    <p>Gracias por registrarte. Por favor, usa el siguiente codigo para verificar tu cuenta. Este codigo expira en <strong>3 minutos</strong>:</p>
                    <div style='text-align: center; margin: 25px 0;'>
                        <h1 style='
                            color: #ffffff; 
                            background-color: #2d8cf0; 
                            padding: 15px 30px; 
                            border-radius: 5px; 
                            display: inline-block;
                            font-size: 32px;
                            letter-spacing: 5px;
                            margin: 0;
                        '>
                            $code
                        </h1>
                    </div>
                    <p>Si no solicitaste este codigo, por favor ignora este correo.</p>
                </div>
            ";
            $mail->AltBody = "Tu codigo de verificacion es: $code. Este codigo expira en 3 minutos.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error: " . $e->getMessage());
            return false;
        }
    }
}