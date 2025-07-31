<?php
/**
 * Model pour la gestion des demandes de service
 */
class DemandeServiceModel {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Créer une nouvelle demande de service
     */
    public function createDemande($utilisateur_id, $service_id, $description) {
        if (!$this->pdo) {
            return false;
        }
        
        try {
            $stmt = $this->pdo->prepare('INSERT INTO DemandeService (utilisateur_id, service_id, description) VALUES (?, ?, ?)');
            return $stmt->execute([$utilisateur_id, $service_id, $description]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Récupérer les demandes d'un utilisateur
     */
    public function getUserDemandes($utilisateur_id) {
        if (!$this->pdo) {
            return [];
        }
        
        try {
            $stmt = $this->pdo->prepare('
                SELECT ds.*, s.nom_service 
                FROM DemandeService ds 
                JOIN Service s ON ds.service_id = s.id 
                WHERE ds.utilisateur_id = ? 
                ORDER BY ds.date_demande DESC
            ');
            $stmt->execute([$utilisateur_id]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Récupérer toutes les demandes (pour admin)
     */
    public function getAllDemandes() {
        if (!$this->pdo) {
            return [];
        }
        
        try {
            $stmt = $this->pdo->prepare('
                SELECT ds.*, s.nom_service, u.nom_utilisateur 
                FROM DemandeService ds 
                JOIN Service s ON ds.service_id = s.id 
                JOIN Utilisateur u ON ds.utilisateur_id = u.id 
                ORDER BY ds.date_demande DESC
            ');
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
}
?> 