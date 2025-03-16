<?php
session_start();
require_once 'config.php';
require_once 'permissions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userRole = $_SESSION['role'];
$apps = [
    'user' => [
        ['name' => 'Mes Notes de Frais', 'icon' => '📝', 'link' => 'dashboard.php'],
    ],
    'manager' => [
        ['name' => 'Mes Notes de Frais', 'icon' => '📝', 'link' => 'dashboard.php'],
        ['name' => 'Validation des Notes', 'icon' => '✅', 'link' => 'validate_expenses.php'],
        ['name' => 'Gérer les Notes de Frais', 'icon' => '📂', 'link' => 'manage_expenses.php'],
    ],
    'comptable' => [
        ['name' => 'Toutes les Notes de Frais', 'icon' => '📊', 'link' => 'all_expenses.php'],
        ['name' => 'Gérer les Notes de Frais', 'icon' => '📂', 'link' => 'manage_expenses.php'],
        ['name' => 'Tableau de Bord', 'icon' => '📈', 'link' => 'dashboard_stats.php'],
    ],
    'admin' => [
        ['name' => 'Gestion des Utilisateurs', 'icon' => '👤', 'link' => 'manage_users.php'],
        ['name' => 'Gérer les Notes de Frais', 'icon' => '📂', 'link' => 'manage_expenses.php'],
        ['name' => 'Tableau de Bord', 'icon' => '📈', 'link' => 'dashboard_stats.php'],
    ]
];

$availableApps = array_merge($apps['user'], $apps[$userRole] ?? []);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accueil</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .app-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 50px;
        }
        .app-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 150px;
            height: 150px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            color: black;
            font-size: 18px;
            font-weight: bold;
            transition: transform 0.3s;
        }
        .app-card:hover {
            transform: scale(1.1);
        }
        .app-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container text-center">
        <h2 class="mt-5">Bienvenue dans l'outil de gestion</h2>
        <div class="app-container">
            <?php foreach ($availableApps as $app): ?>
                <a href="<?= htmlspecialchars($app['link']) ?>" class="app-card">
                    <div class="app-icon"> <?= $app['icon'] ?> </div>
                    <?= htmlspecialchars($app['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="mt-4">
            <a href="logout.php" class="btn btn-danger">Se déconnecter</a>
        </div>
    </div>
</body>
</html>
