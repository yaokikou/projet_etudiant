<?php
require_once __DIR__.'/includes/db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php?message=connectez-vous');
    exit;
}

$message = '';
$messageType = '';
$service_id = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;

// Récupérer la liste des services
$stmt = $pdo->query('SELECT id, nom_service FROM Service WHERE actif = 1 ORDER BY nom_service');
$services = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = (int)($_POST['service_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    
    if ($service_id && $description) {
        if (strlen($description) >= 10 && strlen($description) <= 1000) {
            $stmt = $pdo->prepare('INSERT INTO DemandeService (utilisateur_id, service_id, description) VALUES (?, ?, ?)');
            $stmt->execute([$_SESSION['user_id'], $service_id, $description]);
            $message = 'Votre demande a bien été envoyée. Nous vous contacterons dans les plus brefs délais.';
            $messageType = 'success';
            // Redirection pour éviter la duplication
            header('Location: demande_service.php?success=1');
            exit;
        } else {
            $message = 'La description doit contenir entre 10 et 1000 caractères.';
            $messageType = 'error';
        }
    } else {
        $message = 'Veuillez sélectionner un service et décrire votre demande.';
        $messageType = 'error';
    }
}

// Afficher le message de succès si redirection
if (isset($_GET['success']) && $_GET['success'] == '1') {
    $message = 'Votre demande a bien été envoyée. Nous vous contacterons dans les plus brefs délais.';
    $messageType = 'success';
}

include __DIR__.'/includes/header.php';
?>




<?php include __DIR__.'/includes/footer.php'; ?> 