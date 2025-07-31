<?php
require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/auth.php';
session_start();
include __DIR__.'/includes/header.php';

$message = '';
if (isset($_GET['message'])) {
    switch ($_GET['message']) {
        case 'connectez-vous':
            $message = "Vous devez être inscrit et connecté pour accéder à cette page.";
            break;
        case 'acces-refuse':
            $message = "Accès refusé. Vous n'avez pas les permissions nécessaires.";
            break;
        case 'deconnexion':
            $message = "Vous avez été déconnecté avec succès.";
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_utilisateur = trim($_POST['nom_utilisateur'] ?? '');
    $motdepasse = $_POST['motdepasse'] ?? '';
    
    if ($nom_utilisateur && $motdepasse) {
        $stmt = $pdo->prepare('SELECT id, nom_utilisateur, motdepasse, role, actif FROM Utilisateur WHERE nom_utilisateur = ?');
        $stmt->execute([$nom_utilisateur]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($motdepasse, $user['motdepasse'])) {
            if (!$user['actif']) {
                $message = 'Votre compte a été désactivé. Contactez l\'administrateur.';
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nom_utilisateur'] = $user['nom_utilisateur'];
                $_SESSION['role'] = $user['role'];
                
                // Redirection selon le rôle
                $redirect = getRedirectAfterLogin($user);
                header('Location: ' . $redirect);
                exit;
            }
        } else {
            $message = 'Identifiants invalides.';
        }
    } else {
        $message = 'Veuillez remplir tous les champs.';
    }
}
?>


<?php include __DIR__.'/includes/footer.php'; ?> 