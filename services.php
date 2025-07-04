<?php
require_once __DIR__.'/includes/db.php';
include __DIR__.'/includes/header.php';

$stmt = $pdo->prepare('SELECT nom_service, description, image FROM Service WHERE actif = 1 ORDER BY date_ajout DESC');
$stmt->execute();
$services = $stmt->fetchAll();

if (isset($_POST['ajouter'])) {
    // ... traitement de l'ajout ...
    header('Location: services.php');
    exit;
}
?>
<section>
    <h1>Nos Services</h1>
    <div class="services">
        <?php foreach ($services as $service): ?>
            <div class="service" style="display:flex;align-items:center;gap:1em;margin-bottom:2em;">
                <img src="assets/img/<?= htmlspecialchars($service['image']) ?>" alt="Image service" style="max-width:120px;max-height:120px;border-radius:8px;">
                <div>
                    <h2><?= htmlspecialchars($service['nom_service']) ?></h2>
                    <p><?= nl2br(htmlspecialchars($service['description'])) ?></p>
                    <a href="demande_service.php?service_id=<?= urlencode($service['id'] ?? '') ?>" class="btn">Demander ce service</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php include __DIR__.'/includes/footer.php'; ?> 