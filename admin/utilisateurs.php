<?php
require_once '../includes/db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['nom_utilisateur'] !== 'admin') {
    header('Location: ../connexion.php');
    exit;
}
// Suppression utilisateur
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM Utilisateur WHERE id = ? AND nom_utilisateur != "admin"');
    $stmt->execute([$id]);
    header('Location: utilisateurs.php');
    exit;
}
// Liste utilisateurs
$stmt = $pdo->query('SELECT id, nom_utilisateur, email FROM Utilisateur WHERE nom_utilisateur != "admin"');
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Utilisateurs</title>
    <link rel="stylesheet" href="/site-informatique/assets/css/style.css">
</head>
<body>
<header><h1>Gestion des utilisateurs</h1></header>
<main>
    <table>
        <tr><th>ID</th><th>Nom d'utilisateur</th><th>Email</th><th>Action</th></tr>
        <?php foreach ($users as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['nom_utilisateur']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><a href="?delete=<?= $u['id'] ?>" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <a href="index.php">Retour au dashboard</a>
</main>
</body>
</html> 