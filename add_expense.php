<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $date = $_POST['date'];
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $justification = isset($_POST['justification']) ? $_POST['justification'] : NULL;
    $receiptPaths = [];

    // Gestion de l'upload de plusieurs fichiers
    if (!empty($_FILES['receipts']['name'][0])) {
        $targetDir = "uploads/";
        foreach ($_FILES["receipts"]["name"] as $key => $fileName) {
            $fileTmpPath = $_FILES["receipts"]["tmp_name"][$key];
            $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $newFileName = time() . "_" . uniqid() . "." . $fileType;
            $targetFilePath = $targetDir . $newFileName;

            // Vérifier le type de fichier autorisé
            $allowedTypes = array("jpg", "jpeg", "png", "pdf");
            if (!in_array($fileType, $allowedTypes)) {
                die("Type de fichier non autorisé. Seuls les fichiers JPG, JPEG, PNG et PDF sont acceptés.");
            }

            // Vérifier la taille (max 5MB par fichier)
            if ($_FILES["receipts"]["size"][$key] > 5 * 1024 * 1024) {
                die("Un fichier est trop volumineux. Maximum 5MB.");
            }

            // Déplacer le fichier vers le dossier sécurisé
            if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                $receiptPaths[] = $newFileName;
            } else {
                die("Erreur lors de l'upload d'un fichier.");
            }
        }
    }

    // Stocker les noms des fichiers sous forme de chaîne séparée par des virgules
    $receiptPathsStr = implode(",", $receiptPaths);

    // Insérer les données en base
    $stmt = $pdo->prepare("INSERT INTO expenses (user_id, date, amount, category, justification, receipt) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $date, $amount, $category, $justification ?? NULL, $receiptPathsStr])) {
        header("Location: dashboard.php?success=1");
        exit();
    } else {
        die("Erreur lors de l'ajout de la note de frais.");
    }
}
?>
