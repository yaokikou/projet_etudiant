<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/ServiceModel.php';

class ServicesController extends BaseController {
    
    public function index() {
        // Récupérer tous les services
        $serviceModel = new ServiceModel($this->pdo);
        $services = $serviceModel->getActiveServices();
        
        // Définir les variables pour la vue
        $this->title = 'TECHNOVAServices - Nos Services';
        $this->css = '/site-informatique/assets/css/services.css';
        
        // Rendre la vue avec les données
        $this->render('services', [
            'services' => $services
        ]);
    }
}
?> 