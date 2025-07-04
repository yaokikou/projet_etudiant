<?php
require_once __DIR__.'/includes/db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php?message=connectez-vous');
    exit;
}
include __DIR__.'/includes/header.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $msg = trim($_POST['message'] ?? '');
    if ($nom && $email && $msg) {
        $stmt = $pdo->prepare('INSERT INTO Contact (nom, email, message) VALUES (?, ?, ?)');
        $stmt->execute([$nom, $email, $msg]);
        $message = 'Votre message a bien été envoyé.';
    } else {
        $message = 'Veuillez remplir tous les champs.';
    }
}
?>
<section>
    <h1>Contact</h1>
    <?php if ($message): ?><p><?= htmlspecialchars($message) ?></p><?php endif; ?>
    <form method="post">
        <label>Nom : <input type="text" name="nom" required></label><br>
        <label>Email : <input type="email" name="email" required></label><br>
        <label>Message : <textarea name="message" required></textarea></label><br>
        <button type="submit">Envoyer</button>
    </form>
</section>
<?php include __DIR__.'/includes/footer.php'; ?> 