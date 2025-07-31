<?php
// Model pour la gestion des services

class ServiceModel {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    //Fonction pour récupérer tous les services actifs

    public function getActiveServices() {
        if (!$this->pdo) {
            return [];
        }
        
        try {
            $stmt = $this->pdo->prepare('SELECT id, nom_service, description, image FROM Service WHERE actif = 1 ORDER BY date_ajout DESC');
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    //Ensuite, on peut ajouter une fonction pour récupérer un service par son ID
    
    public function getServiceById($id) {
        if (!$this->pdo) {
            return null;
        }
        
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM Service WHERE id = ? AND actif = 1');
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }
}
?> 