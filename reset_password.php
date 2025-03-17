<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Vérifier le token
    $stmt = $pdo->prepare("SELECT id FROM users WHERE password_reset_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        // Mettre à jour le mot de passe et supprimer le token
        $stmt = $pdo->prepare("UPDATE users SET password = ?, password_reset_token = NULL WHERE password_reset_token = ?");
        $stmt->execute([$newPassword, $token]);

        echo "Mot de passe mis à jour. Vous pouvez maintenant vous connecter.";
    } else {
        echo "Lien de réinitialisation invalide.";
    }
}

if (!isset($_GET['token'])) {
    die("Lien invalide.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Réinitialisation du mot de passe</title>
</head>
<body>
    <h2>Définir un nouveau mot de passe</h2>
    <form method="post">
        <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token']) ?>">
        <input type="password" name="password" placeholder="Nouveau mot de passe" required>
        <button type="submit">Réinitialiser</button>
    </form>
</body>
</html>
