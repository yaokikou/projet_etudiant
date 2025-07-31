<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/PublicationModel.php';

class PublicationsController extends BaseController {
    
    public function index() {
        // Récupérer les publications
        $publicationModel = new PublicationModel($this->pdo);
        $publications = $publicationModel->getActivePublications();
        
        // Définir les variables pour la vue
        $this->title = 'TECHNOVAServices - Publications';
        $this->css = '/site-informatique/assets/css/publications.css';
        
        // Rendre la vue avec les données
        $this->render('publications', [
            'publications' => $publications
        ]);
    }
}
?> 