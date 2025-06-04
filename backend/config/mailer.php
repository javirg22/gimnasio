<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../../vendor/autoload.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

class Mailer {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        try {
            $this->mail->isSMTP();
            $this->mail->Host = getenv('SMTP_HOST');
            $this->mail->SMTPAuth = true;
            $this->mail->Username = getenv('SMTP_USER');
            $this->mail->Password = getenv('SMTP_PASS');
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port = getenv('SMTP_PORT');
            $this->mail->setFrom(getenv('MAIL_FROM'), getenv('MAIL_FROM_NAME'));
        } catch (Exception $e) {
            error_log("Mailer Error: " . $e->getMessage());
        }
    }

    public function sendMail($to, $subject, $body) {
        try {
            $this->mail->addAddress($to);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            $this->mail->isHTML(true);
            
            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Error enviando correo: " . $e->getMessage());
            return false;
        }
    }
}
