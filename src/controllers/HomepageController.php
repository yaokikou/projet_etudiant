<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/ServiceModel.php';

class HomepageController extends BaseController {
    
    public function index() {
        // Récupérer les services actifs depuis le modèle

        $serviceModel = new ServiceModel($this->pdo);
        $services = $serviceModel->getActiveServices();
        
        // Définir les variables pour la vue de la page d'accueil

        $this->title = 'TECHNOVAServices - Accueil';
        $this->css = '/site-informatique/assets/css/home.css';
        
        // Rendre les données à la vue

        $this->render('homepage', [
            'services' => $services
        ]);
    }
}
?> 