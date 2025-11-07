<?php
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'twoj_email@gmail.com';
    $mail->Password = 'HASLO_APLIKACJI';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('twoj_email@gmail.com', 'AutoPart');
    $mail->addAddress('adres_docelowy@wp.pl');

    $mail->isHTML(true);
    $mail->Subject = 'Test PHPMailer (bez Composera)';
    $mail->Body = 'Działa! ✅';

    $mail->send();
    echo '✅ Mail wysłany poprawnie!';
} catch (Exception $e) {
    echo '❌ Błąd: ' . $mail->ErrorInfo;
}
