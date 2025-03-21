<?php
session_start();
require_once 'config.php';
require_once 'permissions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$canValidate = hasPermission('manager');
$canViewAll = hasPermission('comptable') || hasPermission('admin');

$stmt = $pdo->prepare("SELECT * FROM expenses WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mes Notes de Frais</title>
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
        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .table th {
            background-color: #007bff;
            color: white;
            text-align: center;
        }
        .table td {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Mes Notes de Frais</h2>
        <div class="btn-container">
            <a href="home.php" class="btn btn-secondary">🏠 Retour à l'accueil</a>
            <a href="export_pdf.php" class="btn btn-primary">📄 Exporter en PDF</a>
        </div>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Montant</th>
                    <th>Catégorie</th>
                    <th>Statut</th>
                    <th>Justificatifs</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($expenses as $expense): ?>
                    <tr>
                        <td><?= htmlspecialchars($expense['date']) ?></td>
                        <td><?= htmlspecialchars($expense['amount']) ?> €</td>
                        <td><?= htmlspecialchars($expense['category']) ?></td>
                        <td><?= htmlspecialchars($expense['status']) ?></td>
                        <td>
                            <?php 
                            if (!empty($expense['receipt'])) {
                                $receipts = explode(",", $expense['receipt']);
                                foreach ($receipts as $receipt) {
                                    echo '<a href="uploads/' . htmlspecialchars(trim($receipt)) . '" class="btn btn-secondary btn-sm me-1">📂 Voir</a>';
                                }
                            } else {
                                echo "-";
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3 class="mt-4">Ajouter une Note de Frais</h3>
        <form action="add_expense.php" method="POST" enctype="multipart/form-data" class="mb-3">
            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" id="date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="amount" class="form-label">Montant</label>
                <input type="number" name="amount" id="amount" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Catégorie</label>
                <select name="category" id="category" class="form-control" required>
                    <option value="transport">Transport</option>
                    <option value="hébergement">Hébergement</option>
                    <option value="repas">Repas</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="receipts" class="form-label">Justificatif(s)</label>
                <input type="file" name="receipts[]" id="receipts" class="form-control" multiple>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
    </div>
</body>
</html>
