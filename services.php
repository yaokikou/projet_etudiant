<?php
require_once __DIR__.'/includes/db.php';
include __DIR__.'/includes/header.php';

$stmt = $pdo->prepare('SELECT id, nom_service, description, image FROM Service WHERE actif = 1 ORDER BY date_ajout DESC');
$stmt->execute();
$services = $stmt->fetchAll();

if (isset($_POST['ajouter'])) {
    // ... traitement de l'ajout ...
    header('Location: services.php');
    exit;
}
?>


