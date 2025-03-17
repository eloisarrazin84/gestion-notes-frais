<?php
session_start();
require_once 'config.php';
require_once 'permissions.php';
require_once 'mail.php';
require_once 'templates/email_template.php';

if (!hasPermission('manager') && !hasPermission('comptable') && !hasPermission('admin')) {
    die("Accès refusé.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['expense_id']) && isset($_POST['action'])) {
    $expenseId = $_POST['expense_id'];
    $action = $_POST['action'];
    $reason = isset($_POST['reason']) ? $_POST['reason'] : '';

    $stmt = $pdo->prepare("SELECT expenses.*, users.email, users.name FROM expenses JOIN users ON expenses.user_id = users.id WHERE expenses.id = ?");
    $stmt->execute([$expenseId]);
    $expense = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($expense) {
        $updateStmt = $pdo->prepare("UPDATE expenses SET status = ?, rejection_reason = ? WHERE id = ?");
        $updateStmt->execute([$action, $reason, $expenseId]);

        $title = ($action == "validé") ? "Votre note de frais a été validée" : "Votre note de frais a été rejetée";
        $message = "Bonjour " . htmlspecialchars($expense['name']) . ",<br><br>Votre note de frais (#" . $expenseId . ") a été <b>" . $action . "</b>.";
        if ($action == "rejeté") {
            $message .= "<br><br>Raison : " . htmlspecialchars($reason);
        }
        $emailBody = renderEmailTemplate($title, $message);
        sendEmail($expense['email'], $title, $emailBody);
    }
    header("Location: manage_expenses.php");
    exit;
}

$stmt = $pdo->query("SELECT expenses.*, users.name, users.email FROM expenses JOIN users ON expenses.user_id = users.id ORDER BY created_at DESC");
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des Notes de Frais</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        function rejectExpense(id) {
            let reason = prompt("Veuillez entrer la raison du rejet :");
            if (reason !== null && reason.trim() !== "") {
                document.getElementById('rejectForm' + id).reason.value = reason;
                document.getElementById('rejectForm' + id).submit();
            }
        }
    </script>
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
        <h2 class="text-center">Gestion des Notes de Frais</h2>
        <a href="home.php" class="btn btn-secondary btn-back">🏠 Retour à l'accueil</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Date</th>
                    <th>Montant</th>
                    <th>Catégorie</th>
                    <th>Statut</th>
                    <th>Justificatif</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($expenses as $expense): ?>
                    <tr>
                        <td><?= htmlspecialchars($expense['name']) ?></td>
                        <td><?= htmlspecialchars($expense['date']) ?></td>
                        <td><?= htmlspecialchars($expense['amount']) ?> €</td>
                        <td><?= htmlspecialchars($expense['category']) ?></td>
                        <td><?= htmlspecialchars($expense['status']) ?></td>
                        <td>
                            <?php if (!empty($expense['receipt'])): ?>
                                <a href="uploads/<?= htmlspecialchars(trim($expense['receipt'])) ?>" class="btn btn-secondary btn-sm">📂 Voir</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (hasPermission('manager') && $expense['status'] == 'en attente'): ?>
                               <form method="POST" action="manage_expenses.php" style="display:inline;">
                                   <input type="hidden" name="expense_id" value="<?= $expense['id'] ?>">
                                   <input type="hidden" name="action" value="validé">
                                   <button type="submit" class="btn btn-success btn-sm">✔️ Valider</button>
                               </form>

                                <form id="rejectForm<?= $expense['id'] ?>" method="POST" action="manage_expenses.php" style="display:inline;">
                                    <input type="hidden" name="expense_id" value="<?= $expense['id'] ?>">
                                    <input type="hidden" name="action" value="rejeté">
                                    <input type="hidden" name="reason">
                                    <button type="button" class="btn btn-warning btn-sm" onclick="rejectExpense(<?= $expense['id'] ?>)">❌ Rejeter</button>
                                </form>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
