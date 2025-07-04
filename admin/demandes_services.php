<?php
require_once '../includes/db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['nom_utilisateur'] !== 'admin') {
    header('Location: ../connexion.php');
    exit;
}
// Changement de statut
if (isset($_POST['changer_statut'])) {
    $id = (int)$_POST['id'];
    $statut = $_POST['statut'];
    $stmt = $pdo->prepare('UPDATE DemandeService SET statut=? WHERE id=?');
    $stmt->execute([$statut, $id]);
    header('Location: demandes_services.php');
    exit;
}
// Suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare('DELETE FROM DemandeService WHERE id=?')->execute([$id]);
    header('Location: demandes_services.php');
    exit;
}
// Récupérer toutes les demandes de services avec jointure utilisateur et service
$stmt = $pdo->query('SELECT d.id, u.nom_utilisateur, s.nom_service, d.description, d.date_demande, d.statut FROM DemandeService d JOIN Utilisateur u ON d.utilisateur_id = u.id JOIN Service s ON d.service_id = s.id ORDER BY d.date_demande DESC');
$demandes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Demandes de services</title>
    <link rel="stylesheet" href="/site-informatique/assets/css/style.css">
</head>
<body>
<header><h1>Demandes de services</h1></header>
<main>
    <table>
        <tr>
            <th>ID</th>
            <th>Utilisateur</th>
            <th>Service</th>
            <th>Description</th>
            <th>Date</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($demandes as $d): ?>
        <tr>
            <td><?= $d['id'] ?></td>
            <td><?= htmlspecialchars($d['nom_utilisateur']) ?></td>
            <td><?= htmlspecialchars($d['nom_service']) ?></td>
            <td><?= nl2br(htmlspecialchars($d['description'])) ?></td>
            <td><?= $d['date_demande'] ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $d['id'] ?>">
                    <select name="statut">
                        <option value="en attente" <?= $d['statut']==='en attente'?'selected':'' ?>>En attente</option>
                        <option value="en cours" <?= $d['statut']==='en cours'?'selected':'' ?>>En cours</option>
                        <option value="traitée" <?= $d['statut']==='traitée'?'selected':'' ?>>Traitée</option>
                        <option value="refusée" <?= $d['statut']==='refusée'?'selected':'' ?>>Refusée</option>
                    </select>
                    <button type="submit" name="changer_statut">OK</button>
                </form>
            </td>
            <td>
                <a href="?delete=<?= $d['id'] ?>" onclick="return confirm('Supprimer cette demande ?')">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <a href="index.php">Retour au dashboard</a>
</main>
</body>
</html> 