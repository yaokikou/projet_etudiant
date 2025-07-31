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
    
    <?php if (empty($demandes)): ?>
        <div style="text-align: center; padding: 3rem; color: #666;">
            <i class="fas fa-clipboard-list" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
            <h2>Aucune demande de service</h2>
            <p>Vous n'avez pas encore fait de demande de service.</p>
            <a href="services.php" class="btn" style="margin-top: 1rem;">Découvrir nos services</a>
        </div>
    <?php else: ?>
    <table>
        <tr>
            <th>Service</th>
            <th>Statut</th>
        </tr>
        <?php foreach ($demandes as $d): ?>
        <tr>
            <td><?= htmlspecialchars($d['nom_service']) ?></td>
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
    <?php endif; ?>
    
    <a href="index.php">Retour sur la page d'accueil</a>
</section>
<?php include __DIR__.'/includes/footer.php'; ?> 