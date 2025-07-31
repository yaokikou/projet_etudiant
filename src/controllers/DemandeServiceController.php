<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/ServiceModel.php';
require_once __DIR__ . '/../model/DemandeServiceModel.php';

class DemandeServiceController extends BaseController {
    
    public function index() {
        $this->requireLogin();
        
        $message = '';
        
        // Récupération de la liste des services actifs depuis le modèle
        
        $serviceModel = new ServiceModel($this->pdo);
        $services = $serviceModel->getActiveServices();
        
        // Traitement du formulaire de demande de service

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $service_id = $_POST['service_id'] ?? '';
            $description = trim($_POST['description'] ?? '');
            
            if ($service_id && $description) {
                $demandeModel = new DemandeServiceModel($this->pdo);
                if ($demandeModel->createDemande($_SESSION['user_id'], $service_id, $description)) {
                    $message = 'Votre demande de service a été envoyée avec succès.';
                } else {
                    $message = 'Erreur lors de l\'envoi de la demande.';
                }
            } else {
                $message = 'Veuillez remplir tous les champs.';
            }
        }
        
        // Définir les variables pour la vue de la page de demande de service

        $this->title = 'TECHNOVAServices - Demande de Service';
        $this->css = '/site-informatique/assets/css/demande_user.css';
        
        // Rendre les données à la vue

        $this->render('demande_service', [
            'services' => $services,
            'message' => $message
        ]);
    }
}
?> 