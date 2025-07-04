<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['nom_utilisateur'] !== 'admin') {
    header('Location: ../connexion.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Tableau de bord</title>
    <link rel="stylesheet" href="/site-informatique/assets/css/style.css">
</head>
<body>
<header><h1>Tableau de bord administrateur</h1></header>
<main>
    <ul>
        <li><a href="utilisateurs.php">Gestion des utilisateurs</a></li>
        <li><a href="services.php">Gestion des services</a></li>
        <li><a href="publications.php">Gestion des publications</a></li>
        <li><a href="contacts.php">Messages de contact</a></li>
        <li><a href="demandes_services.php">Demandes de services</a></li>
        <li><a href="../index.php">Retour au site</a></li>
    </ul>
</main>
</body>
</html> 