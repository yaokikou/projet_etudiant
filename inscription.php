<?php
require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/auth.php';
include __DIR__.'/includes/header.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_utilisateur = trim($_POST['nom_utilisateur'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $motdepasse = $_POST['motdepasse'] ?? '';
    $motdepasse2 = $_POST['motdepasse2'] ?? '';
    
    if ($nom_utilisateur && $email && $motdepasse && $motdepasse === $motdepasse2) {
        // Validation supplémentaire
        if (strlen($motdepasse) < 6) {
            $message = 'Le mot de passe doit contenir au moins 6 caractères.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Veuillez entrer une adresse email valide.';
        } else {
            $hash = password_hash($motdepasse, PASSWORD_DEFAULT);
            try {
                $stmt = $pdo->prepare('INSERT INTO Utilisateur (nom_utilisateur, motdepasse, email, role) VALUES (?, ?, ?, "utilisateur")');
                $stmt->execute([$nom_utilisateur, $hash, $email]);
                $message = 'Inscription réussie. Vous pouvez vous connecter.';
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $message = 'Nom d\'utilisateur ou email déjà utilisé.';
                } else {
                    $message = 'Erreur lors de l\'inscription.';
                }
            }
        }
    } else {
        $message = 'Veuillez remplir tous les champs et vérifier les mots de passe.';
    }
}
?>


<?php include __DIR__.'/includes/footer.php'; ?> 