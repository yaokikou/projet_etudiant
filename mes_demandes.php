<?php
require_once __DIR__.'/includes/db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php?message=connectez-vous');
    exit;
}
// Récupérer les demandes de l'utilisateur connecté
$stmt = $pdo->prepare('SELECT d.id, s.nom_service, d.description, d.date_demande, d.statut FROM DemandeService d JOIN Service s ON d.service_id = s.id WHERE d.utilisateur_id = ? ORDER BY d.date_demande DESC');
$stmt->execute([$_SESSION['user_id']]);
$demandes = $stmt->fetchAll();
include __DIR__.'/includes/header.php';
?>
<section>
    <h1>Mes demandes de services</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Service</th>
            <th>Description</th>
            <th>Date</th>
            <th>Statut</th>
        </tr>
        <?php foreach ($demandes as $d): ?>
        <tr>
            <td><?= $d['id'] ?></td>
            <td><?= htmlspecialchars($d['nom_service']) ?></td>
            <td><?= nl2br(htmlspecialchars($d['description'])) ?></td>
            <td><?= $d['date_demande'] ?></td>
            <td>
                <span style="
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-weight: bold;
                    color: white;
                    background-color: 
                    <?php 
                    switch($d['statut']) {
                        case 'en attente': echo '#ff9500'; break;
                        case 'en cours': echo '#0074d9'; break;
                        case 'traitée': echo '#2ecc40'; break;
                        case 'refusée': echo '#ff4136'; break;
                        default: echo '#666';
                    }
                    ?>
                ">
                    <?= htmlspecialchars($d['statut']) ?>
                </span>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <a href="index.php">Retour à l'accueil</a>
</section>
<?php include __DIR__.'/includes/footer.php'; ?> 