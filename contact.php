<?php
require_once __DIR__.'/includes/db.php';
session_start();
include __DIR__.'/includes/header.php';

$message = '';
$nom = '';
$email = '';

// Si l'utilisateur est connecté, récupérer ses informations
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare('SELECT nom_utilisateur, email FROM Utilisateur WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if ($user) {
        $nom = $user['nom_utilisateur'];
        $email = $user['email'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $msg = trim($_POST['message'] ?? '');
    
    if ($nom && $email && $msg) {
        // Validation basique de l'email
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $stmt = $pdo->prepare('INSERT INTO Contact (nom, email, message) VALUES (?, ?, ?)');
            $stmt->execute([$nom, $email, $msg]);
            $message = 'Votre message a bien été envoyé. Nous vous répondrons dans les plus brefs délais.';
            
            // Vider les champs après envoi réussi
            $nom = '';
            $email = '';
        } else {
            $message = 'Veuillez saisir une adresse email valide.';
        }
    } else {
        $message = 'Veuillez remplir tous les champs.';
    }
}
?>


<?php include __DIR__.'/includes/footer.php'; ?> 