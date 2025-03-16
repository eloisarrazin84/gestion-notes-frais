<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = $_GET['id'];
    $status = $_GET['action'];

    if (in_array($status, ['validé', 'rejeté'])) {
        $stmt = $pdo->prepare("UPDATE expenses SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }
}

header("Location: dashboard.php");
exit();
?>
