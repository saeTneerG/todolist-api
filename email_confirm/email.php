<?php
require_once(__DIR__ . '/../vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMail($toMail, $toName, $activation_token) {
    $mail = new PHPMailer(true);
    try {
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = 'smtp.gmail.com';
        $mail->Username = 'chawit.srikam@gmail.com';
        $mail->Password = 'wvaz ytot vzcd cnvp';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('chawit.srikam@gmail.com', 'noreply');
        $mail->addAddress($toMail, $toName);

        $mail->isHTML(true);
        $mail->Subject = 'Account Activation';
        $mail->Body    = 'Click <a href="http://localhost/todolist/email_confirm/activation_account.php?token=' . $activation_token . '">here</a> to activate you account.';

        $mail->send();
        return true;
    } catch (Exception $e) {
         return "Error: {$mail->ErrorInfo}";
    }
}
?>