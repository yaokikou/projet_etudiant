<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/ServiceModel.php';
require_once __DIR__ . '/../model/DemandeServiceModel.php';

class DemandeServiceController extends BaseController {
    
    public function index() {
        $this->requireLogin();
        
        $message = '';
        
        // Récupérer les services disponibles
        $serviceModel = new ServiceModel($this->pdo);
        $services = $serviceModel->getActiveServices();
        
        // Traitement du formulaire
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
        
        // Définir les variables pour la vue
        $this->title = 'TECHNOVAServices - Demande de Service';
        $this->css = '/site-informatique/assets/css/demande_user.css';
        
        // Rendre la vue avec les données
        $this->render('demande_service', [
            'services' => $services,
            'message' => $message
        ]);
    }
}
?> 