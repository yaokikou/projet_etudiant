<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/ServiceModel.php';

class ServicesController extends BaseController {
    
    public function index() {
        // Récupération de la liste des services actifs depuis le modèle

        $serviceModel = new ServiceModel($this->pdo);
        $services = $serviceModel->getActiveServices();
        
        // Définir les variables pour la vue de la page des services

        $this->title = 'TECHNOVAServices - Nos Services';
        $this->css = '/site-informatique/assets/css/services.css';
        
        // Rendre les données à la vue

        $this->render('services', [
            'services' => $services
        ]);
    }
}
?> 