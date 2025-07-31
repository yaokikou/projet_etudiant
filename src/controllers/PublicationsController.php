<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/PublicationModel.php';

class PublicationsController extends BaseController {
    
    public function index() {
        // Récupération de la liste des publications actives depuis le modèle

        $publicationModel = new PublicationModel($this->pdo);
        $publications = $publicationModel->getActivePublications();
        
        // Définir les variables pour la vue de la page des publications

        $this->title = 'TECHNOVAServices - Publications';
        $this->css = '/site-informatique/assets/css/publications.css';
        
        // Rendre les données à la vue

        $this->render('publications', [
            'publications' => $publications
        ]);
    }
}
?> 