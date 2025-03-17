<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        // Configuration du serveur SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'eloi@bewitness.fr';
        $mail->Password = 'gilt uepj nuao udlq';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Expéditeur et destinataire
        $mail->setFrom('eloi@bewitness.fr', 'Gestion Notes de Frais');
        $mail->addAddress($to);

        // Contenu de l'email
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        // Envoi de l'email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erreur d'envoi d'email: " . $mail->ErrorInfo);
        return false;
    }
}

// Notifications pour workflow des notes de frais
function sendExpenseSubmissionEmail($to, $expenseId) {
    $subject = "Nouvelle note de frais soumise";
    $body = "<p>Une nouvelle note de frais (#$expenseId) a été soumise et est en attente de validation.</p>";
    sendEmail($to, $subject, $body);
}

function sendExpenseApprovalEmail($to, $expenseId) {
    $subject = "Note de frais approuvée";
    $body = "<p>Votre note de frais (#$expenseId) a été <strong>approuvée</strong>.</p>";
    sendEmail($to, $subject, $body);
}

function sendExpenseRejectionEmail($to, $expenseId, $reason) {
    $subject = "Note de frais rejetée";
    $body = "<p>Votre note de frais (#$expenseId) a été <strong>rejetée</strong>.</p><p>Raison: $reason</p>";
    sendEmail($to, $subject, $body);
}

function sendRoleChangeEmail($to, $newRole) {
    $subject = "Changement de rôle";
    $body = "<p>Votre rôle a été mis à jour. Vous êtes maintenant un <strong>$newRole</strong>.</p>";
    sendEmail($to, $subject, $body);
}
?>
