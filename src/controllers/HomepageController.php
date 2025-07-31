<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/ServiceModel.php';

class HomepageController extends BaseController {
    
    public function index() {
        // Récupérer les services
        $serviceModel = new ServiceModel($this->pdo);
        $services = $serviceModel->getActiveServices();
        
        // Définir les variables pour la vue
        $this->title = 'TECHNOVAServices - Accueil';
        $this->css = '/site-informatique/assets/css/home.css';
        
        // Rendre la vue avec les données
        $this->render('homepage', [
            'services' => $services
        ]);
    }
}
?> 