<?php
require_once __DIR__.'/includes/db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php?message=connectez-vous');
    exit;
}
$message = '';
$service_id = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;
// Récupérer la liste des services
$stmt = $pdo->query('SELECT id, nom_service FROM Service WHERE actif = 1 ORDER BY nom_service');
$services = $stmt->fetchAll();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = (int)($_POST['service_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    if ($service_id && $description) {
        $stmt = $pdo->prepare('INSERT INTO DemandeService (utilisateur_id, service_id, description) VALUES (?, ?, ?)');
        $stmt->execute([$_SESSION['user_id'], $service_id, $description]);
        $message = 'Votre demande a bien été envoyée.';
        // Redirection pour éviter la duplication
        header('Location: demande_service.php?success=1');
        exit;
    } else {
        $message = 'Veuillez sélectionner un service et décrire votre demande.';
    }
}
// Afficher le message de succès si redirection
if (isset($_GET['success']) && $_GET['success'] == '1') {
    $message = 'Votre demande a bien été envoyée.';
}
include __DIR__.'/includes/header.php';
?>
<section>
    <h1>Demande de service</h1>
    <?php if ($message): ?><p><?= htmlspecialchars($message) ?></p><?php endif; ?>
    <form method="post">
        <label>Service :
            <select name="service_id" required>
                <option value="">-- Choisir un service --</option>
                <?php foreach ($services as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= ($s['id'] == $service_id) ? 'selected' : '' ?>><?= htmlspecialchars($s['nom_service']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br><br>
        <label>Description de la demande :<br>
            <textarea name="description" required rows="5" cols="50" placeholder="Décrivez votre besoin..."></textarea>
        </label><br>
        <button type="submit">Envoyer la demande</button>
    </form>
    <a href="services.php">Retour aux services</a>
</section>
<?php include __DIR__.'/includes/footer.php'; ?> 