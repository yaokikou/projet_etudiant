<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/DemandeServiceModel.php';

class MesDemandesController extends BaseController {
    
    public function index() {
        $this->requireLogin();
        
        // Récupérer les demandes de l'utilisateur
        $demandeModel = new DemandeServiceModel($this->pdo);
        $demandes = $demandeModel->getUserDemandes($_SESSION['user_id']);
        
        // Définir les variables pour la vue
        $this->title = 'TECHNOVAServices - Mes Demandes';
        $this->css = '/site-informatique/assets/css/demande_user.css';
        
        // Rendre la vue avec les données
        $this->render('mes_demandes', [
            'demandes' => $demandes
        ]);
    }
}
?> 