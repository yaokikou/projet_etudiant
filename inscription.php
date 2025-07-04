<?php
require_once __DIR__.'/includes/db.php';
include __DIR__.'/includes/header.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_utilisateur = trim($_POST['nom_utilisateur'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $motdepasse = $_POST['motdepasse'] ?? '';
    $motdepasse2 = $_POST['motdepasse2'] ?? '';
    if ($nom_utilisateur && $email && $motdepasse && $motdepasse === $motdepasse2) {
        $hash = password_hash($motdepasse, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare('INSERT INTO Utilisateur (nom_utilisateur, motdepasse, email) VALUES (?, ?, ?)');
            $stmt->execute([$nom_utilisateur, $hash, $email]);
            $message = 'Inscription réussie. Vous pouvez vous connecter.';
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = 'Nom d\'utilisateur ou email déjà utilisé.';
            } else {
                $message = 'Erreur lors de l\'inscription.';
            }
        }
    } else {
        $message = 'Veuillez remplir tous les champs et vérifier les mots de passe.';
    }
}
?>
<section>
    <h1>Inscription</h1>
    <?php if ($message): ?><p><?= htmlspecialchars($message) ?></p><?php endif; ?>
    <form method="post">
        <label>Nom d'utilisateur : <input type="text" name="nom_utilisateur" required></label><br>
        <label>Email : <input type="email" name="email" required></label><br>
        <label>Mot de passe : <input type="password" name="motdepasse" required></label><br>
        <label>Confirmer le mot de passe : <input type="password" name="motdepasse2" required></label><br>
        <button type="submit">S'inscrire</button>
    </form>
</section>
<?php include __DIR__.'/includes/footer.php'; ?> 