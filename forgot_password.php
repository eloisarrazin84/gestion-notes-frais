<?php
session_start();
require_once 'config.php';
require_once 'mail.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    
    // Vérifier si l'utilisateur existe
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Générer un token unique
        $token = bin2hex(random_bytes(32));
        
        // Sauvegarder le token en base
        $stmt = $pdo->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
        $stmt->execute([$token, $email]);
        
        // Envoyer l'email avec le lien de réinitialisation
        $resetLink = "http://yourdomain.com/reset_password.php?token=$token";
        $subject = "Réinitialisation de votre mot de passe";
        $body = "Cliquez sur ce lien pour réinitialiser votre mot de passe : <a href='$resetLink'>$resetLink</a>";
        
        sendEmail($email, $subject, $body);
        
        $successMessage = "Un email de réinitialisation a été envoyé.";
    } else {
        $errorMessage = "Aucun compte trouvé avec cet email.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mot de passe oublié</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Réinitialisation du mot de passe</h2>
        <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success"> <?= htmlspecialchars($successMessage) ?> </div>
        <?php elseif (!empty($errorMessage)): ?>
            <div class="alert alert-danger"> <?= htmlspecialchars($errorMessage) ?> </div>
        <?php endif; ?>
        
        <form method="post">
            <input type="email" name="email" class="form-control" placeholder="Votre email" required>
            <button type="submit" class="btn btn-primary btn-login">Envoyer</button>
        </form>
        <a href="login.php" class="forgot-password">Retour à la connexion</a>
    </div>
</body>
</html>
