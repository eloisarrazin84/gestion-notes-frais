<?php
session_start();
require_once 'config.php';
require_once 'permissions.php';

if (!hasPermission('admin')) {
    die("Accès refusé.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['user_id'];
    $newRole = $_POST['role'];
    
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$newRole, $userId]);
    
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
        .btn-back {
            margin-bottom: 15px;
            display: inline-block;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .table th, .table td {
            text-align: center;
            white-space: nowrap;
        }
        @media (max-width: 768px) {
            .table th, .table td {
                font-size: 12px;
                white-space: normal;
            }
            .form-select, .btn {
                font-size: 12px;
                padding: 6px;
            }
            .btn-back {
                display: block;
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="text-center flex-grow-1">Gestion des Utilisateurs</h2>
            <a href="home.php" class="btn btn-secondary btn-back">🏠 Retour à l'accueil</a>
        </div>
        <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success mt-3"> <?= htmlspecialchars($successMessage) ?> </div>
        <?php endif; ?>
        
        <div class="table-responsive mt-4">
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
                                <form method="post" class="d-flex align-items-center justify-content-center">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <select name="role" class="form-select me-2">
                                        <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                                        <option value="manager" <?= $user['role'] == 'manager' ? 'selected' : '' ?>>Manager</option>
                                        <option value="comptable" <?= $user['role'] == 'comptable' ? 'selected' : '' ?>>Comptable</option>
                                        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary">Modifier</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
