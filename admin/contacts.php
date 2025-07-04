<?php
require_once '../includes/db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['nom_utilisateur'] !== 'admin') {
    header('Location: ../connexion.php');
    exit;
}
$stmt = $pdo->query('SELECT * FROM Contact ORDER BY date_envoi DESC');
$contacts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Messages de contact</title>
    <link rel="stylesheet" href="/site-informatique/assets/css/style.css">
</head>
<body>
<header><h1>Messages de contact</h1></header>
<main>
    <table>
        <tr><th>ID</th><th>Nom</th><th>Email</th><th>Message</th><th>Date</th></tr>
        <?php foreach ($contacts as $c): ?>
        <tr>
            <td><?= $c['id'] ?></td>
            <td><?= htmlspecialchars($c['nom']) ?></td>
            <td><?= htmlspecialchars($c['email']) ?></td>
            <td><?= nl2br(htmlspecialchars($c['message'])) ?></td>
            <td><?= $c['date_envoi'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <a href="index.php">Retour au dashboard</a>
</main>
</body>
</html> 