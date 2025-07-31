<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/UserModel.php';

class ConnexionController extends BaseController {
    
    public function index() {
        $message = '';
        
        // Traitement des messages de statut lors du remplissage du formulaire de connexion
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
        
        // Traitement du formulaire de connexion

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom_utilisateur = trim($_POST['nom_utilisateur'] ?? '');
            $motdepasse = $_POST['motdepasse'] ?? '';
            
            if ($nom_utilisateur && $motdepasse) {
                $userModel = new UserModel($this->pdo);
                $user = $userModel->authenticate($nom_utilisateur, $motdepasse);
                
                if ($user) {
                    if (!$user['actif']) {
                        $message = 'Votre compte a été désactivé. Contactez l\'administrateur.';
                    } else {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['nom_utilisateur'] = $user['nom_utilisateur'];
                        $_SESSION['role'] = $user['role'];
                        
                        // Gestion de la redirection selon le rôle de l'utilisateur

                        if ($user['role'] === 'admin' || $user['role'] === 'moderateur') {
                            $this->redirect('admin/');
                        } else {
                            $this->redirect('index.php');
                        }
                    }
                } else {
                    $message = 'Identifiants invalides.';
                }
            } else {
                $message = 'Veuillez remplir tous les champs.';
            }
        }
        
        // Définir les variables pour la vue pour la page de connexion

        $this->title = 'TECHNOVAServices - Connexion';
        $this->css = '/site-informatique/assets/css/connexion.css';
        
        // Rendre les données à la vue
        
        $this->render('connexion', [
            'message' => $message
        ]);
    }
}
?> 