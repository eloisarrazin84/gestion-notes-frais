<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // Configuration du serveur SMTP Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'eloi@bewitness.fr'; // Email expéditeur
        $mail->Password   = 'gilt uepj nuao udlq'; // Mot de passe d'application Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Expéditeur et destinataire
        $mail->setFrom('eloi@bewitness.fr', 'BeWitness Support');
        $mail->addAddress($to);

        // Contenu de l'e-mail
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        // Envoi de l'e-mail
        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Erreur lors de l'envoi de l'email : " . $mail->ErrorInfo;
    }
}
?>
