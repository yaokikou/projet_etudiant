<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../model/DemandeServiceModel.php';

class MesDemandesController extends BaseController {
    
    public function index() {
        $this->requireLogin();
        
        // Récupération de la liste des demandes de service de l'utilisateur connecté depuis le modèle

        $demandeModel = new DemandeServiceModel($this->pdo);
        $demandes = $demandeModel->getUserDemandes($_SESSION['user_id']);
        $has_demandes = $demandeModel->userHasDemandes($_SESSION['user_id']); // à créer ci-dessous

        // Définir les variables pour la vue de la page des demandes

        $this->title = 'TECHNOVAServices - Mes Demandes';
      
        
        // Rendre les données à la vue
        
        $this->render('mes_demandes', [
            'demandes' => $demandes,
             'has_demandes' => count($demandes) > 0
        ]);
    }
}
?> 