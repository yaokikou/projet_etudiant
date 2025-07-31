<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/ContactModel.php';

class ContactController extends BaseController {
    
    public function index() {
        $message = '';
        $nom = '';
        $email = '';
        
        // Si l'utilisateur est connecté, récupérer ses informations
        if (isset($_SESSION['user_id'])) {
            $stmt = $this->pdo->prepare('SELECT nom_utilisateur, email FROM Utilisateur WHERE id = ?');
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            if ($user) {
                $nom = $user['nom_utilisateur'];
                $email = $user['email'];
            }
        }
        
        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $msg = trim($_POST['message'] ?? '');
            
            if ($nom && $email && $msg) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $contactModel = new ContactModel($this->pdo);
                    if ($contactModel->createContact($nom, $email, $msg)) {
                        $message = 'Votre message a bien été envoyé. Nous vous répondrons dans les plus brefs délais.';
                        $nom = '';
                        $email = '';
                    } else {
                        $message = 'Erreur lors de l\'envoi du message.';
                    }
                } else {
                    $message = 'Veuillez saisir une adresse email valide.';
                }
            } else {
                $message = 'Veuillez remplir tous les champs.';
            }
        }
        
        // Définir les variables pour la vue
        $this->title = 'TECHNOVAServices - Contact';
        $this->css = '/site-informatique/assets/css/contact.css';
        
        // Rendre la vue avec les données
        $this->render('contact', [
            'message' => $message,
            'nom' => $nom,
            'email' => $email
        ]);
    }
}
?> 