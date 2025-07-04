<?php
require_once __DIR__.'/includes/db.php';
session_start();
include __DIR__.'/includes/header.php';
$message = '';
if (isset($_GET['message']) && $_GET['message'] === 'connectez-vous') {
    $message = "Vous devez être inscrit et connecté pour accéder à cette page.";
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_utilisateur = trim($_POST['nom_utilisateur'] ?? '');
    $motdepasse = $_POST['motdepasse'] ?? '';
    if ($nom_utilisateur && $motdepasse) {
        $stmt = $pdo->prepare('SELECT id, motdepasse FROM Utilisateur WHERE nom_utilisateur = ?');
        $stmt->execute([$nom_utilisateur]);
        $user = $stmt->fetch();
        if ($user && password_verify($motdepasse, $user['motdepasse'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nom_utilisateur'] = $nom_utilisateur;
            header('Location: index.php');
            exit;
        } else {
            $message = 'Identifiants invalides.';
        }
    } else {
        $message = 'Veuillez remplir tous les champs.';
    }
}
?>
<section>
    <h1>Connexion</h1>
    <?php if ($message): ?><p><?= htmlspecialchars($message) ?></p><?php endif; ?>
    <form method="post">
        <label>Nom d'utilisateur : <input type="text" name="nom_utilisateur" required></label><br>
        <label>Mot de passe : <input type="password" name="motdepasse" required></label><br>
        <button type="submit">Se connecter</button>
    </form>
</section>
<?php include __DIR__.'/includes/footer.php'; ?> 