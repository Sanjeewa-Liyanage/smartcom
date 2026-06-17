<?php

namespace App\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    /**
     * Send Email
     *
     * @param string $to
     * @param string $name
     * @param string $subject
     * @param string $body
     * @param string $altBody
     *
     * @return bool
     * @throws Exception
     */
    public static function send(
        string $to,
        string $name,
        string $subject,
        string $body,
        string $altBody = ''
    ): bool {

        $smtpUser = $_ENV['SMTP_USER'] ?? '';
        $smtpPass = $_ENV['SMTP_PASS'] ?? '';
        $fromName = $_ENV['SMTP_FROM_NAME'] ?? 'Smart Commerce Core Admin';

        if (empty($smtpUser) || empty($smtpPass)) {
            throw new \Exception('SMTP credentials are missing in .env');
        }

        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $smtpUser;
            $mail->Password = $smtpPass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Uncomment while debugging
            // $mail->SMTPDebug = 2;

            $mail->setFrom($smtpUser, $fromName);
            $mail->addAddress($to, $name);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = $altBody ?: strip_tags($body);

            return $mail->send();

        } catch (Exception $e) {
            throw new \Exception(
                'Mail sending failed: ' . $mail->ErrorInfo
            );
        }
    }
}