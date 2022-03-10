<?php

namespace App\Http\Mailer;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use Illuminate\Foundation\Http\FormRequest;

class SendEmailRequest
{

    public function sendEmail($email, $token)
    {
        $name = 'Manjunath Halli';
        $email = $email;
        $subject = 'fundoo Notes password Settings ';
        $data = "Hi, " . $name . " Your password Reset Link : " . $token;

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;
            $mail->setFrom(env('MAIL_USERNAME'), env('MAIL_FROM_NAME'));
            $mail->addAddress($email, $name);
            $mail->isHTML(true);
            $mail->Subject =  $subject;
            $mail->Body    = $data;
            $dt = $mail->send();
            sleep(3);

            if ($dt)
                return true;
            else
                return false;
        } catch (Exception $e) {
            return back()->with('error', 'Message could not be sent.');
        }
    }
}
