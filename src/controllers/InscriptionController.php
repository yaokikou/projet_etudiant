<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/UserModel.php';

class InscriptionController extends BaseController {
    
    public function index() {
        $message = '';
        
        // Traitement du formulaire d'inscription
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom_utilisateur = trim($_POST['nom_utilisateur'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $motdepasse = $_POST['motdepasse'] ?? '';
            $confirmation = $_POST['confirmation'] ?? '';
            
            if ($nom_utilisateur && $email && $motdepasse && $confirmation) {
                if ($motdepasse === $confirmation) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $userModel = new UserModel($this->pdo);
                        
                        if ($userModel->usernameExists($nom_utilisateur)) {
                            $message = 'Ce nom d\'utilisateur existe déjà.';
                        } elseif ($userModel->emailExists($email)) {
                            $message = 'Cette adresse email existe déjà.';
                        } else {
                            if ($userModel->createUser($nom_utilisateur, $email, $motdepasse)) {
                                $message = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
                            } else {
                                $message = 'Erreur lors de l\'inscription.';
                            }
                        }
                    } else {
                        $message = 'Veuillez saisir une adresse email valide.';
                    }
                } else {
                    $message = 'Les mots de passe ne correspondent pas.';
                }
            } else {
                $message = 'Veuillez remplir tous les champs.';
            }
        }
        
        // Définir les variables pour la vue
        $this->title = 'TECHNOVAServices - Inscription';
        $this->css = '/site-informatique/assets/css/inscription.css';
        
        // Rendre la vue avec les données
        $this->render('inscription', [
            'message' => $message
        ]);
    }
}
?> 