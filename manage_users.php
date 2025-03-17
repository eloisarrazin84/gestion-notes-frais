<?php
session_start();
require_once 'config.php';
require_once 'permissions.php';
require_once 'mail.php';

if (!hasPermission('admin')) {
    die("Accès refusé.");
}

// Création d'un nouvel utilisateur
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_user'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    
    // Vérifier si l'utilisateur existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errorMessage = "L'utilisateur existe déjà.";
    } else {
        // Générer un token pour la création du mot de passe
        $token = bin2hex(random_bytes(50));
        
        // Insérer l'utilisateur avec le token
        $stmt = $pdo->prepare("INSERT INTO users (name, email, role, password_reset_token) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $role, $token]);
        
        // Envoyer l'email de création de compte
        $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=" . $token;
        $subject = "Création de votre compte";
        $body = "<p>Bonjour $name,</p><p>Un compte a été créé pour vous. Veuillez définir votre mot de passe en cliquant sur le lien suivant :</p><p><a href='$resetLink'>$resetLink</a></p>";
        sendEmail($email, $subject, $body);

        $successMessage = "Utilisateur créé avec succès. Un email a été envoyé pour définir le mot de passe.";
    }
}

// Modification de rôle
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_role'])) {
    $userId = $_POST['user_id'];
    $newRole = $_POST['role'];
    
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$newRole, $userId]);
    
    sendRoleChangeEmail($email, $newRole);
    $successMessage = "Le rôle a été mis à jour.";
}

$users = $pdo->query("SELECT id, name, email, role FROM users")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des Utilisateurs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 900px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">Gestion des Utilisateurs</h2>
        <a href="home.php" class="btn btn-secondary mb-3">🏠 Retour à l'accueil</a>
        
        <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success"> <?= htmlspecialchars($successMessage) ?> </div>
        <?php endif; ?>
        
        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger"> <?= htmlspecialchars($errorMessage) ?> </div>
        <?php endif; ?>

        <!-- Formulaire de création d'utilisateur -->
        <form method="post" class="mb-4">
            <h4>Créer un nouvel utilisateur</h4>
            <div class="mb-3">
                <label>Nom</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Rôle</label>
                <select name="role" class="form-select">
                    <option value="user">User</option>
                    <option value="manager">Manager</option>
                    <option value="comptable">Comptable</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" name="create_user" class="btn btn-primary">Créer l'utilisateur</button>
        </form>

        <!-- Tableau de gestion des utilisateurs -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <form method="post" class="d-flex">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <select name="role" class="form-select me-2">
                                    <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                                    <option value="manager" <?= $user['role'] == 'manager' ? 'selected' : '' ?>>Manager</option>
                                    <option value="comptable" <?= $user['role'] == 'comptable' ? 'selected' : '' ?>>Comptable</option>
                                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                                <button type="submit" name="update_role" class="btn btn-primary">Modifier</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
