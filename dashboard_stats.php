<?php
session_start();
require_once 'config.php';
require_once 'permissions.php';

if (!hasPermission('comptable') && !hasPermission('admin')) {
    die("Accès refusé.");
}

// Récupération des statistiques
$totalExpenses = $pdo->query("SELECT SUM(amount) as total FROM expenses")->fetchColumn();
$totalPending = $pdo->query("SELECT COUNT(*) FROM expenses WHERE status = 'en attente'")->fetchColumn();
$totalValidated = $pdo->query("SELECT COUNT(*) FROM expenses WHERE status = 'validé'")->fetchColumn();
$totalRejected = $pdo->query("SELECT COUNT(*) FROM expenses WHERE status = 'rejeté'")->fetchColumn();

$expensesByCategory = $pdo->query("SELECT category, SUM(amount) as total FROM expenses GROUP BY category")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tableau de Bord</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5 text-center">
        <h2>Tableau de Bord</h2>
        <div class="d-flex justify-content-center gap-3 mt-4">
            <div class="p-3 border rounded shadow-sm">
                <h4>Total Dépenses</h4>
                <p class="fs-4 text-primary">€<?= number_format($totalExpenses, 2) ?></p>
            </div>
            <div class="p-3 border rounded shadow-sm">
                <h4>En attente</h4>
                <p class="fs-4 text-warning"><?= $totalPending ?></p>
            </div>
            <div class="p-3 border rounded shadow-sm">
                <h4>Validées</h4>
                <p class="fs-4 text-success"><?= $totalValidated ?></p>
            </div>
            <div class="p-3 border rounded shadow-sm">
                <h4>Rejetées</h4>
                <p class="fs-4 text-danger"><?= $totalRejected ?></p>
            </div>
        </div>
        <a href="home.php" class="btn btn-secondary mt-4">Retour</a>
    </div>
</body>
</html>
