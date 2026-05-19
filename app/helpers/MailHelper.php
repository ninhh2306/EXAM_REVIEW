<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once ROOT . '/vendor/autoload.php';

class MailHelper
{
    public static function send($to, $subject, $body)
    {
        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();

            $mail->Host = 'smtp.gmail.com';

            $mail->SMTPAuth = true;

            // EMAIL GMAIL THẬT
            $mail->Username = 'ninhh2306@gmail.com';

            // APP PASSWORD GOOGLE
            $mail->Password = 'vrmpnrmruguzhucw';

            $mail->SMTPSecure =
                PHPMailer::ENCRYPTION_STARTTLS;

            $mail->Port = 587;

            $mail->CharSet = 'UTF-8';

            // FROM
            $mail->setFrom(
                'ninhh2306@gmail.com',
                'Vui Luyện Thi'
            );

            // TO
            $mail->addAddress($to);

            // HTML
            $mail->isHTML(true);

            $mail->Subject = $subject;

            $mail->Body = $body;

            $mail->send();

            return true;

        } 
        
        catch (Exception $e) {
            return false;
        }
    }
}